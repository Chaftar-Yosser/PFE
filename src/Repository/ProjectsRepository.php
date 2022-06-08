<?php

namespace App\Repository;

use App\Entity\Projects;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Projects|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projects|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projects[]    findAll()
 * @method Projects[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projects::class);
    }


    public function getProjectsCount()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('count(p)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getProjectsByUser($user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('count(p)')
            ->join("p.users", "users")
            ->andWhere($qb->expr()->eq("users", ":user"))
            ->setParameter("user", $user)
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    /*
    public function findOneBySomeField($value): ?Projects
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
