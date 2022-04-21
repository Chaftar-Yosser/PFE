<?php

namespace App\Repository;

use App\Entity\SuiviLeave;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SuiviLeave|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiviLeave|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiviLeave[]    findAll()
 * @method SuiviLeave[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviLeaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviLeave::class);
    }


    public function findLastByUser($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?SuiviCongs
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
