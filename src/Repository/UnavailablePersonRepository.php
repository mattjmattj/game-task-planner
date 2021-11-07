<?php

namespace App\Repository;

use App\Entity\UnavailablePerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UnavailablePerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnavailablePerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnavailablePerson[]    findAll()
 * @method UnavailablePerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnavailablePersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnavailablePerson::class);
    }

    // /**
    //  * @return UnavailablePerson[] Returns an array of UnavailablePerson objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UnavailablePerson
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
