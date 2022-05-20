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

    public function getQuizByUser($user)
    {
        $qb = $this->createQueryBuilder('quiz');
        $qb->select("quiz")
            ->join('quiz.users', 'users');
        $qb->andWhere($qb->expr()->eq("users", ":user"))
            ->setParameter("user", $user);

        return $qb->getQuery()->getResult();
    }
}
