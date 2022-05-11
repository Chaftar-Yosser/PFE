<?php

namespace App\Repository;

use App\Entity\Projects;
use App\Entity\Quiz;
use App\Entity\Search;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function getUser(Search $search = null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select("u");
        if ($search && $search->getRole()){
            //filter by role user
            $qb->andWhere($qb->expr()->like("u.role",":userRole"))
                ->setParameter(
                    "userRole"  , '%"'.$search->getRole().'"%');
            ;
        }
        return $qb->getQuery()
            ->getResult()
            ;
    }


    // get user ili 3andou même skills avec le projet
    public function getUsersByProjectSkills(Projects $project)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select("u")
            ->innerJoin("u.skills", 's')
            ->andWhere($qb->expr()->in("s", ":skills"))
            ->setParameter("skills"  , $project->getSkills());

        return $qb->getQuery()->getResult();
    }



    // get user ili 3andou même skills avec le quiz
    public function getUsersByQuizSkills(Quiz $quiz)
    {
//        $skillsQuestion = [];
//        foreach ($quiz->getQuestions() as $question ){
//            $skillsQuestion = $question->getSkills();
//            // kii ken aandich relation entre skills et quiz
//        }

        $qb = $this->createQueryBuilder('u');
        $qb->select("u")
            ->innerJoin("u.skills", 's')
            ->andWhere($qb->expr()->in("s", ":skills"))
            ->setParameter("skills"  , $quiz->getSkills());

        return $qb->getQuery()->getResult();
    }


    public function getUsersByProject(Projects $project)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select("u")
            ->Join("u.projects", 'p')
            ->andWhere($qb->expr()->eq("projects", ':projects'))
            ->setParameter("projects"  , $project->getUsers());

        return $qb->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
