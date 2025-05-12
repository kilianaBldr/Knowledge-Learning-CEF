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


final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart_show')]
    public function show(CartService $cartService, EntityManagerInterface $em, StripeService $stripeService
    ): Response
    {
        $cart = $cartService->getCart();
        $lessons = $em->getRepository(\App\Entity\Lessons::class)->findBy(['id' => $cart['lessons']]);
        $cursuses = $em->getRepository(\App\Entity\Cursus::class)->findBy(['id' => $cart['cursuses']]);
        $total = $cartService->getTotal($em);

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

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
    #[Route('/cart/add/lesson/{id}', name: 'app_cart_add_lesson')]
    public function addLesson(int $id, CartService $cartService): Response
    {
        $cartService->addLesson($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/add/cursus/{id}', name: 'app_cart_add_cursus')]
    public function addCursus(int $id, CartService $cartService): Response
    {
        $cartService->addCursus($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/remove/cursus/{id}', name: 'app_cart_remove_cursus')]
    public function removeCursus(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->removeCursus($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/remove/lesson/{id}', name: 'app_cart_remove_lesson')]
    public function removeLesson(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->removeLesson($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clearCarte();
        return $this->redirectToRoute('app_cart_show');
    }
}
