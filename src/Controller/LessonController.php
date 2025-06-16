<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Lessons;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LessonController
 *
 * Handles actions related to individual lessons:
 * - Access control
 * - Displaying lesson content
 * - Validating lesson completion
 */
class LessonController extends AbstractController
{
     /**
     * Displays the lesson content if the user is authenticated and has purchased it.
     *
     * @Route('/lesson/{id}', name='app_lesson_show')
     *
     * @param Lessons $lesson The lesson entity to display
     * @return Response The rendered lesson page or redirection
     */
    #[Route('/lesson/{id}', name: 'app_lesson_show')]
    public function show(Lessons $lesson): Response
    {
         // Check if user is authenticated
        if (!$this->getUser()) {
            dd($user->getRoles());
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette leçon.');
            return $this->redirectToRoute('app_login');
        }

        // Check if the lesson is purchased
        if (!$this->getUser()->getPurchasedLessons()->contains($lesson)) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette leçon.');
            return $this->redirectToRoute('app_cursus_show', ['id' => $lesson->getCursus()->getId()]);
        }
        
        return $this->render('lesson/lesson.html.twig', [
            'lesson' => $lesson,
        ]);
    }

     /**
     * Validates a lesson manually by the user.
     * Creates a Certification if it doesn't already exist.
     *
     * @Route('/lesson/{id}/validate', name='app_lesson_validate', methods={"POST"})
     *
     * @param Lessons $lesson The lesson being validated
     * @param EntityManagerInterface $em Doctrine's entity manager
     * @return Response Redirects to the cursus page with flash messages
     */
    #[Route('/lesson/{id}/validate', name: 'app_lesson_validate', methods: ['POST'])]
    public function validateLesson(Lessons $lesson, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Ensure the user is authenticated and has purchased the lesson
        if (!$user || !$user->getPurchasedLessons()->contains($lesson)) {
            $this->addFlash('error', 'Vous ne pouvez pas valider cette leçon.');
            return $this->redirectToRoute('app_home');
        }

        // Check if the certification already exists for this lesson
        $existingCertification = $em->getRepository(Certification::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);
        if (!$existingCertification) {
            $certification = new Certification();
            $certification->setUser($user);
            $certification->setLesson($lesson);
            $certification->setCursus($lesson->getCursus());
            $certification->setTheme($lesson->getCursus()->getTheme());
            $certification->setDateObtained(new \DateTimeImmutable());

            $em->persist($certification);
            $em->flush();

            $this->addFlash('success', 'Leçon validée avec succès.');
        } else {
            $this->addFlash('info', 'Cette leçon est déjà validée.');
        }
        return $this->redirectToRoute('app_cursus_show', ['id' => $lesson->getCursus()->getId()]);
    }
}