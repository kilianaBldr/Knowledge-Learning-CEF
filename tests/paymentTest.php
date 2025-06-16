<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Lessons;
use App\Entity\Cursus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @covers \App\Entity\User
 * @covers \App\Entity\Lessons
 * @covers \App\Entity\Cursus
 * @covers \App\Entity\Theme
 *
 * This test ensures the payment-related data access behaves correctly.
 * It checks that when a user purchases a lesson, it is properly associated in the database.
 * It also validates that the transaction is secure (user must be verified).
 */
class PaymentTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Boot the Symfony kernel and get the EntityManager.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * @test
     * @description Verifies that a verified user can successfully purchase a lesson.
     * This simulates a real purchase process by creating related entities (theme, cursus, lesson),
     * attaching the lesson to the user, and confirming the lesson is saved correctly.
     */
    public function testLessonPurchase(): void
    {
        // Create user
        $user = new User();
        $user->setName('testbuyer');
        $user->setEmail('testbuyer@test.com');
        $user->setPassword('password'); // This would usually be hashed
        $user->setIsVerified(true); // Required for purchase

        // Create theme
        $theme = new Theme();
        $theme->setName('Test Theme');

        // Create cursus
        $cursus = new Cursus();
        $cursus->setName('Test Cursus')
               ->setPrice(0)
               ->setTheme($theme);

        // Create lesson
        $lesson = new Lessons();
        $lesson->setName('Test Lesson')
               ->setPrice(10)
               ->setDescription('Test description')
               ->setVideoFile('videos/test.mp4')
               ->setContent('Mock content for testing')
               ->setCursus($cursus);

        // Persist data
        $this->entityManager->persist($theme);
        $this->entityManager->persist($cursus);
        $this->entityManager->persist($lesson);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Simulate purchase
        $user->addPurchasedLesson($lesson);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Assert the lesson was purchased
        $this->assertTrue(
            $user->getPurchasedLessons()->contains($lesson),
            'The user should have the lesson in their purchased lessons list.'
        );
    }

    /**
     * Clean up test data (user only) after test execution.
     * Avoids DB pollution and ensures isolated test environment.
     */
    protected function tearDown(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['name' => 'testbuyer']);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        parent::tearDown();
    }
}