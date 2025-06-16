<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class HomeController
 *
 * This controller handles the main entry point of the application.
 * It renders the homepage for all users (guests or authenticated).
 */
final class HomeController extends AbstractController
{
     /**
     * Display the homepage of the platform.
     *
     * This method is mapped to the root route `/`.
     * It renders the `home/home.html.twig` template with a controller name variable.
     *
     * @Route('/', name='app_home')
     *
     * @return Response The rendered homepage response
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
