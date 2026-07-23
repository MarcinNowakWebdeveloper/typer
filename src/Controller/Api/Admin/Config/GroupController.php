<?php

namespace App\Controller\Api\Admin\Config;

use App\Controller\AbstractBaseController;
use App\Entity\Group;
use App\Exception\InvalidDataException;
use App\Repository\GroupRepository;
use App\Service\GroupManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/admin/config/group')]
class GroupController extends AbstractBaseController
{
    public function __construct(
        private GroupManager $groupManager,
        private EntityManagerInterface $em,
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    #[Route('/list', name: 'app_config_group_list', methods: ['GET'])]
    public function index(GroupRepository $repository): JsonResponse
    {
        return $this->json(
            data: $repository->findBy([], ['createdAt' => 'DESC']),
            context: ['groups' => ['admin:group:list']],
        );
    }

    #[Route('/add', name: 'app_config_group_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $group = new Group();

            $data = $this->getEditData($request);
            $this->groupManager->updateFromArray($group, $data);
            $this->validate($group);

            $this->em->persist($group);
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

    #[Route(':{group}/edit', name: 'app_config_group_edit', methods: ['PATCH'])]
    public function edit(
        Request $request,
        Group $group,
    ): JsonResponse {
        try {
            $data = $this->getEditData($request);
            $this->groupManager->updateFromArray($group, $data);
            $this->validate($group);

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

    #[Route(':{group}/remove', name: 'app_config_group_remove', methods: ['DELETE'])]
    public function remove(Group $group): Response
    {
        foreach ($group->getTeams()->toArray() as $team) {
            $group->removeTeam($team);
        }

        $this->em->remove($group);
        $this->em->flush();

        return new Response(content: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array{name: string, teamIds: int[]}
     *
     * @throws InvalidDataException
     */
    private function getEditData(Request $request): array
    {
        /** @var array{name: string, teamIds: int[]} $data */
        $data = $this->getAndValidData($request, ['name', 'teamIds'], 'group');

        return $data;
    }
}
