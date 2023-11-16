<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PasswordService;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/register')]
class RegistrationController extends AbstractController
{
    private $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    #[Route('/', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // create a new User and a new Form
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $isUserConnected = false;
        $roleUser = '';

        // Check if the user is connected
        if ($security->getUser() != null) {
            $isUserConnected = true;
            $roleUser = $security->getUser()->getRoles();
        }

        // If the form is valid and submitted Hash the password with the service
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordService->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

        // Give the role User 
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            
        // Add a flash message
            $this->addFlash('success', 'Inscription réussi! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'isUserConnected' => $isUserConnected, 'roleUser' => $roleUser
        ]);
    }
}