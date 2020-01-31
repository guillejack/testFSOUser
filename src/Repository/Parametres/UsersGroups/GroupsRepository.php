<?php

namespace App\Repository\Parametres\UsersGroups;

use App\Entity\Parametres\UsersGroups\Groups;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Groups|null find($id, $lockMode = null, $lockVersion = null)
 * @method Groups|null findOneBy(array $criteria, array $orderBy = null)
 * @method Groups[]    findAll()
 * @method Groups[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groups::class);
    }

    // /**
    //  * @return Groups[] Returns an array of Groups objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Groups
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
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
            $qb->andWhere('a.name like :search or a.description like :search ')
            ->setParameter('search', "%".$filter['search']."%"); 
         }
          
        $qb->orderBy('a.name', 'ASC');

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
