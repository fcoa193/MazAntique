<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    // public function searchAction(Request $request)
    // {
    //     $searchForm = $this->createForm(SearchFormType::class);

    //     return $this->render('partials/header.html.twig', [
    //         'searchForm' => $searchForm->createView(),
    //     ]);
    // }


}
