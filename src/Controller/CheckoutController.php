<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\CheckoutFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout', methods: ['GET', 'POST'])]
    public function checkout(Request $request): Response
    {
        $form = $this->createForm(CheckoutFormType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {                
                $this->addFlash('success', 'Your order has been successfully placed.');

                return $this->redirectToRoute('checkout_success');
            }
        }

        return $this->render('checkout/checkout.html.twig', [
            'checkoutForm' => $form->createView(),
        ]);
    }
}
