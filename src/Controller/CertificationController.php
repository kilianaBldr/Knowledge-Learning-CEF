<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Theme;
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
            $themeId = $theme->getId();

            if (!isset($themes[$themeId])) {
                $themes[$themeId]['theme'] = $theme;
                $themes[$themeId]['cursus'] = [];
                $themes[$themeId]['validatedLessons'] = 0;
                $themes[$themeId]['totalLessons'] = 0;
            }

            $themes[$themeId]['cursus'][] = $certif->getCursus();

            foreach ($certif->getCursus()->getLessons() as $lesson) {
                $themes[$themeId]['totalLessons']++;
                if ($user->getCertifications()->exists(fn($k, $c) => $c->getLesson() === $lesson)) {
                    $themes[$themeId]['validatedLessons']++;
                }
            }
        }

        foreach ($themes as &$data) {
            $total = $data['totalLessons'];
            $valid = $data['validatedLessons'];
            $data['progress'] = $total > 0 ? round(($valid / $total) * 100) : 0;
            $data['isCertified'] = $total > 0 && $total === $valid;
        }

        return $this->render('certifications/certifications.html.twig', [
            'themes' => $themes,
        ]);
    }

    #[Route('/certifications/theme/{id}', name: 'app_certifications_show')]
    public function show(Theme $theme, EntityManagerInterface $em): Response
    {
        $user =$this->getUser();
        $certifications = $em->getRepository(Certification::class)->findBy(['user' => $user]);

        $validatedLessons = 0;
        $totalLessons = 0;

        foreach ($theme->getCursuses() as $cursus) {
            foreach ($cursus->getLessons() as $lesson) {
                $totalLessons++;
                foreach ($certifications as $certif) {
                    if ($certif->getLesson()=== $lesson) {
                        $validatedLessons++;
                    }
                }
            }
        }
        $isCertified = $totalLessons > 0 && $validatedLessons === $totalLessons;
        return $this->render('certifications/show.html.twig', [
            'theme' => $theme,
            'isCertified' => $isCertified,
        ]);
    }
}
