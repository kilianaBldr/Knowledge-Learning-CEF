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

         // Création admin
         $admin = new User(); 
         $admin->setEmail('admin@test.com');
         $admin->setRoles(['ROLE_ADMIN']); 
         $admin->setName('admin');
         $admin->setIsVerified(true); 
         $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin00'));
         $manager->persist($admin); 
 
         // Création de l'utilisateur 
         $user = new User(); 
         $user->setEmail('user@test.com');
         $user->setRoles(['ROLE_USER']); 
         $user->setName('user'); 
         $user->setIsVerified(true); 
         $user->setPassword($this->passwordHasher->hashPassword($user, 'User00'));
         $manager->persist($user); 
 

        // MUSIQUE
        $musique = new Theme();
        $musique->setName('Musique');
        $manager->persist($musique);

        $guitare = new Cursus();
        $guitare->setName('Cursus d’initiation à la guitare')->setPrice(50)->setTheme($musique);
        $manager->persist($guitare);
        $manager->persist((new Lessons())
            ->setName('Découverte de l’instrument')
            ->setPrice(26)
            ->setDescription('Introduction à la guitare et à ses composants.')
            ->setVideoFile('videos/cours_guitare.mp4')
            ->setCursus($guitare));
        $manager->persist((new Lessons())
            ->setName('Les accords et les gammes')
            ->setPrice(26)
            ->setDescription('Apprentissage des accords de base et des gammes.')
            ->setVideoFile('videos/cours_guitare.mp4')
            ->setCursus($guitare));

        $piano = new Cursus();
        $piano->setName('Cursus d’initiation au piano')->setPrice(50)->setTheme($musique);
        $manager->persist($piano);
        $manager->persist((new Lessons())
            ->setName('Découverte de l’instrument')
            ->setPrice(26)
            ->setDescription('Introduction au piano et à ses touches.')
            ->setVideoFile('videos/cours_piano.mp4')
            ->setCursus($piano));
        $manager->persist((new Lessons())
            ->setName('Les accords et les gammes')
            ->setPrice(26)
            ->setDescription('Travail des gammes et accords fondamentaux au piano.')
            ->setVideoFile('videos/cours_piano.mp4')
            ->setCursus($piano));

        // INFORMATIQUE
        $informatique = new Theme();
        $informatique->setName('Informatique');
        $manager->persist($informatique);

        $devWeb = new Cursus();
        $devWeb->setName('Cursus d’initiation au développement web')->setPrice(60)->setTheme($informatique);
        $manager->persist($devWeb);
        $manager->persist((new Lessons())
            ->setName('Les langages Html et CSS')
            ->setPrice(32)
            ->setDescription('Bases du HTML et CSS pour créer un site web.')
            ->setVideoFile('videos/cours_informatiques.mp4')
            ->setCursus($devWeb));
        $manager->persist((new Lessons())
            ->setName('Dynamiser votre site avec Javascript')
            ->setPrice(32)
            ->setDescription('Ajout d’interactivité avec JavaScript.')
            ->setVideoFile('videos/cours_informatiques.mp4')
            ->setCursus($devWeb));

        // JARDINAGE
        $jardinage = new Theme();
        $jardinage->setName('Jardinage');
        $manager->persist($jardinage);

        $initJardin = new Cursus();
        $initJardin->setName('Cursus d’initiation au jardinage')->setPrice(30)->setTheme($jardinage);
        $manager->persist($initJardin);
        $manager->persist((new Lessons())
            ->setName('Les outils du jardinier')
            ->setPrice(16)
            ->setDescription('Présentation des outils indispensables.')
            ->setVideoFile('videos/cours_jardinage.mp4')
            ->setCursus($initJardin));
        $manager->persist((new Lessons())
            ->setName('Jardiner avec la lune')
            ->setPrice(16)
            ->setDescription('Apprendre à jardiner selon les cycles lunaires.')
            ->setVideoFile('videos/cours_jardinage.mp4')
            ->setCursus($initJardin));

        // CUISINE
        $cuisine = new Theme();
        $cuisine->setName('Cuisine');
        $manager->persist($cuisine);

        $initCuisine = new Cursus();
        $initCuisine->setName('Cursus d’initiation à la cuisine')->setPrice(44)->setTheme($cuisine);
        $manager->persist($initCuisine);
        $manager->persist((new Lessons())
            ->setName('Les modes de cuisson')
            ->setPrice(23)
            ->setDescription('Techniques de cuisson essentielles.')
            ->setVideoFile('videos/cours_cuisine.mp4')
            ->setCursus($initCuisine));
        $manager->persist((new Lessons())
            ->setName('Les saveurs')
            ->setPrice(23)
            ->setDescription('Découverte des saveurs et des assaisonnements.')
            ->setVideoFile('videos/cours_cuisine.mp4')
            ->setCursus($initCuisine));

        $dressage = new Cursus();
        $dressage->setName('Cursus d’initiation à l’art du dressage culinaire')->setPrice(48)->setTheme($cuisine);
        $manager->persist($dressage);
        $manager->persist((new Lessons())
            ->setName('Mettre en œuvre le style dans l’assiette')
            ->setPrice(26)
            ->setDescription('Apprendre à dresser ses assiettes avec élégance.')
            ->setVideoFile('videos/cours_cuisine_culinaire.mp4')
            ->setCursus($dressage));
        $manager->persist((new Lessons())
            ->setName('Harmoniser un repas à quatre plats')
            ->setPrice(26)
            ->setDescription('Conseils pour équilibrer un repas gastronomique.')
            ->setVideoFile('videos/cours_cuisine_culinaire.mp4')
            ->setCursus($dressage));

        $manager->flush();
    }
}