<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Form\PromotionType;
use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PromotionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/promotion', name: 'promotion')]
    public function listPromotions(PromotionRepository $promotionRepository)
    {
        $promotions = $promotionRepository->findAll();

        return $this->render('promotion/index.html.twig', [
            'promotions' => $promotions,
        ]);
    }
    
    #[Route('/promotion/json', name: 'promotion_json')]
    public function listPromotionsJson(PromotionRepository $promotionRepository)
    {
        $promotions = $promotionRepository->findAll();
    
        $myData = [];
        foreach ($promotions as $promo) {
            $myData[] = [
                "promoId" => $promo->getId(),
                "promoName" => $promo->getName(),
                "promoDescription" => $promo->getDescription(),
                "promoStartDate" => $promo->getStartDate(),
                "promoEndDate" => $promo->getEndDate(),
                "promoDiscountPercentage" => $promo->getDiscountPercentage(),
            ];
        }
    
       return $this->render('promotion/index.html.twig', [
        'promotions' => $promotions,
        'myData' => $myData
        ]);
    }

    #[Route('/addPromotion', name: 'addPromotion')]
    public function addPromotion(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($promotion);
            $this->entityManager->flush();

            $this->addFlash('success', 'Promotion created successfully.');
            return $this->redirectToRoute('promotion');
        } else {

        }
        return $this->render('promotion/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editPromotion', name: 'editPromotion')]
    public function editPromotion(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($promotion);
            $entityManager->flush();
            $this->addFlash('success', 'Promotion modifiée!');
            return $this->redirectToRoute('promotion', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('promotion/edit.html.twig', [
            'promotion' => $promotion,
            'form' => $form->createView()
        ]);
    }
     
    #[Route('/{id}/deletePromotion', name: 'deletePromotion')]
    public function deletePromotion(Request $request, Promotion $promotion)
    {
        if ($this->isCsrfTokenValid('delete'.$promotion->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($promotion);
            $this->entityManager->flush();

            $this->addFlash('success', 'Promotion deleted successfully.');
        } else {
            $this->addFlash('error', 'CSRF token validation failed.');
        }

        return $this->redirectToRoute('promotion');
    }



    #[Route('/promotion/{id}/associate-products', name: 'associate_products')]
    public function associateProducts(Request $request, Promotion $promotion)
    {
        $form = $this->createForm(PromotionProductType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('promotion_details', ['id' => $promotion->getId()]);
        }

        return $this->render('promotion/associate_products.html.twig', [
            'form' => $form->createView(),
            'promotion' => $promotion,
        ]);
    }
}