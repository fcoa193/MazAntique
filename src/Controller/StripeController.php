<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    /**
     * @Route("/stripe", name="stripe")
     */
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }

    /**
     * @Route("/stripe/create-checkout-session", name="create_checkout_session", methods={"POST"})
     */
    public function createCheckoutSession(Request $request): Response
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => 'price_123', // Replace with your actual product price ID
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->json(['id' => $checkoutSession->id]);
    }

    /**
     * @Route("/stripe/success", name="stripe_success")
     */
    public function success(): Response
    {
        return $this->render('stripe/success.html.twig');
    }

    /**
     * @Route("/stripe/cancel", name="stripe_cancel")
     */
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}