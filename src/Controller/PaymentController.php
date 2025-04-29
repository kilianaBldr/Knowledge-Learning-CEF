<?php

namespace App\Controller;

use App\Repository\LessonsRepository;
use App\Repository\CursusRepository;
use App\Service\CartService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{

    #[Route('/success', name: 'app_stripe_success')]
    public function success(CartService $cartService, LessonsRepository $lessonsRepo, CursusRepository $cursusRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $lessons = $cartService->getLessons($em);
        $cursuses = $cartService->getCursuses($em);

        foreach ($lessons as $lesson) {
            if (!$user->getPurchasedLessons()->contains($lesson)) {
                $user->addPurchasedLesson($lesson);
            }
        }

        foreach ($cursuses as $cursus) {
            if (!$user->getPurchasedCursus()->contains($cursus)) {
                $user->addPurchasedCursus($cursus);

                foreach ($cursus->getLessons() as $lesson) {
                    if (!$user->getPurchasedLessons()->contains($lesson)) {
                        $user->addPurchasedLesson($lesson);
                    }
                }
            }
        }

        $em->flush();
        $cartService->clearCart();
        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/error', name: 'app_stripe_error')]
    public function error(): Response 
    {
        $this->addFlash('error', 'Le paiment a échoué. Veuillez réessayer.');
        return $this->render('payment/error.html.twig');
    }

}
