<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private LoggerInterface $authLogger,
    ) {
    }

    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(
                data: ['user' => null],
                status: Response::HTTP_UNAUTHORIZED,
            );
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserIdentifier(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
                'isAdmin' => $user->isAdmin(),
                'color' => $user->getColor(),
            ],
        ]);
    }

    #[Route('/register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        EmailVerifier $emailVerifier,
        ValidatorInterface $validator,
        #[Autowire(env: 'resolve:MAILER_FROM')]
        string $fromEmail,
        #[Autowire(env: 'resolve:MAILER_FROM_NAME')]
        string $fromName,
        #[Autowire(env: 'resolve:MAILER_APP_NAME')]
        string $appName,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail(trim($data['email']));
        $user->setName(trim($data['name']));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $message = [];
            foreach ($errors as $error) {
                $message[] = $error->getMessage();
            }

            return $this->json([
                'success' => false,
                'message' => implode("\n", $message),
            ]);
        }

        $em->persist($user);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $data['password']
            )
        );

        try {
            $em->flush();
        } catch (\Throwable $e) {
            $this->authLogger->alert($e->getMessage(), $e->getTrace());
            $error = $this->translator->trans('auth.registry.errors', [], 'errors');
            if ($e instanceof \Doctrine\DBAL\Exception\UniqueConstraintViolationException) {
                $error = $this->translator->trans('auth.registry.emailDuplicate', [], 'errors');
            }

            return $this->json([
                'success' => false,
                'message' => $error,
            ]);
        }

        $this->sendConfirmEmail($fromEmail, $fromName, $user, $appName, $emailVerifier);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/verify/email', name: 'api_verify_email', methods: ['GET'])]
    public function verifyEmail(
        Request $request,
        EmailVerifier $emailVerifier,
        EntityManagerInterface $em,
    ): Response {
        try {
            $userId = $request->query->get('userId');

            $user = $em->getRepository(User::class)
                ->find($userId);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $emailVerifier->handleEmailConfirmation($request, $user);
        } catch (\Throwable $e) {
            $this->authLogger->alert($e->getMessage(), $e->getTrace());

            return $this->redirect('/verify-failed');
        }

        return $this->redirect('/waiting-for-activation');
    }

    #[Route('/login', name: 'app_login', methods: ['POST', 'GET'])]
    public function login(): never
    {
        $message = $this->translator->trans('auth.login.wrongPath', [], 'errors');
        throw new \Exception($message);
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): void
    {
        $message = $this->translator->trans('auth.logout.wrongPath', [], 'errors');
        throw new \Exception($message);
    }

    public function sendConfirmEmail(
        string $fromEmail,
        string $fromName,
        User $user,
        string $appName,
        EmailVerifier $emailVerifier,
    ): void {
        $subject = $this->translator->trans('emails.register.subject', [], 'emails');
        $email = new TemplatedEmail()
            ->from(new Address($fromEmail, $fromName))
            ->to((string) $user->getEmail())
            ->subject($subject)
            ->htmlTemplate('emails/pl/registration/confirmation_email.html.twig');

        $emailContext = array_merge($email->getContext(), [
            'app_name' => $appName,
            'user_name' => $user->getName(),
        ]);
        $email->context($emailContext);

        $emailVerifier->sendEmailConfirmation('api_verify_email', $user, $email);
    }
}
