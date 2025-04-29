<?php

namespace App\Controller;

use App\Entity\Certification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CertificationController extends AbstractController
{
    #[Route('/certifications', name: 'app_certifications')]
    public function showCertification(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $certifications = $em->getRepository(Certification::Class)->findBy(['user' => $user]);

        $themes = [];
        foreach ($certifications as $certif) {
            $theme = $certif->getCursus()->getTheme();
            $themes[$theme->getId()]['theme'] = $theme;
            $themes[$theme->getId()]['cursus'][] = $certif->getCursus();
        }

        return $this->render('certifications/certifications.html.twig', [
            'themes' => $themes,
        ]);
    }

    #[Route('/certifications/theme/{id}', name: 'app_certifications_show')]
    public function show(Theme $theme): Response
    {
    
        return $this->render('certifications/show.html.twig', [
            'theme' => $theme,
        ]);
    }
}
