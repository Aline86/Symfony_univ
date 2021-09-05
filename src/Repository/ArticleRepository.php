<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function myFindAllWithPaging($currentPage, $nbPerPage){
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.created_at', 'DESC')
            ->leftJoin('a.categories', 'c')
            ->addOrderBy('c.name', 'ASC')
            ->andWhere('a.published = true')
            ->getQuery()
            ->setFirstResult(($currentPage - 1) * $nbPerPage )
            ->setMaxResults($nbPerPage);
        return new Paginator($query);
    }

    public function findLastArticle($nbPage){
        return $query = $this->createQueryBuilder('v')
            ->orderBy('v.created_at', 'DESC')
            ->andWhere('v.published = true')
            ->setMaxResults( $nbPage )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return Article[] Returns an array of Article objects
     */
    public function findArticleFromCate($id)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.categories', 'c')
            ->where('c.id=:id')->setParameter('id', $id)
            ->addSelect('c')
            ->andWhere('a.published = true')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Article
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
