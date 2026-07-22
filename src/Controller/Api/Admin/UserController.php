<?php

namespace App\Controller\Api\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserColorGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/admin/users')]
class UserController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire(env: 'resolve:MAILER_FROM')]
        private string $fromEmail,
        #[Autowire(env: 'resolve:MAILER_FROM_NAME')]
        private string $fromName,
        #[Autowire(env: 'resolve:MAILER_APP_NAME')]
        private string $appName,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(
        Request $request,
        UserRepository $repository,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $page = max(
            1,
            (int) $request->query->get('page', 1)
        );

        $status = $request->query->get(
            'status',
            'all'
        );

        $limit = 20;

        $result = $repository->paginate(
            $page,
            $limit,
            $status
        );

        return $this->json([
            'items' => $result['items'],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $result['total'],
                'pages' => (int) ceil(
                    $result['total'] / $limit
                ),
            ],
        ],
            context: [
                'groups' => ['user:list'],
            ]
        );
    }

    #[Route('/stats', methods: ['GET'])]
    public function stats(
        UserRepository $repository,
    ): JsonResponse {
        return $this->json(
            $repository->getStats()
        );
    }

    #[Route('/{id}/activate', methods: ['POST'])]
    public function activate(
        User $user,
        EntityManagerInterface $em,
        UserColorGenerator $userColorGenerator,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user->setIsActive(true);

        if (null === $user->getColor()) {
            $user->setColor(
                $userColorGenerator->generate(
                    $user->getId()
                )
            );
        }

        $em->flush();

        $subject = $this->translator->trans('emails.afterActivate.subject', [], 'emails');
        $this->sendEmail($user, 'emails/pl/afterActivate.html.twig', $subject);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/{id}/deactivate', methods: ['POST'])]
    public function deactivate(
        User $user,
        EntityManagerInterface $em,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user->setIsActive(false);

        $em->flush();

        $this->sendEmail($user, 'emails/pl/afterDeactivate.html.twig', $this->translator->trans('emails.afterDeactivate.subject', [], 'emails'));

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/{id}/make-admin', methods: ['POST'])]
    public function makeAdmin(
        User $user,
        EntityManagerInterface $em,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user->addRole('ROLE_ADMIN');

        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }

    protected function sendEmail(User $user, string $template, string $subject): void
    {
        $email = new TemplatedEmail()
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template);

        $emailContext = array_merge($email->getContext(), [
            'app_name' => $this->appName,
            'user_name' => $user->getName(),
        ]);
        $email->context($emailContext);

        $this->mailer->send($email);
    }
}
