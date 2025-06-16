<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * CertificationController
 *
 * Handles the logic for displaying user certifications grouped by theme,
 * and checking the completion status of each theme.
 */
final class CertificationController extends AbstractController
{
    /**
     * Displays all certifications grouped by theme with progress and validation status.
     *
     * - Retrieves certifications for the current user.
     * - Groups them by theme and cursus.
     * - Calculates progress for each theme.
     *
     * @param EntityManagerInterface $em The Doctrine entity manager.
     *
     * @return Response Renders the certifications overview page.
     */
    #[Route('/certifications', name: 'app_certifications')]
    public function showCertification(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $certifications = $em->getRepository(Certification::Class)->findBy(['user' => $user]);

        $themes = [];

        foreach ($certifications as $certif) {
            $theme = $certif->getCursus()->getTheme();
            $themeId = $theme->getId();

            // Initialize theme data structure if not already present
            if (!isset($themes[$themeId])) {
                $themes[$themeId]['theme'] = $theme;
                $themes[$themeId]['cursus'] = [];
                $themes[$themeId]['validatedLessons'] = 0;
                $themes[$themeId]['totalLessons'] = 0;
            }

            // Group cursus by theme
            $themes[$themeId]['cursus'][] = $certif->getCursus();

            // Count total and validated lessons for progress bar
            foreach ($certif->getCursus()->getLessons() as $lesson) {
                $themes[$themeId]['totalLessons']++;

                if ($user->getCertifications()->exists(fn($k, $c) => $c->getLesson() === $lesson)) {
                    $themes[$themeId]['validatedLessons']++;
                }
            }
        }

        // Calculate progress percentage and check if all lessons are validated
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

    /**
     * Displays the official certification page for a given theme.
     *
     * - Verifies that the user has completed all lessons for the theme.
     *
     * @param Theme $theme The theme to check.
     * @param EntityManagerInterface $em Doctrine entity manager.
     *
     * @return Response Renders the theme-specific certification page.
     */
    #[Route('/certifications/theme/{id}', name: 'app_certifications_show')]
    public function show(Theme $theme, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $certifications = $em->getRepository(Certification::class)->findBy(['user' => $user]);

        $validatedLessons = 0;
        $totalLessons = 0;

        foreach ($theme->getCursuses() as $cursus) {
            foreach ($cursus->getLessons() as $lesson) {
                $totalLessons++;
                foreach ($certifications as $certif) {
                    if ($certif->getLesson() === $lesson) {
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