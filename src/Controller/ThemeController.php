<?php

namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app_theme_show')]
    public function show(Theme $theme): Response
    {
        return $this->render('theme/theme.html.twig', [
            'controller_name' => 'ThemeController',
            'theme' => $theme,
        ]);
    }
}
