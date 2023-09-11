<?php

namespace App\Controller;

use App\Controller\ProductController;
use App\Repository\ProductRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function homeIndex(
        ProductRepository $productRepository,
        ProductController $productController,
        Security $security
    ): Response {

        $isUserConnected = false;
        $roleUser = '';
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }

        // Récupération de toutes les catégories de la table
        $request = $productController->allProducts($productRepository);
        $response = $request->getContent();
        $myData = json_decode($response);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController', 'myData' => $myData, 'isUserConnected' => $isUserConnected, 'roleUser' => $roleUser
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


    #[Route('/', name: 'home')]
    public function admin_index(ProductRepository $productRepository, Security $security): Response
    {
        $isUserConnected = false;
        $roleUser = '';
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }
        $produ = $productRepository->findAll();

        $myData = [];
        foreach ($produ as $prod) {
            $myData[] = [
                "productId" => $prod->getId(),
                "productTitle" => $prod->getTitle(),
                "productPrice" => $prod->getPrice(),
                "productImage" => $prod->getImage(),
                "productDescription" => $prod->getDescription(),
            ];
        }

        // $topCarts = $cartRepository->mostCarts();

        return $this->render('/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'myData' => $myData,
            'isUserConnected' => $isUserConnected, 'roleUser' => $roleUser
        ]);
    }
}