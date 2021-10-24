<?php

namespace App\Repository;

use App\Entity\Assignement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Assignement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assignement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assignement[]    findAll()
 * @method Assignement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssignementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignement::class);
    }

    // /**
    //  * @return Assignement[] Returns an array of Assignement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Assignement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
