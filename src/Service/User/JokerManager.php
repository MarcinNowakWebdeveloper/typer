<?php

namespace App\Service\User;

use App\Entity\User;
use App\Entity\User\Joker;
use App\Exception\InvalidDataException;
use App\Repository\TeamRepository;
use App\Repository\User\JokerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class JokerManager
{
    public function __construct(
        private JokerRepository $jokerRepository,
        private TeamRepository $teamRepository,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param array{teamId: int} $data
     *
     * @throws InvalidDataException
     */
    public function updateFromArray(User $user, array $data): Joker
    {
        $joker = $this->jokerRepository->findOneByUser($user);
        if (!$joker) {
            $joker = new Joker();
            $joker->setUser($user);
            $this->em->persist($joker);
        }

        $team = $this->teamRepository->find($data['teamId']);
        if (!$team) {
            $objectName = $this->translator->trans('joker.teamId', [], 'entities');
            $message = $this->translator->trans('form.notFound', ['{object}' => $objectName], 'errors');
            throw new InvalidDataException(message: $message, code: Response::HTTP_NOT_FOUND);
        }

        $joker->setTeam($team);

        return $joker;
    }
}
