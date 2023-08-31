<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Favorite;
use App\Form\FavoriteType;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/favorite')]
class FavoriteController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_favorite_index', methods: ['GET'])]
    public function index(FavoriteRepository $favoriteRepository): Response
    {
        return $this->render('favorite/index.html.twig', [
            'favorites' => $favoriteRepository->findAll(),
        ]);
    }

    #[Route('/{id}/new', name: 'app_favorite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FavoriteRepository $favoriteRepository): Response
    {
        $favorite = new Favorite();
        $form = $this->createForm(FavoriteType::class, $favorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $favoriteRepository->save($favorite, true);

            return $this->redirectToRoute('app_favorite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('favorite/new.html.twig', [
            'favorite' => $favorite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_favorite_show', methods: ['GET'])]
    public function show(Favorite $favorite): Response
    {
        return $this->render('favorite/show.html.twig', [
            'favorite' => $favorite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_favorite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Favorite $favorite, FavoriteRepository $favoriteRepository): Response
    {
        $form = $this->createForm(FavoriteType::class, $favorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $favoriteRepository->save($favorite, true);

            return $this->redirectToRoute('app_favorite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('favorite/edit.html.twig', [
            'favorite' => $favorite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_favorite_delete', methods: ['POST'])]
    public function delete(Request $request, Favorite $favorite, FavoriteRepository $favoriteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$favorite->getId(), $request->request->get('_token'))) {
            $favoriteRepository->remove($favorite, true);
        }

        return $this->redirectToRoute('app_favorite_index', [], Response::HTTP_SEE_OTHER);
    }



    // #[Route('/add/{productId}', name: 'add_to_favorite')]
    // #[IsGranted("ROLE_ADMIN")]
    // #[Route('/add/{productId}', name: 'add_to_favorite')]
    // public function addToFavorite(Product $product): JsonResponse
    // {
    //     $user = $this->getUser();

    //     if ($user instanceof User) {
    //         $favorites = $user->getFavorites();

    //         // Check if the product is already in the user's favorites
    //         foreach ($favorites as $favorite) {
    //             if ($favorite->getProduct() === $product) {
    //                 return $this->json(['success' => false, 'message' => 'Product is already in favorites.']);
    //             }
    //         }

    //         // If not in favorites, proceed to add the product
    //         $favorite = new Favorite();
    //         $favorite->setUser($user);
    //         $favorite->setProduct($product);

    //         $this->entityManager->persist($favorite);
    //         $this->entityManager->flush();

    //         return $this->json(['success' => true]);
    //     }

    //     return $this->json(['success' => false, 'message' => 'User is not authenticated.']);
    // }

    #[Route('/add_to_favorite/{productId}', name: 'add_to_favorite')]
    #[IsGranted("ROLE_USER")]
    public function addToFavorite(Product $product): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user instanceof User) {
            if (!$user->hasFavorite($product)) {
                $favorite = new Favorite();
                $favorite->setUser($user);
                $favorite->setProduct($product);

                $user->addToFavorite($favorite);

                $this->entityManager->persist($favorite);
                $this->entityManager->flush();

                return $this->json(['success' => true]);
            }

            return $this->json(['success' => false, 'message' => 'Product is already in favorites.']);
        }

        return $this->json(['success' => false, 'message' => 'User is not authenticated.']);
    }






    #[Route('/favorite/remove/{productId}', name: 'remove_from_favorite')]
    #[IsGranted("ROLE_ADMIN")]
    public function removeFromFavorite(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        $favorite = $entityManager->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'product' => $product,
        ]);

        if ($favorite) {
            $entityManager->remove($favorite);
            $entityManager->flush();

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'message' => 'Product is not in favorites.']);
    }
}