<?php

namespace App\Repository;

use App\Entity\Tasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Tasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasks[]    findAll()
 * @method Tasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    public function getTasksByUserAndSprint($user, $sprint)
    {
        $qb = $this->createQueryBuilder("tasks");
        $qb->andWhere($qb->expr()->eq("tasks.sprint", ":sprint"))
            ->setParameter("sprint", $sprint);
        $qb->join("tasks.users", "users");
        $qb->andWhere($qb->expr()->eq("users", ":user"))
            ->setParameter("user", $user);
        return $qb->getQuery()->getResult();
    }


    public function getTasks()
    {
        return $this->createQueryBuilder('t')
            ->select("t")
            ->join('t.Projects', 'p')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Tasks
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
