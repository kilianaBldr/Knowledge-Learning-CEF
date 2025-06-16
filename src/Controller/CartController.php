<?php

namespace App\Controller;

use App\Entity\Lessons;
use App\Entity\Cursus;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\StripeService;

/**
 * CartController
 *
 * Handles cart-related actions: displaying the cart, adding/removing items,
 * clearing the cart, and initiating Stripe payment sessions.
 */
final class CartController extends AbstractController
{
    /**
     * Displays the cart and prepares a Stripe Checkout session if items are present.
     *
     * @param CartService $cartService Service managing cart logic via session.
     * @param EntityManagerInterface $em Used to retrieve Lesson and Cursus entities.
     * @param StripeService $stripeService Custom service to build Stripe checkout session.
     *
     * @return Response Renders the cart view or redirects to login if not authenticated.
     */
    #[Route('/cart', name: 'app_cart_show')]
    public function show(CartService $cartService, EntityManagerInterface $em, StripeService $stripeService): Response
    {
        $cart = $cartService->getCart();
        $lessons = $em->getRepository(Lessons::class)->findBy(['id' => $cart['lessons']]);
        $cursuses = $em->getRepository(Cursus::class)->findBy(['id' => $cart['cursuses']]);
        $total = $cartService->getTotal($em);

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Prepare Stripe line items
        $lineItems = [];

        foreach ($lessons as $lesson) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $lesson->getName(),
                    ],
                    'unit_amount' => $lesson->getPrice() * 100,
                ],
                'quantity' => 1,
            ];
        }

        foreach ($cursuses as $cursus) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $cursus->getName(),
                    ],
                    'unit_amount' => $cursus->getPrice() * 100,
                ],
                'quantity' => 1,
            ];
        }

        if (empty($lineItems)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_show');
        }

        // Create Stripe checkout session
        $session = $stripeService->createCheckoutSession(
            $lineItems,
            $this->generateUrl('app_stripe_success', [], 0),
            $this->generateUrl('app_cart_show', [], 0),
        );

        return $this->render('cart/panier.html.twig', [
            'lessons' => $lessons,
            'cursuses' => $cursuses,
            'total' => $total,
            'checkoutUrl' => $session->url
        ]);
    }

    /**
     * Adds a lesson to the cart by its ID.
     */
    #[Route('/cart/add/lesson/{id}', name: 'app_cart_add_lesson')]
    public function addLesson(int $id, CartService $cartService): Response
    {
        $cartService->addLesson($id);
        return $this->redirectToRoute('app_cart_show');
    }

    /**
     * Adds a cursus to the cart by its ID.
     */
    #[Route('/cart/add/cursus/{id}', name: 'app_cart_add_cursus')]
    public function addCursus(int $id, CartService $cartService): Response
    {
        $cartService->addCursus($id);
        return $this->redirectToRoute('app_cart_show');
    }

    /**
     * Removes a cursus from the cart by its ID.
     */
    #[Route('/cart/remove/cursus/{id}', name: 'app_cart_remove_cursus')]
    public function removeCursus(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->removeCursus($id);
        return $this->redirectToRoute('app_cart_show');
    }

    /**
     * Removes a lesson from the cart by its ID.
     */
    #[Route('/cart/remove/lesson/{id}', name: 'app_cart_remove_lesson')]
    public function removeLesson(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->removeLesson($id);
        return $this->redirectToRoute('app_cart_show');
    }

    /**
     * Clears the entire cart session.
     */
    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clearCarte();
        return $this->redirectToRoute('app_cart_show');
    }
}