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

/**
 * Class PaymentController
 *
 * Handles the post-payment logic for Stripe success and error routes.
 * Updates the user's purchased lessons and cursus upon successful payment.
 */
final class PaymentController extends AbstractController
{
     /**
     * Handles successful Stripe payments.
     * Adds purchased lessons and cursus to the current user,
     * flushes changes to the database, and clears the cart.
     *
     * @Route('/success', name='app_stripe_success')
     *
     * @param CartService $cartService The service that manages the session cart
     * @param LessonsRepository $lessonsRepo Repository to retrieve lessons
     * @param CursusRepository $cursusRepo Repository to retrieve cursus
     * @param EntityManagerInterface $em Doctrine entity manager
     *
     * @return Response Renders the success page or redirects to login
     */
    #[Route('/success', name: 'app_stripe_success')]
    public function success(CartService $cartService, LessonsRepository $lessonsRepo, CursusRepository $cursusRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Redirect if user is not logged in
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // Retrieve cart content
        $cart = $cartService->getCart();
        $lessonIds = $cart['lessons'] ?? [];
        $cursusIds = $cart['cursuses'] ?? [];

        // Retrieve lesson and cursus entities from repositories
        $lessons = $lessonsRepo->findBy(['id' => $lessonIds]);
        $cursuses = $cursusRepo->findBy(['id' => $cursusIds]);

        // Add individual lessons to user's purchased list
        foreach ($lessons as $lesson) {
            if (!$user->getPurchasedLessons()->contains($lesson)) {
                $user->addPurchasedLesson($lesson);
            }
        }

        // Add cursus and its lessons to the user's account
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

        // Persist changes
        $em->flush();
        $cartService->clearCart();
        return $this->render('payment/success.html.twig');
    }

    
    /**
     * Displays the error page when Stripe payment fails or is cancelled.
     *
     * @Route('/payment/error', name='app_stripe_error')
     *
     * @return Response Renders the error page
     */
    #[Route('/payment/error', name: 'app_stripe_error')]
    public function error(): Response 
    {
        return $this->render('payment/error.html.twig');
    }

}
