<?php

namespace App\Controller\Api\Admin\Config;

use App\Controller\AbstractBaseController;
use App\Entity\Stage;
use App\Exception\InvalidDataException;
use App\Repository\StageRepository;
use App\Service\StageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/admin/config/stage')]
class StageController extends AbstractBaseController
{
    public function __construct(
        private StageManager $stageManager,
        private EntityManagerInterface $em,
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    #[Route('/list', name: 'app_config_stage_list', methods: ['GET'])]
    public function index(StageRepository $repository): JsonResponse
    {
        return $this->json(
            data: $repository->findBy([], ['createdAt' => 'DESC']),
            context: ['groups' => ['admin:stage:list']],
        );
    }

    #[Route('/add', name: 'app_config_stage_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $stage = new Stage();

            $data = $this->getEditData($request);
            $this->stageManager->updateFromArray($stage, $data);
            $this->validate($stage);

            $this->em->persist($stage);
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

    #[Route(':{stage}/edit', name: 'app_config_stage_edit', methods: ['PATCH'])]
    public function edit(
        Request $request,
        Stage $stage,
    ): JsonResponse {
        try {
            $data = $this->getEditData($request);
            $this->stageManager->updateFromArray($stage, $data);
            $this->validate($stage);

            $this->em->flush();
        } catch (InvalidDataException $e) {
            return $this->json(
                data: ['message' => $e->getMessage()],
                status: $e->getCode(),
            );
        }

        $this->em->flush();

        return $this->json(
            data: ['success' => true],
            status: Response::HTTP_OK
        );
    }

    #[Route(':{stage}/remove', name: 'app_config_stage_remove', methods: ['DELETE'])]
    public function remove(Stage $stage): Response
    {
        foreach ($stage->getGroups()->toArray() as $group) {
            $stage->removeGroup($group);
        }

        $this->em->remove($stage);
        $this->em->flush();

        return new Response(content: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array{name: string, shortName: string, groupIds: int[]}
     *
     * @throws InvalidDataException
     */
    private function getEditData(Request $request): array
    {
        /** @var array{name: string, shortName: string, groupIds: int[]} $data */
        $data = $this->getAndValidData($request, ['name', 'groupIds', 'shortName'], 'stage');

        return $data;
    }
}
