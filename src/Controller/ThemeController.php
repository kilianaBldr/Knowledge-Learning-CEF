<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Certification;
use App\Repository\CertificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller responsible for displaying a specific theme and its user's progress.
 */
final class ThemeController extends AbstractController
{
     /**
     * Displays detailed information about a given theme, including user's progress and certification status.
     *
     * @Route("/theme/{id}", name="app_theme_show")
     *
     * @param Theme $theme The theme being viewed
     * @param CertificationRepository $certRepo Repository used to fetch validated lessons by the user
     * @param EntityManagerInterface $em Doctrine entity manager
     * @return Response Returns the rendered theme page with progress data
     */
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

        // Count total lessons and validated ones by the user
        foreach ($cursusList as $cursus) {
            foreach ($cursus->getLessons() as $lesson) {
                $totalLessons++;

                if ($certRepo->findOneBy(['user' => $user, 'lesson' => $lesson])) {
                    $validatedLessons++;
                }
            }
        }

        $progress = null;
        $isCertified = false;

        // Calculate progress and certification only if at least one lesson is purchased
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
