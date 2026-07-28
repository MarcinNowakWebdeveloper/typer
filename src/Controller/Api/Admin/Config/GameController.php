<?php

namespace App\Controller\Api\Admin\Config;

use App\Controller\AbstractBaseController;
use App\Entity\Game;
use App\Exception\InvalidDataException;
use App\Repository\GameRepository;
use App\Service\GameManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/admin/config/game')]
class GameController extends AbstractBaseController
{
    public function __construct(
        private GameManager $gameManager,
        private EntityManagerInterface $em,
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    #[Route('/list', name: 'app_config_game_list', methods: ['GET'])]
    public function index(GameRepository $repository): JsonResponse
    {
        return $this->json(
            data: $repository->findBy([], ['date' => 'DESC']),
            context: ['groups' => ['admin:game:list']],
        );
    }

    #[Route('/add', name: 'app_config_game_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $game = new Game();

            $data = $this->getEditData($request);
            $this->gameManager->updateFromArray($game, $data);
            $this->validate($game);

            $this->em->persist($game);
            $this->em->flush();
        } catch (InvalidDataException $exception) {
            return $this->json(
                data: ['message' => $exception->getMessage()],
                status: $exception->getCode(),
            );
        }

        return $this->json(
            data: ['success' => true],
            status: Response::HTTP_CREATED
        );
    }

    #[Route(':{game}/edit', name: 'app_config_game_edit', methods: ['PATCH'])]
    public function edit(
        Request $request,
        Game $game,
    ): JsonResponse {
        try {
            $data = $this->getEditData($request);
            $this->gameManager->updateFromArray($game, $data);
            $this->validate($game);

            $this->em->flush();
        } catch (InvalidDataException $e) {
            return $this->json(
                data: ['message' => $e->getMessage()],
                status: $e->getCode(),
            );
        }

        return $this->json(
            data: ['success' => true],
            status: Response::HTTP_OK
        );
    }

    #[Route(':{game}/remove', name: 'app_config_game_remove', methods: ['DELETE'])]
    public function remove(Game $game, EntityManagerInterface $em): Response
    {
        $em->remove($game);
        $em->flush();

        return new Response(content: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array{
     *     stageGroupId: int,
     *     date: string,
     *     time: string,
     *     homeTeamId: int,
     *     awayTeamId: int,
     *     homeGoals: ?int,
     *     awayGoals: ?int
     * }
     */
    private function getEditData(Request $request): array
    {
        /** @var array{
         * stageGroupId: int,
         * date: string,
         * time: string,
         * homeTeamId: int,
         * awayTeamId: int,
         * } $data */
        $data = $this->getAndValidData(
            $request,
            ['date', 'time', 'stageGroupId', 'homeTeamId', 'awayTeamId'],
            'game'
        );

        $payload = json_decode($request->getContent(), true);
        $data['homeGoals'] = $payload['homeGoals'] ?? null;
        $data['awayGoals'] = $payload['awayGoals'] ?? null;

        return $data;
    }
}
