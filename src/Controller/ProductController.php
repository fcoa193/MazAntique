<?php

namespace App\Controller;
use App\Entity\Product;
use App\Form\SearchFormType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductController extends AbstractController
{
    #[Route('/product', name: 'product')]
    public function allProducts(ProductRepository $productRepository): Response
    {
        $produ = $productRepository->findAll();

        // Construction du tableau à partir des données récupérées
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
        return $this->json($myData);
    }

    #[Route('/delete.html.twig', name: 'delete_form')]
    public function deleteForm(){
        return $this->render('product/delete.html.twig');
    }

    #[Route('/{id}/productDescription', name: 'productDescription', methods: ['GET'])]
    public function productDescription($id, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
        $myData = [
            "productId" => $product->getId(),
            "productTitle" => $product->getTitle(),
            "productPrice" => $product->getPrice(),
            "productImage" => $product->getImage(),
            "productDescription" => $product->getDescription(),
        ];
        return $this->render('product/productDescription.html.twig', ['myData' => $myData]);
    }

    #[Route('/partials/_header.html.twig', name: 'search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepository)
    {
        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        $searchResults = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $searchQuery = $form->get('search')->getData();
            // Perform the search in the database using the title field
            $searchResults = $productRepository->findBy(['title' => $searchQuery]);
        }
        return $this->render('/home/index.html.twig', [
            'searchForm' => $form->createView(),
            'searchResults' => $searchResults,
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function newProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);
            $this->addFlash('success', 'Product created successfully.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }   

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Product $product, ProductRepository $productRepository, Security $security): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'product_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Product $product, ProductRepository $productRepository, Security $security): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }
        return $this->redirectToRoute('app_home', [
        ], Response::HTTP_SEE_OTHER);
    }




    public function addToCart($productId, SessionInterface $session)
    {
        // Get the cart data from session
        $cart = $session->get('cart', []);

        // Add the product to the cart or update its quantity
        if (isset($cart[$productId])) {
            $cart[$productId]++;
        } else {
            $cart[$productId] = 0;
        }

        // Store the updated cart data in session
        $session->set('cart', $cart);
        return new Response();
    }

    #[Route('showCart', name: 'showCart', methods: ['GET', 'POST'])]
    public function showCart(SessionInterface $session)
    {
        $cart = $session->get('cart', []);
        dump($cart);

        return $this->render('home/panier.html.twig', [ 'cart' => $cart]);
    }
}