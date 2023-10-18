<?php

namespace App\Controller;

use App\Service\SearchService;
use App\Form\SearchFormType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function homeIndex(
        ProductRepository $productRepository,
        Security $security,
        EntityManagerInterface $entityManager,
        Request $request,
        SearchService $searchService
    ): Response {
        $mostLikedProducts = $this->mostLikedProducts($entityManager);
        $promotionDataArray = $this->productsWithPromotion($entityManager);
        $isUserConnected = false;
        $roleUser = '';
        
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }
        
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $searchResults = [];
    
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchQuery = $searchForm->get('search')->getData();
            $searchResults = $searchService->searchProducts($searchQuery);
        }

        $produ = $productRepository->findAll();

        $myDataHome = [];
        foreach ($produ as $prod) {
            $myDataHome[] = [
                "productId" => $prod->getId(),
                "productTitle" => $prod->getTitle(),
                "productPrice" => $prod->getPrice(),
                "productImage" => $prod->getImage(),
                "productDescription" => $prod->getDescription(),
                "productPromotion" => $prod->getPromotion()
             ];
        }

        return $this->render('home/index.html.twig', [
            'myDataHome' => $myDataHome,
            'isUserConnected' => $isUserConnected,
            'roleUser' => $roleUser,
            'mostLikedProducts' => $mostLikedProducts,
            'productsWithPromotion' => $promotionDataArray,
            'searchForm' => $searchForm->createView(),
            'searchResults' => $searchResults,
        ]);
    }
    
    #[Route('/panier', name: 'panier')]
    public function panier(){
        return $this->render('home/panier.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(){
        return $this->render('home/contact.html.twig');
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, SearchService $searchService, ProductRepository $productRepository, Security $security, EntityManagerInterface $entityManager)
    {
        $mostLikedProducts = $this->mostLikedProducts($entityManager);
        $productsWithPromotion = $this->productsWithPromotion($entityManager);
        $isUserConnected = false;
        $roleUser = '';

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);
        $searchResults = [];
    
        if ($form->isSubmitted() && $form->isValid()) {
            $searchQuery = $form->get('search')->getData();
            $searchResults = $searchService->searchProducts($searchQuery);
        }
    
        $produ = $productRepository->findAll();

        $myDataHome = [];
        foreach ($produ as $prod) {
            $myDataHome[] = [
                "productId" => $prod->getId(),
                "productTitle" => $prod->getTitle(),
                "productPrice" => $prod->getPrice(),
                "productImage" => $prod->getImage(),
                "productDescription" => $prod->getDescription(),
                "productPromotion" => $prod->getPromotion()
             ];
        }

        return $this->render('home/index.html.twig', [
            'myDataHome' => $myDataHome,
            'isUserConnected' => $isUserConnected,
            'roleUser' => $roleUser,
            'mostLikedProducts' => $mostLikedProducts,
            'productsWithPromotion' => $productsWithPromotion,
            'searchForm' => $form->createView(),
            'searchResults' => $searchResults,
        ]);
    }
    
    #[Route('/most_liked_products', name: 'most_liked_products', methods: ['POST'])]
    public function mostLikedProducts(EntityManagerInterface $entityManager): array
    {
        $query = $entityManager->createQueryBuilder()
            ->select('p, COUNT(l.id) as likeCount')
            ->from('App\Entity\Product', 'p')
            ->leftJoin('p.liked', 'l')
            ->groupBy('p')
            ->orderBy('likeCount', 'DESC')
            ->getQuery();
    
        $mostLikedProducts = $query->getResult();
    
        $productDataArray = [];
        foreach ($mostLikedProducts as $productData) {
            $likeCount = $productData['likeCount'];
    
            if ($likeCount > 0) {
                $product = $productData[0];
                $productDataArray[] = [
                    "productId" => $product->getId(),
                    "productTitle" => $product->getTitle(),
                    "productPrice" => $product->getPrice(),
                    "productImage" => $product->getImage(),
                    "productPromotion" => $product->getPromotion(),
                    "productDescription" => $product->getDescription(),
                    "likeCount" => $likeCount,
                ];
            }
        }
        return $productDataArray;
    }
    
    #[Route('/products_with_promotion', name: 'products_with_promotion', methods: ['POST'])]
    public function productsWithPromotion(EntityManagerInterface $entityManager): array
    {
        $query = $entityManager->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Product', 'p')
            ->where('p.promotion IS NOT NULL')
            ->getQuery();

        $productsWithPromotion = $query->getResult();

        $promotionDataArray = [];
        foreach ($productsWithPromotion as $product) {
            $promotionDataArray[] = [
                "productId" => $product->getId(),
                "productTitle" => $product->getTitle(),
                "productPrice" => $product->getPrice(),
                "productImage" => $product->getImage(),
                "productPromotion" => $product->getPromotion(),
                "productDescription" => $product->getDescription(),
            ];
        }
        return $promotionDataArray;
    }
}