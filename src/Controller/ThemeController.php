<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Certification;
use App\Repository\CertificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app_theme_show')]
    public function show(
        Theme $theme,
        CertificationRepository $certRepo,
        EntityManagerInterface $em
    ): Response
    {
        $user = $this->getUser();
        $cursusList = $theme->getCursuses();

        $totalLessons = 0;
        $validatedLessons = 0;
        $hasPurchasedLesson = false;

        foreach ($cursusList as $cursus) {
            foreach ($cursus->getLessons() as $lesson) {
                $totalLessons++;
                if ($user->getPurchasedLessons()->contains($lesson)) {
                    $hasPurchasedLesson = true;
                }

                if ($certRepo->findOneBy(['user' => $user, 'lesson' => $lesson])) {
                    $validatedLessons++;
                }
            }
        }

        $progress = null;
        $isCertified = false;

        if ($hasPurchasedLesson) {
            $progress = $totalLessons > 0 ? round(($validatedLessons / $totalLessons) * 100) : 0;
            $isCertified = $progress === 100;
        }

        return $this->render('theme/theme.html.twig', [
            'theme' => $theme,
            'progress' => $progress,
            'isCertified' => $isCertified,
        ]);
    }
}
