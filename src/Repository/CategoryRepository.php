<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findCateFromArticle($id)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.articles', 'c')
            ->where('c.id=:id')->setParameter('id', $id)
            ->addSelect('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $id
     * @return Article[] Returns an array of Article objects
     */
    public function findArticleFromCate($id)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.articles', 'c')
            ->where('a.id=:id')->setParameter('id', $id)
            ->addSelect('a')
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
