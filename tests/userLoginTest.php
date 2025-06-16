<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @covers \App\Security\UserAuthenticator
 *
 * Functional and security test for the login system using username and password.
 * Ensures that valid credentials allow login and invalid ones return an error.
 */
class UserLoginTest extends WebTestCase
{
    private $client;
    private $entityManager;

    /**
     * Sets up the client and test user before each test.
     *
     * - Removes any existing user named 'login'
     * - Creates a test user with name 'login' and hashed password
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get(EntityManagerInterface::class);

        // Clean up if user already exists
        $existing = $this->entityManager->getRepository(User::class)->findOneBy(['name' => 'login']);
        if ($existing) {
            $this->entityManager->remove($existing);
            $this->entityManager->flush();
        }

        // Create test user
        $user = new User();
        $user->setName('login')
             ->setEmail('login@test.com')
             ->setPassword(password_hash('password123', PASSWORD_BCRYPT))
             ->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @test
     * @description Tests login with correct username and password.
     * Expects a successful response and username to be displayed after login.
     */
    public function testUserLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            'name' => 'login',
            'password' => 'password123',
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'login');
    }

    /**
     * @test
     * @description Tests login with incorrect credentials.
     * Expects login form to show an error message with class '.alert'.
     */
    public function testLoginFail(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            'name' => 'wronguser',
            'password' => 'wrongpass',
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert');
        $this->assertSelectorTextContains('.alert', 'Identifiants incorrects');
    }
}
