<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Cursus;
use App\Entity\Lessons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ADMIN
        $admin = new User();
        $admin->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setName('admin')
            ->setIsVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($admin, 'Admin00'));
        $manager->persist($admin);

        // USER
        $user = new User();
        $user->setEmail('user@test.com')
            ->setRoles(['ROLE_USER'])
            ->setName('user')
            ->setIsVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($user, 'User00'));
        $manager->persist($user);

        // THEMES, CURSUS & LESSONS
        $themesData = [
            'Musique' => [
                'Cursus d’initiation à la guitare' => [
                    ['Découverte de l’instrument', 26, 'Introduction à la guitare et à ses composants.', 'videos/cours_guitare.mp4'],
                    ['Les accords et les gammes', 26, 'Apprentissage des accords de base et des gammes.', 'videos/cours_guitare.mp4']
                ],
                'Cursus d’initiation au piano' => [
                    ['Découverte de l’instrument', 26, 'Introduction au piano et à ses touches.', 'videos/cours_piano.mp4'],
                    ['Les accords et les gammes', 26, 'Travail des gammes et accords fondamentaux au piano.', 'videos/cours_piano.mp4']
                ]
            ],
            'Informatique' => [
                'Cursus d’initiation au développement web' => [
                    ['Les langages Html et CSS', 32, 'Bases du HTML et CSS pour créer un site web.', 'videos/cours_informatiques.mp4'],
                    ['Dynamiser votre site avec Javascript', 32, 'Ajout d’interactivité avec JavaScript.', 'videos/cours_informatiques.mp4']
                ]
            ],
            'Jardinage' => [
                'Cursus d’initiation au jardinage' => [
                    ['Les outils du jardinier', 16, 'Présentation des outils indispensables.', 'videos/cours_jardinage.mp4'],
                    ['Jardiner avec la lune', 16, 'Apprendre à jardiner selon les cycles lunaires.', 'videos/cours_jardinage.mp4']
                ]
            ],
            'Cuisine' => [
                'Cursus d’initiation à la cuisine' => [
                    ['Les modes de cuisson', 23, 'Techniques de cuisson essentielles.', 'videos/cours_cuisine.mp4'],
                    ['Les saveurs', 23, 'Découverte des saveurs et des assaisonnements.', 'videos/cours_cuisine.mp4']
                ],
                'Cursus d’initiation à l’art du dressage culinaire' => [
                    ['Mettre en œuvre le style dans l’assiette', 26, 'Apprendre à dresser ses assiettes avec élégance.', 'videos/cours_cuisine_culinaire.mp4'],
                    ['Harmoniser un repas à quatre plats', 26, 'Conseils pour équilibrer un repas gastronomique.', 'videos/cours_cuisine_culinaire.mp4']
                ]
            ]
        ];

        foreach ($themesData as $themeName => $cursusList) {
            $theme = new Theme();
            $theme->setName($themeName);
            $manager->persist($theme);

        foreach ($cursusList as $cursusName => $lessonsList) {
            $cursus = new Cursus();
            $cursus->setName($cursusName)
                ->setTheme($theme)
                ->setPrice(array_sum(array_column($lessonsList, 1)));
            $manager->persist($cursus);

            foreach ($lessonsList as [$name, $price, $description, $file]) {
                $lesson = new Lessons();
                $lesson->setName($name)
                    ->setPrice($price)
                    ->setDescription($description)
                    ->setVideoFile($file)
                    ->setCursus($cursus);
                $manager->persist($lesson);
            }
        }
    }

    $manager->flush();
    }
}