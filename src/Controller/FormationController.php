<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Certification;
use App\Repository\ThemeRepository;
use App\Repository\CertificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formations')]
    public function index(
        ThemeRepository $themeRepository,
        CertificationRepository $certRepo,
        EntityManagerInterface $em
    ): Response {
        $themes = $themeRepository->findAll();
        $user = $this->getUser();
        $themeProgress = [];

        foreach ($themes as $theme) {
            $cursusList = $theme->getCursuses();
            $totalLessons = 0;
            $validatedLessons = 0;

            foreach ($cursusList as $cursus) {
                $lessons = $cursus->getLessons();
                $totalLessons += count($lessons);

                foreach ($lessons as $lesson) {
                    $certif = $certRepo->findOneBy([
                    'user' => $user,
                    'lesson' => $lesson,
                ]);
                if ($certif) {
                    $validatedLessons++;
                }
            }
        }

        // Seulement si au moins une leçon est validée
        if ($validatedLessons > 0) {
            $progress = $totalLessons > 0 ? round(($validatedLessons / $totalLessons) * 100) : 0;
            $isCertified = $progress === 100;

            $themeProgress[$theme->getId()] = [
                'progress' => $progress,
                'certified' => $isCertified,
            ];
        }
    }

    return $this->render('formation/formation.html.twig', [
        'themes' => $themes,
        'themeProgress' => $themeProgress,
        ]);
    }
}
