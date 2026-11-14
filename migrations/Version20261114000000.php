<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * getStatus busca por ticket en cada consulta (sondeo de resúmenes asíncronos).
 */
final class Version20261114000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Índice por ticket en cpe_document';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_CPE_DOCUMENT_TICKET ON cpe_document (ticket)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_CPE_DOCUMENT_TICKET');
    }
}
