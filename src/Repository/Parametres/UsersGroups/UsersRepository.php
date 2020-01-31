<?php

namespace App\Repository\Parametres\UsersGroups;

use App\Entity\Parametres\UsersGroups\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    // /**
    //  * @return Users[] Returns an array of Users objects
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
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function search($filter, $page = 0, $max = NULL, $getResult = true)
    {
        $qb = $this->createQueryBuilder('a')
              ->select('a');
                  
         if (isset($filter['search']) && $filter['search'] )
         {
            $qb->andWhere('a.nom like :search or a.prenom like :search ')
            ->setParameter('search', "%".$filter['search']."%"); 
         }
          
        $qb->orderBy('a.nom', 'ASC');

        if ($max) {
            $qb ->setMaxResults($max)
                ->setFirstResult($page * $max)
            ;
            //$this->getQuery();
        }

        return $qb->getQuery()
        ->getResult();

    }
}
