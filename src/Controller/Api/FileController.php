<?php

namespace App\Controller\Api;

use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/file')]
class FileController extends AbstractController
{
    #[Route('/{id}', name: 'api_file_content', methods: ['GET'])]
    public function getFileContent(
        int $id,
        FileRepository $fileRepository,
        TranslatorInterface $translator,
        #[Autowire(env: 'resolve:UPLOAD_DIRECTORY')]
        string $uploadDirectory,
    ): Response {
        $file = $fileRepository->find($id);

        if (!$file) {
            return $this->json(
                data: ['message' => $translator->trans('api.file.notFound', [], 'errors')],
                status: Response::HTTP_NOT_FOUND
            );
        }

        $filePath = $uploadDirectory.'/'.$file->getName();

        if (!file_exists($filePath)) {
            return $this->json(
                data: ['message' => $translator->trans('api.file.notFoundOnDisk', [], 'errors')],
                status: Response::HTTP_NOT_FOUND
            );
        }

        $response = new Response(
            file_get_contents($filePath),
            Response::HTTP_OK,
            [
                'Content-Type' => $file->getMimeType() ?? 'application/octet-stream',
            ]
        );

        $response->setPublic();
        $response->setMaxAge(86400 * 30);
        $response->headers->set('Cache-Control', 'public, max-age=2592000');

        return $response;
    }
}
