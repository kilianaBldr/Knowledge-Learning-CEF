<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\UserRepository
 *
 * This test class validates the core data access functionalities of the UserRepository,
 * including user retrieval by email and entity persistence.
 */
class UserRepositoryTest extends KernelTestCase
{
    private ?UserRepository $userRepository = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    /**
     * @test
     * @description Ensures a user can be persisted and retrieved by name or email.
     */
    public function testFindUserByEmail(): void
    {
        // Create user
        $user = new User();
        $user->setEmail('repo@test.com');
        $user->setName('RepositoryTestUser');
        $user->setPassword('hashed-password');
        $user->setIsVerified(true);

        // Persist user
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        // Retrieve user by email
        $fetchedUser = $this->userRepository->findOneBy(['email' => 'repo@test.com']);

        // Assert user is correctly fetched
        $this->assertNotNull($fetchedUser, 'User should be found by email.');
        $this->assertSame('repo@test.com', $fetchedUser->getEmail());
        $this->assertSame('RepositoryTestUser', $fetchedUser->getName());

        // Cleanup
        $em->remove($fetchedUser);
        $em->flush();
    }
}
