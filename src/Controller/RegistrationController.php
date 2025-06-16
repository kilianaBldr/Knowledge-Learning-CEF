<?php

namespace App\Controller;

use App\Entity\User;
use \DateTime;
use App\Service\MailerService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Security\UserAuthenticator;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

/**
 * Controller responsible for user registration and email confirmation.
 */
class RegistrationController extends AbstractController
{
     /**
     * Handles the user registration form.
     * Generates an email confirmation token and sends a verification email.
     *
     * @Route("/register", name="app_register")
     *
     * @param Request $request The current HTTP request
     * @param UserPasswordHasherInterface $passwordHasher The Symfony service to hash passwords
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     * @param MailerService $mailerService Custom service to send emails
     *
     * @return Response Returns the registration page or redirects to login after success
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager,
        MailerService $mailerService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Form submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Generate unique email confirmation token
            $confirmationToken = Uuid::v4()->toRfc4122();
            $user->setConfirmationToken($confirmationToken);

            // Token valid for 24 hours
            $user->setTokenRegistrationLifetime((new \DateTime())->add(new \DateInterval('P1D')));

            //  Handle password confirmation manually
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new \Symfony\Component\Form\FormError('Les mots de passe coresspondent pas.'));
            } else {
                // Hash and persist user password
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                
                // Generate absolute URL for email confirmation
                $confirmationUrl = $this->generateUrl(
                    'app_verify_email',
                    ['token' => $confirmationToken],
                    \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
                );
                // Send email using custom MailerService
                $mailerService->send(
                    $user->getEmail(),
                    'Confirmation de votre inscription',
                    'confirmation_email.html.twig',
                    [
                        'user' => $user,
                        'confirmationUrl' => $confirmationUrl,
                        'lifetimeToken' => $user->getTokenRegistrationLifetime()->format('d-m-Y')
                    ]
                );
                $this->addFlash('success', 'Votre compte a bien été créé, veuillez vérifier vos e-mails pour l\'activer.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Confirms the user's email using the provided token.
     * Activates the account and logs in the user automatically.
     *
     * @Route("/verify-email/{token}", name="app_verify_email", methods={"GET"})
     *
     * @param string $token The confirmation token from the email
     * @param UserRepository $userRepository Repository to fetch user from DB
     * @param EntityManagerInterface $entityManager To persist user verification
     * @param UserAuthenticatorInterface $userAuthenticator Symfony authentication handler
     * @param UserAuthenticator $userAuthenticatorService Custom authenticator used at login
     * @param Request $request Current request context
     *
     * @return Response Redirects to homepage after successful verification
     */
    #[Route('/verify-email/{token}', name: 'app_verify_email', methods: ['GET'])]
    public function verifyEmail(
        string $token, 
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $userAuthenticatorService,
        Request $request
        ): Response
    {
         // Check if user with token exists
        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Ce token est invalide.');
        }

        // Check token expiration
        if ($user->getTokenRegistrationLifetime() === null || new \DateTime() > $user->getTokenRegistrationLifetime()) {
            throw new AccessDeniedException('Le lien de confirmation a expiré.');
        }

        // Mark user as verified and remove token
        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a bien été activé, vous pouvez maintenant vous connecter.');

        // Automatically log in the user after activation
        return $userAuthenticator->authenticateUser(
            $user,
            $userAuthenticatorService,
            $request
        );
    }
}