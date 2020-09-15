<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CpeDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    // /**
    //  * @return CpeDocument[] Returns an array of CpeDocument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CpeDocument
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
