<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Security $security): Response
    {
        $isUserConnected = false;
        $roleUser = '';
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }
        $user = $security->getUser();
        return $this->render('user/index.html.twig', [
            'user' => $user, 'isUserConnected' => $isUserConnected, 'roleUser' => $roleUser

        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(){
        return $this->render('profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function editProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        PasswordService $passwordService
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $newPassword = $form->get('newPassword')->getData();
            if ($newPassword) {
                $passwordService->updateUserPassword($user, $newPassword);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Your profile has been updated successfully.');
            return $this->redirectToRoute('profile');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'There are errors in the form. Please correct them.');
        }
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/home/adminUsers', name: 'admin_users')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        dump($users);
        return $this->render('home/adminUsers.html.twig', [
            'users' => $users,
        ]);
    }
}