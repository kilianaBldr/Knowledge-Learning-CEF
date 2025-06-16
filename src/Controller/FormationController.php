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

/**
 * Class FormationController
 *
 * Responsible for displaying the list of themes and computing the user's progress
 * (validated lessons and certification) for each theme.
 */
final class FormationController extends AbstractController
{
    /**
     * Display all themes along with user's progress for each theme.
     *
     * This route gathers all themes, counts lessons, and checks how many have been validated
     * by the currently authenticated user (via the Certification entity).
     * If at least one lesson is validated in a theme, a progress percentage is calculated.
     *
     * @param ThemeRepository $themeRepository Repository to fetch all available themes.
     * @param CertificationRepository $certRepo Repository to check validated lessons by the user.
     * @param EntityManagerInterface $em Doctrine entity manager (not used here but kept for future logic if needed).
     * @return Response The rendered Twig view containing all themes with progress bars.
     *
     * @Route('/formations', name='app_formations')
     */
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

            // Count all lessons and validated lessons
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

        // Only calculate progress if at least one lesson is validated
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
