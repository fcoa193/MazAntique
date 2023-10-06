<?php

namespace App\Controller;

use App\Entity\Liked;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\DBAL\Connection;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/product', name: 'product')]
    public function allProducts(ProductRepository $productRepository): Response
    {
        $produ = $productRepository->findAll();

        $myData = [];
        foreach ($produ as $prod) {
            $myData[] = [
                "productId" => $prod->getId(),
                "productTitle" => $prod->getTitle(),
                "productPrice" => $prod->getPrice(),
                "productImage" => $prod->getImage(),
                "productDescription" => $prod->getDescription(),
                "productPromotion" => $prod->getPromotion()
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
        $myData = [];
        $myData = [
            "productId" => $product->getId(),
            "productTitle" => $product->getTitle(),
            "productPrice" => $product->getPrice(),
            "productImage" => $product->getImage(),
            "productDescription" => $product->getDescription(),
        ];
        return $this->render('product/productDescription.html.twig', ['myData' => $myData]);
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
            $this->addFlash('success', 'Produit créé!');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }   

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);
            $this->addFlash('success', 'Modification réussite!');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'product_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
            $this->addFlash('success', 'Produit supprimé!');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/index.html.twig');
    }

    //// Liked functionality ////

    #[Route('/like_product/{productId}', name: 'like_product', methods: ['GET'])]
    public function likeProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $likeRepository = $entityManager->getRepository(Liked::class);
        $existingLiked = $likeRepository->findOneBy(['user' => $user, 'product' => $product]);

        if (!$existingLiked) {
            $like = new Liked();
            $like->setUser($user);
            $like->setProduct($product);
            $like->setCreatedAt(new \DateTime());
            $entityManager->persist($like);
            $entityManager->flush();
        }
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => 'liked']);
        }
        return $this->render('home/index.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/unlike_product/{productId}', name: 'unlike_product', methods: ['GET'])]
    public function unlikeProduct(Product $product, Request $request): Response
    {
        $user = $this->getUser();
        $likeRepository = $this->entityManager->getRepository(Liked::class);
        $existingLiked = $likeRepository->findOneBy(['user' => $user, 'product' => $product]);

        if ($existingLiked) {
            $this->entityManager->remove($existingLiked);
            $this->entityManager->flush();
        }
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => 'unliked']);
        }
        return $this->render('home/index.html.twig', [
            'product' => $product,
        ]);
    }
}