<?php

namespace App\Controller\Api\Admin\Config;

use App\Controller\AbstractBaseController;
use App\Entity\File;
use App\Entity\Team;
use App\Exception\InvalidDataException;
use App\Service\FileService;
use App\Service\TeamManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/admin/config/team')]
class TeamController extends AbstractBaseController
{
    public function __construct(
        private TeamManager $teamManager,
        private FileService $fileService,
        private EntityManagerInterface $em,
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    #[Route('/add', name: 'app_config_team_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $team = new Team();

            $data = $this->getEditData($request);
            $this->teamManager->updateFromArray($team, $data);
            $this->validate($team);

            $this->em->persist($team);
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

    #[Route(':{team}/edit', name: 'app_config_team_edit', methods: ['PATCH'])]
    public function edit(Request $request, Team $team): JsonResponse
    {
        try {
            $data = $this->getEditData($request);
            $this->teamManager->updateFromArray($team, $data);
            $this->validate($team);

            $this->em->flush();
        } catch (InvalidDataException $exception) {
            return $this->json(
                data: ['message' => $exception->getMessage()],
                status: $exception->getCode(),
            );
        }

        return $this->json(
            data: ['success' => true],
            status: Response::HTTP_OK
        );
    }

    #[Route(':{team}/remove', name: 'app_config_team_remove', methods: ['DELETE'])]
    public function remove(Team $team): Response
    {
        if ($team->getLogo()) {
            $this->fileService->remove($team->getLogo());
        }

        $this->em->remove($team);
        $this->em->flush();

        return new Response(content: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array{ name: string, image: ?File}
     *
     * @throws InvalidDataException
     */
    private function getEditData(Request $request): array
    {
        $data['name'] = trim($request->request->get('name'));

        if (empty($data['name'])) {
            $message = $this->translator->trans('form.required.field', ['{required}' => $this->translator->trans('team.name', [], 'entities')], 'errors');

            throw new InvalidDataException(message: $message, code: Response::HTTP_BAD_REQUEST);
        }

        $data['image'] = null;

        $uploadedFile = $request->files->get('image');
        if (!$uploadedFile) {
            return $data;
        }

        $data['image'] = $this->fileService->upload(
            $uploadedFile,
            [
                'image/jpeg',
                'image/png',
                'image/webp',
            ],
        );

        return $data;
    }
}
