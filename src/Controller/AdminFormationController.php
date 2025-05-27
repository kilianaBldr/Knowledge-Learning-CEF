<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Cursus;
use App\Entity\Lessons;
use App\Form\ThemeTypeForm;
use App\Form\CursusTypeForm;
use App\Form\LessonTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/formation')]
final class AdminFormationController extends AbstractController
{
    // LISTE DES THEMES
    #[Route('/themes', name: 'admin_theme_index')]
    public function listThemes(EntityManagerInterface $em): Response
    {
        $themesList = $em->getRepository(Theme::class)->findAll();

        return $this->render('adminFormations/theme/index.html.twig', [
            'themesList' => $themesList,
        ]);
    }

    // AJOUTER UN NOUVEAU THEME
    #[Route('/themes/new', name: 'admin_theme_new')]
    public function newTheme(Request $request, EntityManagerInterface $em): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeTypeForm::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($theme);
            $em->flush();
            
            $this->addFlash('success', 'Thème ajouté avec succès.');
            return $this->redirectToRoute('admin_theme_index');
        }

        return $this->render('adminFormations/theme/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // MODIFIER UN THEME
    #[Route('/themes/{id}/edit', name: 'admin_theme_edit')]
    public function editTheme(Theme $theme, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ThemeTypeForm::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Thème modifié avec succès.');
            return $this->redirectToRoute('admin_theme_index');
        }

        return $this->render('adminFormations/theme/edit.html.twig', [
            'form' => $form->createView(),
            'theme' => $theme,
        ]);
    }

    // SUPPRIMER UN THEME
    #[Route('/themes/{id}/delete', name: 'admin_theme_delete', methods: ['POST'])]
    public function deleteTheme(Theme $theme, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$theme->getId(), $request->request->get('_token'))) {
            $em->remove($theme);
            $em->flush();
            $this->addFlash('success', 'Thème supprimé.');
        }
        return $this->redirectToRoute('admin_theme_index');
    }

     // LISTE DES CURSUS
    #[Route('/cursus', name: 'admin_cursus_index')]
    public function listCursus(EntityManagerInterface $em): Response
    {
        $cursusList = $em->getRepository(Cursus::class)->findAll();

        return $this->render('adminFormations/cursus/index.html.twig', [
            'cursusList' => $cursusList,
        ]);
    }

    #[Route('/cursus/new', name: 'admin_cursus_new')]
    public function newCursus(Request $request, EntityManagerInterface $em): Response
    {
        $cursus = new Cursus();

        $form = $this->createForm(CursusTypeForm::class, $cursus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cursus);
            $em->flush();
            $this->addFlash('success', 'Cursus créé avec succès.');
            return $this->redirectToRoute('admin_cursus_index');
        }

        return $this->render('adminFormations/cursus/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cursus/{id}/edit', name: 'admin_cursus_edit')]
    public function editCursus(Cursus $cursus, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CursusTypeForm::class, $cursus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Cursus mis à jour.');
            return $this->redirectToRoute('admin_cursus_index', ['themeId' => $cursus->getTheme()->getId()]);
        }

        return $this->render('adminFormations/cursus/edit.html.twig', [
            'form' => $form->createView(),
            'cursus' => $cursus,
        ]);
    }

    #[Route('/cursus/{id}/delete', name: 'admin_cursus_delete')]
    public function deleteCursus(Cursus $cursus, EntityManagerInterface $em): Response
    {
        $themeId = $cursus->getTheme()->getId();
        $em->remove($cursus);
        $em->flush();

        $this->addFlash('success', 'Cursus supprimé.');
        return $this->redirectToRoute('admin_cursus_index', ['themeId' => $themeId]);
    }

    // LISTE DES LEÇONS PAR CURSUS
    #[Route('/lessons', name: 'admin_lesson_index')]
    public function listLessons(EntityManagerInterface $em): Response
    {
        $lessons = $em->getRepository(Lessons::class)->findAll();

        return $this->render('adminFormations/lesson/index.html.twig', [
            'lessons' => $lessons
        ]);
    }

     // AJOUTER UNE LEÇON
    #[Route('/lesson/new', name: 'admin_lesson_new')]
    public function newLesson( Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        
        $lesson = new Lessons();

        $form = $this->createForm(LessonTypeForm::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoFile = $form->get('videoFile')->getData();

            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                $videoFile->move(
                    $this->getParameter('videos_directory'),
                    $newFilename
                );

                $lesson->setVideoFile('videos/' . $newFilename);
            }

            $em->persist($lesson);
            $em->flush();

            return $this->redirectToRoute('admin_lesson_index');
        }

        return $this->render('adminFormations/lesson/new.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson,
        ]);
    }

    // MODIFIER UNE LEÇON
    #[Route('/lesson/{id}/edit', name: 'admin_lesson_edit')]
    public function editLesson(int $id, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $lesson = $em->getRepository(Lessons::class)->find($id);
        $form = $this->createForm(LessonTypeForm::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('videoFile')->getData();

            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                try {
                    $videoFile->move(
                        $this->getParameter('videos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de la vidéo.');
                    return $this->redirectRoute('admin_lesson_edit', ['id' => $lesson->getId()]);
                }
                $lesson->setVideoFile('videos/' . $newFilename);
            }

            $em->flush();
            return $this->redirectToRoute('admin_lesson_index', ['cursusId' => $lesson->getCursus()->getId()]);
        }

        return $this->render('adminFormations/lesson/edit.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson
        ]);
    }

    // SUPPRIMER UNE LEÇON
    #[Route('/lesson/{id}/delete', name: 'admin_lesson_delete')]
    public function deleteLesson(int $id, EntityManagerInterface $em): Response
    {
        $lesson = $em->getRepository(Lessons::class)->find($id);
        $cursusId = $lesson->getCursus()->getId();

        $em->remove($lesson);
        $em->flush();

        return $this->redirectToRoute('admin_lesson_index', ['cursusId' => $cursusId]);
    }

    //VOIR LA LEÇON
    #[Route('/lesson/{id}', name: 'admin_lesson_show')]
    public function showLesson(Lessons $lesson): Response 
    {
        return $this->render('adminFormations/lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }
}

