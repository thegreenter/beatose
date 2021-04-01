<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CpeDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CpeDocument>
 * @method CpeDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method CpeDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method CpeDocument[]    findAll()
 * @method CpeDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CpeDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CpeDocument::class);
    }

    public function findOneByName(?string $name): ?CpeDocument
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :val')
            ->setParameter('val', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByTicket(?string $ticket): ?CpeDocument
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.ticket = :val')
            ->setParameter('val', $ticket)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
