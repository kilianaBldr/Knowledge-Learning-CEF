<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @covers \App\Entity\User
 *
 * Functional and security test to validate user creation and password hashing.
 * This test confirms that the User entity behaves as expected when instantiated manually.
 */
class UserCreationTest extends KernelTestCase
{
    /**
     * @test
     * @description Verifies that a user object is created with valid data, roles are set,
     * and the password is securely hashed (not stored in plain text).
     */
    public function testUserIsCreatedCorrectly(): void
    {
        // Boot the Symfony kernel and get container
        self::bootKernel();
        $container = static::getContainer();

        // Get the password hasher from the container
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        // Create and set up user data
        $user = new User();
        $user->setEmail('test@user.com');
        $user->setName('Test Unitaire');
        $user->setRoles(['ROLE_USER']);

        // Hash the password and assign it
        $hashedPassword = $hasher->hashPassword($user, 'testpassword');
        $user->setPassword($hashedPassword);

        // Functional assertions
        $this->assertSame('test@user.com', $user->getEmail(), 'Email should match the expected value.');
        $this->assertSame('Test Unitaire', $user->getName(), 'Name should match the expected value.');
        $this->assertContains('ROLE_USER', $user->getRoles(), 'User should have ROLE_USER.');
        $this->assertNotEmpty($user->getPassword(), 'Password hash should not be empty.');

        // Security assertion: the stored password must not be plain text
        $this->assertNotEquals('testpassword', $user->getPassword(), 'Password should be hashed and not equal to the raw value.');
    }
}

