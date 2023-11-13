<?php
namespace App\Controller;

use App\Service\PayPalPaymentService;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PayPalController extends AbstractController
{
//     private $paypalPaymentService;

//     public function __construct(PayPalPaymentService $paypalPaymentService)
//     {
//         $this->paypalPaymentService = $paypalPaymentService;
//     }

//     #[Route('/get-access-token', name: 'get_access_token')]
//     public function getAccessToken(Request $request)
//     {
//         $clientId = $_ENV['CLIENT_ID'];
//         $clientSecret = $_ENV['CLIENT_SECRET'];

//         // Prepare the base64-encoded credentials.
//         $base64Credentials = base64_encode("$clientId:$clientSecret");

//         // Set the PayPal API URL for the sandbox environment.
//         $apiUrl = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';

//         // Set the data to be sent in the request body.
//         $requestData = [
//             'grant_type' => 'client_credentials',
//         ];

//         // Send the HTTP POST request to obtain an access token.
//         $httpClient = HttpClient::create();
//         $response = $httpClient->request('POST', $apiUrl, [
//             'headers' => [
//                 'Authorization' => 'Basic ' . $base64Credentials,
//                 'Content-Type' => 'application/x-www-form-urlencoded',
//             ],
//             'body' => http_build_query($requestData),
//         ]);

//         // Process the response.
//         $statusCode = $response->getStatusCode();
//         $data = $response->toArray();

//         // Check for HTTP errors.
//         if ($statusCode !== 200) {
//             throw new HttpException($statusCode, 'Error obtaining access token');
//         }

//         // Create the PayPal payment and get the approval URL
//         $approvalUrl = $this->paypalPaymentService->createPayment();

//         // Redirect the user to the PayPal approval URL
//         return $this->redirect($approvalUrl);
//     }
}
