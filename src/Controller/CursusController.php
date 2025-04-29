<?php

namespace App\Controller;

use App\Entity\Cursus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CursusController extends AbstractController
{
    #[Route('/cursus/{id}', name: 'app_cursus_show')]
    public function index(Cursus $cursus): Response
    {
        return $this->render('cursus/cursus.html.twig', [
            'controller_name' => 'CursusController',
            'cursus' => $cursus,
        ]);
    }
    
}