<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Promotion;

/**
 * @extends ServiceEntityRepository<Promotion>
 *
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

 class PromotionRepository extends ServiceEntityRepository
 {
    public function __construct(ManagerRegistry $registry)
     {
         parent::__construct($registry, Promotion::class);
     }

    public function remove(Promotion $entity, bool $flush = false): void
     {
         $this->getEntityManager()->remove($entity);
     
         if ($flush) {
             $this->getEntityManager()->flush();
         }
     }

    public function findActivePromotions()
     {
        $now = new \DateTime();
    
        return $this->createQueryBuilder('p')
            ->where('p.startDate <= :now')
            ->andWhere('p.endDate >= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
     }

    public function findPromotionsForProduct($productId)
     {
     return $this->createQueryBuilder('p')
        ->join('p.product', 'product')
        ->where('product.id = :productId')
        ->setParameter('productId', $productId)
        ->getQuery()
        ->getResult();
     }
}