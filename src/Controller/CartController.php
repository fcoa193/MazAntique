<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[Route('/cart', name: 'view_cart')]
    #[IsGranted("ROLE_USER")]
    public function viewCart(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
    
        $cartItems = $entityManager->getRepository(CartItem::class)->findBy(['user' => $user]);
    
        return $this->render('cart/view_cart.html.twig', [
            'cartItems' => $cartItems,
        ]);
    }
    

    #[Route('/add_to_cart/{productId}', name: 'add_to_cart', methods: ['POST'])]
    // #[IsGranted("ROLE_USER")]
    public function addToCart(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User is not authenticated.'], 400);
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
    
        return $this->json(['success' => true, 'message' => 'Product added to cart.'], 200);
    }
    

    #[Route('/cart/remove/{productId}', name: 'remove_from_cart')]
    #[IsGranted("ROLE_ADMIN")]
    public function removeFromCart(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        $cart = $entityManager->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'product' => $product,
        ]);

        if ($cart) {
            $entityManager->remove($cart);
            $entityManager->flush();

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'message' => 'Product is not in the cart.']);
    }
}