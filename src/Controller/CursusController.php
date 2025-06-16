<?php

namespace App\Controller;

use App\Entity\Cursus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class CursusController
 *
 * Handles the display of a specific cursus based on its ID.
 * This controller is responsible for rendering the detail view of a cursus.
 */
final class CursusController extends AbstractController
{
    /**
    * Show the details of a specific cursus.
    *
    * @param Cursus $cursus The cursus entity automatically injected by Symfony based on the {id} route parameter.
    * @return Response The rendered view with cursus information.
    *
    * @Route("/cursus/{id}", name="app_cursus_show")
    */
    #[Route('/cursus/{id}', name: 'app_cursus_show')]
    public function index(Cursus $cursus): Response
    {
        return $this->render('cursus/cursus.html.twig', [
            'controller_name' => 'CursusController',
            'cursus' => $cursus,
        ]);
    }
    
}