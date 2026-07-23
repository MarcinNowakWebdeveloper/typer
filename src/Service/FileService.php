<?php

namespace App\Service;

use App\Entity\File;
use App\Exception\InvalidFileMimeTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class FileService
{
    public function __construct(
        #[Autowire(env: 'resolve:UPLOAD_DIRECTORY')]
        private string $uploadDirectory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param string[] $allowedMimeTypes
     */
    public function upload(
        UploadedFile $uploadedFile,
        array $allowedMimeTypes,
    ): File {
        $mimeType = $uploadedFile->getMimeType();

        if (
            null === $mimeType
            || !in_array($mimeType, $allowedMimeTypes, true)
        ) {
            throw new InvalidFileMimeTypeException(sprintf('Mime type "%s" is not allowed.', $mimeType ?? 'unknown'));
        }

        do {
            $fileName = bin2hex(random_bytes(32));
        } while (file_exists($this->uploadDirectory.'/'.$fileName));

        $uploadedFile->move(
            $this->uploadDirectory,
            $fileName,
        );

        $file = new File();
        $file->setName($fileName);
        $file->setOriginName(
            $uploadedFile->getClientOriginalName(),
        );
        $file->setMimeType($mimeType);

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    public function remove(File $file): void
    {
        if (!file_exists($this->uploadDirectory.'/'.$file->getName())) {
            return;
        }

        unlink($this->uploadDirectory.'/'.$file->getName());
    }
}
