<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
    
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }    

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
    
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function findByTitle($searchQuery)
    {
        return $this->createQueryBuilder('p')
            ->where('p.title LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();
    }
}
