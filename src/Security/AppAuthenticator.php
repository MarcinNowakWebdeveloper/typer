<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return '/api/login' === $request->getPathInfo()
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $data = $this->getAndValidData($request);

        return new Passport(
            new UserBadge($data['email'], function ($userIdentifier) {
                $user = $this->userRepository->findOneBy([
                    'email' => $userIdentifier,
                ]);

                if (!$user) {
                    $message = $this->translator->trans('auth.login.pass', [], 'errors');
                    throw new CustomUserMessageAuthenticationException($message);
                }

                if (!$user->isVerified()) {
                    $message = $this->translator->trans('auth.login.notVerified', [], 'errors');
                    throw new CustomUserMessageAuthenticationException($message);
                }

                if (!$user->isActive()) {
                    $message = $this->translator->trans('auth.login.notActive', [], 'errors');
                    throw new CustomUserMessageAuthenticationException($message);
                }

                return $user;
            }),

            new PasswordCredentials($data['password'])
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName,
    ): ?Response {
        /** @var User $user */
        $user = $token->getUser();

        return new JsonResponse([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserIdentifier(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception,
    ): ?Response {
        return new JsonResponse([
            'success' => false,
            'message' => $this->translator->trans($exception->getMessage(), [], 'security'),
        ], 401);
    }

    /**
     * @return array{email: string, password: string}
     */
    private function getAndValidData(Request $request): array
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            $message = $this->translator->trans('api.wrongData', [], 'errors');
            throw new CustomUserMessageAuthenticationException(message: $message);
        }

        $data = [];
        $emptyRequired = [];

        foreach (['email', 'password'] as $key) {
            $value = ($payload[$key] ?? '');
            if (!is_array($value)) {
                $value = trim((string) $value);
            }
            $data[$key] = $value;
            if ('' === $value) {
                $emptyRequired[] = $this->translator->trans('user.'.$key, [], 'entities');
            }
        }

        if (!empty($emptyRequired)) {
            $message = 'form.required.field';
            if (count($emptyRequired) > 1) {
                $message = 'form.required.fields';
            }
            $message = $this->translator->trans($message, ['{required}' => implode(', ', $emptyRequired)], 'errors');

            throw new CustomUserMessageAuthenticationException(message: $message);
        }

        return $data;
    }
}
