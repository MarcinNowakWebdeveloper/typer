<?php

namespace App\Controller;

use App\Exception\InvalidDataException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractBaseController extends AbstractController
{
    protected TranslatorInterface $translator;
    protected ValidatorInterface $validator;

    /**
     * @param array<int, string> $keys
     *
     * @return array<string, string>
     *
     * @throws InvalidDataException
     */
    protected function getAndValidData(Request $request, array $keys, string $entity): array
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            $message = $this->translator->trans('api.wrongData', [], 'errors');
            throw new InvalidDataException(message: $message, code: Response::HTTP_BAD_REQUEST);
        }

        $data = [];
        $emptyRequired = [];

        foreach ($keys as $key) {
            $value = ($payload[$key] ?? '');
            if (!is_array($value)) {
                $value = trim((string) $value);
            }
            $data[$key] = $value;
            if ('' === $value) {
                $emptyRequired[] = $this->translator->trans($entity.'.'.$key, [], 'entities');
            }
        }

        if (!empty($emptyRequired)) {
            $message = 'form.required.field';
            if (count($emptyRequired) > 1) {
                $message = 'form.required.fields';
            }
            $message = $this->translator->trans($message, ['{required}' => implode(', ', $emptyRequired)], 'errors');

            throw new InvalidDataException(message: $message, code: Response::HTTP_BAD_REQUEST);
        }

        return $data;
    }

    /**
     * @throws InvalidDataException
     */
    protected function validate(object $object): void
    {
        $errors = $this->validator->validate($object);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }

            throw new InvalidDataException(message: implode("\n", $messages), code: Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
