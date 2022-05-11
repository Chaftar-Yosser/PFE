<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

//    public function getQuiz(Search $search = null)
//    {
//        $qb = $this->createQueryBuilder('q');
//        $qb->select("q")
//            ->join('q.user', 'u');
//        if ($search && $search->getSkills()){
//            //filter by user
//            $qb->andWhere($qb->expr()->eq("u", ':user'))
//                ->setParameter('user', $search->getUser() )
//            ;
//        }
//        return $qb->getQuery()->getResult();
//    }

    /*
    public function findOneBySomeField($value): ?Quiz
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
