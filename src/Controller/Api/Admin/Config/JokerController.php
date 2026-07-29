<?php

namespace App\Controller\Api\Admin\Config;

use App\Controller\AbstractBaseController;
use App\Entity\Team;
use App\Exception\InvalidDataException;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/admin/config/joker')]
class JokerController extends AbstractBaseController
{
    public function __construct(
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    #[Route('/set', name: 'app_config_joker_set', methods: ['PUT'])]
    public function setJoker(
        Request $request,
        TeamRepository $repository,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = $this->getAndValidData($request, ['teamId'], 'joker');
            /** @var ?Team $team */
            $team = $repository->find($data['teamId']);
            if (!$team) {
                return $this->json(
                    data: ['message' => 'Team not found'],
                    status: Response::HTTP_NOT_FOUND
                );
            }
            $repository->resetJoker();

            $team->setIsJoker(true);
            $em->flush();
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
}
