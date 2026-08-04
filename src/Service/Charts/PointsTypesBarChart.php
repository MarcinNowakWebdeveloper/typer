<?php

namespace App\Service\Charts;

use App\Exception\InvalidDataException;
use App\Repository\PointCountingStrategyRepository;
use App\Repository\User\UserGamePointsRepository;
use App\Service\PointCountingStrategy\StrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PointsTypesBarChart
{
    /**
     * @param iterable<StrategyInterface> $strategies
     */
    public function __construct(
        #[AutowireIterator('app.scoring_strategy')]
        private iterable $strategies,
        private UserGamePointsRepository $userGamePointsRepository,
        private TranslatorInterface $translator,
        private PointCountingStrategyRepository $pointCountingStrategyRepository,
    ) {
    }

    /**
     * @return array{
     *     pointTypes: array<int, array{
     *          key: string,
     *          label: string,
     *          color: string
     *     }>,
     *     users: array<int, array{
     *          userId: int,
     *          userName: string,
     *          userColor: string,
     *     }>
     * }
     */
    public function getData(?int $strategyId): array
    {
        $data = [];
        $types = $this->getPointTypes($strategyId);
        $usersGamePointsCount = $this->userGamePointsRepository->countByTypeAndUserId($strategyId);

        foreach ($usersGamePointsCount as $userGamePoints) {
            if (!isset($data[$userGamePoints['id']])) {
                $data[$userGamePoints['id']] = [
                    'userId' => $userGamePoints['id'],
                    'userName' => $userGamePoints['name'],
                    'userColor' => $userGamePoints['color'],
                    'types' => [],
                ];
                foreach ($types as $type) {
                    $data[$userGamePoints['id']]['types'][$type] = 0;
                }
            }

            $data[$userGamePoints['id']]['types'][$userGamePoints['type']] = $userGamePoints['count'];
        }

        return [
            'pointTypes' => $this->getTypesData($types),
            'users' => $data,
        ];
    }

    /**
     * @param array<string> $types
     *
     * @return array<int, array{key: string, label: string, color: string}>
     */
    private function getTypesData(array $types): array
    {
        $typesData = [];
        foreach ($types as $type) {
            $typesData[] = [
                'key' => $type,
                'label' => $this->translator->trans('type.'.$type.'.label', [], 'pointsStrategy'),
                'color' => $this->translator->trans('type.'.$type.'.color', [], 'pointsStrategy'),
            ];
        }

        return $typesData;
    }

    /**
     * @return array<string>
     *
     * @throws \Exception
     */
    private function getPointTypes(?int $strategyId): array
    {
        $strategy = $this->pointCountingStrategyRepository->getByIdOrDefault($strategyId ?? null);
        foreach ($this->strategies as $scoringStrategy) {
            if ($scoringStrategy->getCode() === $strategy->getCode()) {
                return $scoringStrategy->getPointTypes();
            }
        }

        throw new InvalidDataException(message: $this->translator->trans('type.not_found', [], 'pointsStrategy'), code: Response::HTTP_NOT_FOUND);
    }
}
