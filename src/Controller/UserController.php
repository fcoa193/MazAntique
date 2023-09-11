<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PasswordService;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
    private $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function updateUserPassword(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->passwordService->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
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
}