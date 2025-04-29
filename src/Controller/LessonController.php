<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Lessons;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonController extends AbstractController
{
    #[Route('/lesson/{id}', name: 'app_lesson_show')]
    public function show(Lessons $lesson): Response
    {
        // Vérifie si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette leçon.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si l'utilisateur a bien acheté la leçon
        if (!$this->getUser()->getPurchasedLessons()->contains($lesson)) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette leçon.');
            return $this->redirectToRoute('app_cursus_show', ['id' => $lesson->getCursus()->getId()]);
        }

        return $this->render('lesson/lesson.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/lesson/{id}/validate', name: 'app_lesson_validate', methods: ['POST'])]
    public function validateLesson(Lessons $lesson, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Vérifie si l'utilisateur a bien acheté la leçon
        if (!$user || !$user->getPurchasedLessons()->contains($lesson)) {
            $this->addFlash('error', 'Vous ne pouvez pas valider cette leçon.');
            return $this->redirectToRoute('app_home');
        }

        //Vérifie si la leçon est déja validée
        $existingCertification = $em->getRepository(Certification::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);
        if (!$existingCertification) {
            $certification = new Certification();
            $certification->setUser($user);
            $certification->setLesson($lesson);
            $certification->setDateObtained(new \DateTimeImmutable());

            $em->persist($certification);
            $em->flush();

            $this->addFlash('success', 'Leçon validée avec succès.');
        } else {
            $this->addFlash('info', 'Cette leçon es déjà validée.');
        }
        return $this->redirectToRoute('app_cursus_show', ['id' => $lesson->getCursus()->getId()]);
    }
}