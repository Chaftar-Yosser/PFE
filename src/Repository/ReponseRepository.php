<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Reponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reponse[]    findAll()
 * @method Reponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }


    public function findByQuestion($question)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.question = :q')
            ->setParameter('q', $question)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCorrectAnswer(Question $question){
        $qb = $this->createQueryBuilder('r');
        $qb->andWhere($qb->expr()->eq("r.question", ":question"))
            ->andWhere($qb->expr()->eq("r.isCorrect", ":isCorrect"))
            ->setParameter("question", $question)
            ->setParameter("isCorrect", true);
        return $qb->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Reponse
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
