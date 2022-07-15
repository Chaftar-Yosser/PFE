<?php

namespace App\Repository;

use App\Entity\Contrat;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrat[]    findAll()
 * @method Contrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }


    public function getContrats(Search $search = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select("c")
            ->join('c.user', 'user');
        if ($search && $search->getUser()){
            //filter by user
            $qb->andWhere($qb->expr()->eq("user", ':user'))
                ->setParameter('user', $search->getUser() )
            ;
        }
        if ($search && $search->getTypeContrat()){
            $qb->join('c.type_contrat', 'type_contrat');
            //filter by type contrat
            $qb->andWhere($qb->expr()->eq("type_contrat", ':type_contrat'))
                ->setParameter('type_contrat', $search->getTypeContrat() )
            ;
        }
        return $qb->getQuery()
            ->getResult()
            ;
    }

    // get last contrat
    public function getUserContrat($user)
    {
        $qb = $this->createQueryBuilder('c');

            $qb->andWhere($qb->expr()->gte('c.date_fin', ':now'))
                ->setParameter("now",new \DateTime());
            $qb->andWhere($qb->expr()->eq("c.status", ":status"))
                ->setParameter("status", Contrat::STATUS_EN_COURS);
        if ($user){
            $qb->andWhere($qb->expr()->eq("c.user", ":user"))
                ->setParameter("user", $user);
        }
        $qb->orderBy("c.date_fin", "ASC");
        $result = $qb->getQuery()->getResult();
        return  $result ? $result[0] : null;
    }



    /*
    public function findOneBySomeField($value): ?Contrat
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
