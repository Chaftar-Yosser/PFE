<?php

namespace App\Repository;

use App\Entity\Leave;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Leave|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leave|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leave[]    findAll()
 * @method Leave[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Leave::class);
    }

    public function getLeaveByDateAndUser($user)
    {
        $qb = $this->createQueryBuilder('l')
            ->select("l")
            ->andWhere('l.endDate >= :now')
            ->setParameter("now",new \DateTime());
        if ($user){
            $qb->join("l.userFrom", "userFrom");
            $qb->andWhere($qb->expr()->eq("userFrom", ":userFrom"))
                ->setParameter("userFrom", $user);
        }
        return  $qb->getQuery()->getResult();
    }

    public function getLeave(Search $search = null)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select("l")
            ->join('l.Leave_type', 'Leave_type');
        if ($search && $search->getLeaveType()){
            //filter by type contrat
            $qb->andWhere($qb->expr()->eq("Leave_type", ':Leave_type'))
                ->setParameter('Leave_type', $search->getLeaveType() )
            ;
        }
        $qb->join('l.userFrom', 'userFrom');
        if ($search && $search->getUserFrom()){
            //filter by user
            $qb->andWhere($qb->expr()->eq("userFrom", ':userFrom'))
                ->setParameter('userFrom', $search->getUserFrom() )
            ;
        }
        return $qb->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Leave[] Returns an array of Leave objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Leave
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
