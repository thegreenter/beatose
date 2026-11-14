<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\CpeDocument;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

/**
 * Acceso a la tabla cpe_document con Doctrine DBAL (sin ORM).
 */
class CpeDocumentRepository
{
    private const TABLE = 'cpe_document';

    private const TYPES = [
        'id' => Types::INTEGER,
        'name' => Types::STRING,
        'state_code' => Types::STRING,
        'hash_cpe' => Types::STRING,
        'hash_cdr' => Types::STRING,
        'ticket' => Types::STRING,
        'create_date' => Types::DATETIME_MUTABLE,
    ];

    public function __construct(private Connection $connection)
    {
    }

    public function findOneByName(?string $name): ?CpeDocument
    {
        return $this->findOneBy('name', $name);
    }

    public function findOneByTicket(?string $ticket): ?CpeDocument
    {
        return $this->findOneBy('ticket', $ticket);
    }

    /**
     * Inserta el documento si no tiene id; si lo tiene, lo actualiza.
     */
    public function save(CpeDocument $document): void
    {
        $data = [
            'name' => $document->getName(),
            'state_code' => $document->getStateCode(),
            'hash_cpe' => $document->getHashCpe(),
            'hash_cdr' => $document->getHashCdr(),
            'ticket' => $document->getTicket(),
            'create_date' => $document->getCreateDate() ?? new DateTime(),
        ];

        if ($document->getId() === null) {
            $this->connection->insert(self::TABLE, $data, self::TYPES);
            $document->setId((int) $this->connection->lastInsertId());

            return;
        }

        $this->connection->update(self::TABLE, $data, ['id' => $document->getId()], self::TYPES);
    }

    private function findOneBy(string $column, ?string $value): ?CpeDocument
    {
        if ($value === null) {
            return null;
        }

        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where($column.' = :value')
            ->setParameter('value', $value)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return $row === false ? null : $this->hydrate($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): CpeDocument
    {
        /** @var DateTime|null $createDate */
        $createDate = $this->connection->convertToPHPValue($row['create_date'], Types::DATETIME_MUTABLE);

        return (new CpeDocument())
            ->setId((int) $row['id'])
            ->setName((string) $row['name'])
            ->setStateCode($row['state_code'])
            ->setHashCpe($row['hash_cpe'])
            ->setHashCdr($row['hash_cdr'])
            ->setTicket($row['ticket'])
            ->setCreateDate($createDate);
    }
}
