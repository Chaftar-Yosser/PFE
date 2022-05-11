<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

//$questions => where q.skills in $skills
    public function getQuestionBySkills(Quiz $quiz, $skills)
    {
        $qb = $this->createQueryBuilder('question');
        $qb->andWhere($qb->expr()->eq("question.skills", ":skills"))
            ->setParameter("skills", $skills)
            ->orderBy('RAND()')
            ->setMaxResults( $quiz->getNombrequestion());
        return $qb->getQuery()->getResult();
    }


    public function getQuestionByQuiz(Quiz $quiz)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->select("q")
            ->innerJoin("q.skills", 's')
            ->andWhere($qb->expr()->in("s", ":skills"))
            ->setParameter("skills", $quiz->getSkills())
            ->orderBy('RAND()')
            ->setMaxResults( $quiz->getNombrequestion());
        return $qb->getQuery()->getResult();
    }


    public function getQuestions(Search $search = null)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->select("q")
            ->join('q.skills', 's');
        if ($search && $search->getSkills()){
            //filter by skills
            $qb->andWhere($qb->expr()->eq("s", ':skills'))
                ->setParameter('skills', $search->getSkills() )
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
