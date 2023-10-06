<?php

namespace App\Service;

use App\Repository\ProductRepository;

class SearchService
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function searchProducts($searchQuery)
    {
        return $this->productRepository->findByTitle($searchQuery);
    }
}