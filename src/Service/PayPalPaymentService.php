<?php

namespace App\Service;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payer;

class PayPalPaymentService
{
    private $apiContext;
    
    public function __construct(string $clientId, string $clientSecret)
    {
        // Verify that $clientId and $clientSecret are used to construct the ApiContext.
        $this->apiContext = new ApiContext(new OAuthTokenCredential($clientId, $clientSecret));
    }
    

    public function createPayment()
    {
        // Create a payer
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Create an amount
        $amount = new Amount();
        $amount->setTotal('10.00');
        $amount->setCurrency('USD');

        // Create a transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount);

        // Create a payment
        $payment = new Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setTransactions([$transaction]); // Include the transaction in the payment

        // Create the payment and get approval URL
        $payment->create($this->apiContext);
        $approvalUrl = $payment->getApprovalLink();

        return $approvalUrl;
    }
}