<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Security;

#[Route('/register')]
class RegistrationController extends AbstractController
{
    #[Route('/', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $isUserConnected = false;
        $roleUser = '';
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );


            // Assign the "ROLE_USER" role to newly registered users
            $user->setRoles(['ROLE_USER']);


            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'isUserConnected' => $isUserConnected, 'roleUser' => $roleUser
        ]);
    }
}