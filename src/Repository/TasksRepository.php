<?php

namespace App\Repository;

use App\Entity\Search;
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


    public function getTasks(Search $search = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select("t")
            ->join('t.Projects', 'p');
        if ($search && $search->getProjects()){
            //filter by project
            $qb->andWhere($qb->expr()->eq("p", ':project'))
                ->setParameter('project', $search->getProjects() )
            ;
        }
        $qb->join('t.sprint', 'sprint');
        if ($search && $search->getSprint()){
            //filter by sprint
            $qb->andWhere($qb->expr()->eq("sprint", ':sprint'))
                ->setParameter('sprint', $search->getSprint() )
            ;
        }
        if ($search && $search->getStatus()){
            //filter by status
            $qb->andWhere($qb->expr()->eq("t.status", ':status'))
                ->setParameter('status', $search->getStatus() )
            ;
        }

        return $qb->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function getTaskByDate($user = null)
    {
         $qb = $this->createQueryBuilder('t')
            ->select("t")
            ->andWhere('t.date_fin >= :now')
            ->setParameter("now",new \DateTime());
         if ($user){
             $qb->join("t.users", "users");
             $qb->andWhere($qb->expr()->eq("users", ":user"))
                 ->setParameter("user", $user);
         }
        return  $qb->getQuery()->getResult();
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
