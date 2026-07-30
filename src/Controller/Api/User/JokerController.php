<?php

namespace App\Controller\Api\User;

use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Exception\InvalidDataException;
use App\Repository\GameRepository;
use App\Repository\User\JokerRepository;
use App\Service\User\JokerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/user/joker')]
class JokerController extends AbstractBaseController
{
    public function __construct(
        private GameRepository $gameRepository,
        private JokerManager $jokerManager,
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'app_user_joker', methods: ['GET'])]
    public function index(JokerRepository $jokerRepository): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $joker = $jokerRepository->findOneByUser($user);

        return $this->json([
            'teamId' => $joker?->getTeam()?->getId(),
            'editable' => $this->jokerEditable(),
        ]);
    }

    #[Route('/set', name: 'app_user_joker_set', methods: ['PUT'])]
    public function set(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        if (!$this->jokerEditable()) {
            $message = $this->translator->trans('access.joker.notEditable', [], 'errors');

            return $this->json(
                data: ['message' => $message],
                status: Response::HTTP_FORBIDDEN,
            );
        }

        try {
            /** @var User $user */
            $user = $this->getUser();
            $data = $this->getEditData($request);
            $joker = $this->jokerManager->updateFromArray($user, $data);
            $this->validate($joker);
        } catch (InvalidDataException $e) {
            return $this->json(
                data: ['message' => $e->getMessage()],
                status: $e->getCode(),
            );
        }

        $em->flush();

        return $this->json(
            data: ['success' => true],
            status: Response::HTTP_OK,
        );
    }

    /**
     * @return array{teamId: int}
     *
     * @throws InvalidDataException
     */
    private function getEditData(Request $request): array
    {
        /** @var array{teamId: int} $data */
        $data = $this->getAndValidData($request, ['teamId'], 'joker');

        return $data;
    }

    private function jokerEditable(): bool
    {
        $firstGame = $this->gameRepository->getFirstGame();
        if (!$firstGame) {
            return true;
        }
        $now = new \DateTime();

        return $firstGame->getDate() > $now;
    }
}
