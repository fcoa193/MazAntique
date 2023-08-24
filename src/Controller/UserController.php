<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Random\Engine\Secure;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Security $security): Response
    {
        $isUserConnected = false;
        $roleUser = '';
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }

        $user = $security->getUser(); // Obtient l'utilisateur connecté

        return $this->render('user/index.html.twig', [
            'user' => $user, 'isUserConnected' => $isUserConnected, 'roleUser' => $roleUser

        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, Security $security): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        $isUserConnected = false;
        $roleUser = '';
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'isUserConnected' => $isUserConnected,
            'roleUser' => $roleUser
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}