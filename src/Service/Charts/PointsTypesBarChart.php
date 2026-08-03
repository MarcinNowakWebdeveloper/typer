<?php

namespace App\Service\Charts;

use App\Enum\FixedPointsScoringStrategyTypesEnum;
use App\Repository\User\UserGamePointsRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PointsTypesBarChart
{
    public function __construct(
        private UserGamePointsRepository $userGamePointsRepository,
        private TranslatorInterface $translator,
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
        $types = FixedPointsScoringStrategyTypesEnum::getValues();
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
}
