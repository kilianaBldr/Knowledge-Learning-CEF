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

class RegistrationController extends AbstractController
{
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

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer un token unique pour la vérification de l'e-mail
            $confirmationToken = Uuid::v4()->toRfc4122();
            $user->setConfirmationToken($confirmationToken);

            // Définir l'expiration du token à 24 heures
            $user->setTokenRegistrationLifetime((new \DateTime())->add(new \DateInterval('P1D')));

            // Hasher le mot de passe
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new \Symfony\Component\Form\FormError('Les mots de passe coresspondent pas.'));
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                
                // Générer l'URL de confirmation
                $confirmationUrl = $this->generateUrl(
                    'app_verify_email',
                    ['token' => $confirmationToken],
                    \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
                );
                // Envoi de l'e-mail de confirmation
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
        // Récupérer l'utilisateur via le token
        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Ce token est invalide.');
        }

        // Vérifier si le token a expiré
        if ($user->getTokenRegistrationLifetime() === null || new \DateTime() > $user->getTokenRegistrationLifetime()) {
            throw new AccessDeniedException('Le lien de confirmation a expiré.');
        }

        // Valider l’utilisateur et supprimer le token
        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a bien été activé, vous pouvez maintenant vous connecter.');

        //Connecter autmatiquement l'utilisateur apres activation
        return $userAuthenticator->authenticateUser(
            $user,
            $userAuthenticatorService,
            $request
        );
    }
}