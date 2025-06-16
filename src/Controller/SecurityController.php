<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller responsible for handling user authentication (login/logout).
 */
class SecurityController extends AbstractController
{
     /**
     * Displays the login form and handles login errors.
     *
     * @Route(path: "/login", name: "app_login")
     *
     * @param AuthenticationUtils $authenticationUtils Provides access to login error and last username
     * @return Response Renders the login page with potential error messages
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get any login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Get the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login form with error and username pre-filled
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

       /**
     * Logout route - handled automatically by Symfony security system.
     *
     * @Route(path: "/logout", name: "app_logout")
     *
     * @throws \LogicException This method is never executed directly, it's intercepted by Symfony firewall
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
