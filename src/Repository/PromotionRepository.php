<?php

namespace App\Repository;

use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
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