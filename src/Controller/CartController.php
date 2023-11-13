<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Entity\Product;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart')]
class CartController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(CartRepository $cartRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartRepository->findAll(),
        ]);
    }

    #[Route('/{id}/new', name: 'app_cart_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CartRepository $cartRepository): Response
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartRepository->save($cart, true);

            return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cart/new.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    #[Route('/panier', name: 'panier')]
    public function panier(CartRepository $cartRepository): Response
    {
        $user = $this->getUser();
        $cartItems = $cartRepository->findBy(['user' => $user]);
    
        $myData = [];
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct();
            if ($product) {
                $myData[] = [
                    "cartId" => $cartItem->getId(),
                    "cartQuantity" => $cartItem->getQuantity(),
                    "cartPrice" => $product->getPrice(),
                    "cartPromotion" => $product->getPromotion(),
                    "cartProduct" => $product->getTitle(),
                    "cartImage" => $product->getImage(),
                    "cartDescription" => $product->getDescription(),
                ];
            }
        }
    
        return $this->render('home/panier.html.twig', [
            'cartItems' => $myData,
        ]);
    }
    

    #[Route('/add_to_cart/{productId}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart($productId, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User is not authenticated.'], 400);
        }
    
        $product = $entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            return $this->json(['success' => false, 'message' => 'Product not found.'], 404);
        }
    
        $cartItem = $entityManager->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'product' => $product,
        ]);
    
        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new Cart();
            $cartItem->setUser($user);
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
            $entityManager->persist($cartItem);
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'Produit ajouté au panier.');
    
        return $this->redirectToRoute('app_home');
    }
    
    #[Route('/cart/remove/{cartId}', name: 'remove_from_cart', methods: ['POST'])]
    public function removeFromCart($cartId, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User is not authenticated.'], 400);
        }
    
        $cartItem = $entityManager->getRepository(Cart::class)->find($cartId);
        if (!$cartItem) {
            return $this->json(['success' => false, 'message' => 'Cart item not found.'], 404);
        }
    
        if ($cartItem->getUser() !== $user) {
            return $this->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
    
        if ($cartItem->getQuantity() > 0) {
            $cartItem->setQuantity($cartItem->getQuantity() - 1);
        } else {
            // If quantity reaches 0, remove the cart item
            $entityManager->remove($cartItem);
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'Produit retiré du panier.');
    
        return $this->redirectToRoute('panier');
    }
    
}