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
        $qb->join('c.type_contrat', 'type_contrat');
        if ($search && $search->getTypeContrat()){
            //filter by type contrat
            $qb->andWhere($qb->expr()->eq("type_contrat", ':type_contrat'))
                ->setParameter('type_contrat', $search->getTypeContrat() )
            ;
        }
        return $qb->getQuery()
            ->getResult()
            ;
    }





    // /**
    //  * @return Contrat[] Returns an array of Contrat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
