<?php

namespace App\Service\Charts;

use App\Entity\Game;
use App\Entity\User\UserGame;
use App\Repository\User\UserGameRepository;
use App\Service\JokerPoints;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @phpstan-type UserSeries array{
 *     userId:int,
 *     userName:string,
 *     userColor:string,
 *     data:list<float>
 * }
 * @phpstan-type MasterChart array{
 *     labels:list<string>,
 *     series:list<UserSeries>
 * }
 * @phpstan-type DayLabel array<string,string>
 * @phpstan-type DayChart array{
 *     labels:list<DayLabel>,
 *     series:list<UserSeries>
 * }
 */
class PointsChartLine
{
    private const string MASTER_CHART_KEY = 'master';
    private const string START_LABEL = 'start';
    private const string FILE_CONTENT_ROUTE = 'api_file_content';

    public function __construct(
        private UserGameRepository $userGameRepository,
        private UrlGeneratorInterface $urlGenerator,
        private JokerPoints $jokerPointsService,
    ) {
    }

    /**
     * @return array<string, MasterChart|DayChart>
     */
    public function getData(int $strategyId): array
    {
        $dataToReturn = [];
        [$gamesByDate, $usersMap, $dates] = $this->prepareData($strategyId);
        $jokerPoints = $this->jokerPointsService->getJokersPoints();
        $jokerPointsSet = [] !== $jokerPoints;

        // Initialize master key
        $dataToReturn[self::MASTER_CHART_KEY] = $this->buildMasterChart($dates, $usersMap, $gamesByDate, $jokerPoints);

        $lastDateKey = array_key_last($dates);
        foreach ($dates as $key => $date) {
            $dataToReturn[$date] = $this->buildDayChart(
                $gamesByDate[$date] ?? [],
                $usersMap,
                $jokerPointsSet
            );

            if ($key === $lastDateKey && $jokerPointsSet) {
                $this->appendJokerPoints(
                    $dataToReturn[$date]['series'],
                    $jokerPoints,
                );
            }

            $dataToReturn[$date]['series'] = array_values($dataToReturn[$date]['series']);
        }

        return $dataToReturn;
    }

    /**
     * @return array{
     *     0: array<string, UserGame[]>,
     *     1: array<int, UserSeries>,
     *     2: array<int, string>
     * }
     */
    private function prepareData(int $strategyId): array
    {
        $userGames = $this->userGameRepository->findWithPoints($strategyId);

        // Extract all unique dates and group games by date
        $gamesByDate = [];

        $usersMap = [];

        foreach ($userGames as $userGame) {
            $user = $userGame->getUser();
            $usersMap[$user->getId()] ??= [
                'userId' => $user->getId(),
                'userName' => $user->getName(),
                'userColor' => $user->getColor(),
                'data' => [0 => 0.0],
            ];

            $dateKey = $userGame->getGame()->getDate()->format('d-m');
            $gamesByDate[$dateKey] ??= [];
            $gamesByDate[$dateKey][] = $userGame;
        }

        $dates = array_keys($gamesByDate);
        // Sort dates from oldest to newest
        sort($dates);

        return [$gamesByDate, $usersMap, $dates];
    }

    /**
     * @param array<int, string>        $dates
     * @param array<int, UserSeries>    $usersMap
     * @param array<string, UserGame[]> $gamesByDate
     * @param array<int, int>           $jokerPoints
     *
     * @return MasterChart
     */
    private function buildMasterChart(array $dates, array $usersMap, array $gamesByDate, array $jokerPoints): array
    {
        // Track cumulative points per user
        $userCumulativePoints = [];

        // Build cumulative points for master series
        foreach ($dates as $date) {
            if (isset($gamesByDate[$date])) {
                foreach ($gamesByDate[$date] as $userGame) {
                    $userId = $userGame->getUser()->getId();

                    if (!isset($userCumulativePoints[$userId])) {
                        $userCumulativePoints[$userId] = 0.0;
                    }

                    $userCumulativePoints[$userId] += $userGame->getPoints();
                }
            }

            // Add cumulative points for each user at this date
            foreach ($usersMap as $userId => $userData) {
                $usersMap[$userId]['data'][] = ($userCumulativePoints[$userId] ?? 0.0) + ($jokerPoints[$userId] ?? 0.0);
            }
        }

        return [
            'labels' => array_merge([0 => self::START_LABEL], $dates),
            'series' => array_values($usersMap),
        ];
    }

    /**
     * @param UserGame[]             $userGames
     * @param array<int, UserSeries> $usersMap
     *
     * @return DayChart
     */
    private function buildDayChart(
        array $userGames,
        array $usersMap,
        bool $jokerPointsSet,
    ): array {
        if ([] === $userGames) {
            return [
                'labels' => [],
                'series' => [],
            ];
        }

        $games = $this->groupGames($userGames);

        return [
            'labels' => $this->buildLabels($games, $jokerPointsSet),
            'series' => $this->buildSeries($games, $usersMap),
        ];
    }

    /**
     * @param UserGame[] $userGames
     *
     * @return array<int, array{game: Game, userGames: UserGame[]}> >
     */
    private function groupGames(array $userGames): array
    {
        $games = [];

        foreach ($userGames as $userGame) {
            $gameId = $userGame->getGame()->getId();
            $games[$gameId] ??= [
                'game' => $userGame->getGame(),
                'userGames' => [],
            ];

            $games[$gameId]['userGames'][] = $userGame;
        }

        return $games;
    }

    /**
     * @param array<int, array{game: Game, userGames: UserGame[]}> $games
     *
     * @return list<array<string,string>>
     */
    private function buildLabels(array $games, bool $jokerPointsSet): array
    {
        $labels = [[]];

        foreach ($games as $gameData) {
            $game = $gameData['game'];
            $homeTeamLogoId = $game->getHomeTeam()->getLogo()?->getId();
            $awayTeamLogoId = $game->getAwayTeam()->getLogo()?->getId();

            $labels[] = [
                'home_team_name' => $game->getHomeTeam()->getName(),
                'home_team_logo_url' => $homeTeamLogoId ? $this->getLogoUrl($homeTeamLogoId) : '',
                'away_team_name' => $game->getAwayTeam()->getName(),
                'away_team_logo_url' => $awayTeamLogoId ? $this->getLogoUrl($awayTeamLogoId) : '',
            ];
        }
        if ($jokerPointsSet) {
            $labels[] = [
                'special' => 'Joker',
            ];
        }

        return $labels;
    }

    private function getLogoUrl(int $logoId): string
    {
        return $this->urlGenerator->generate(self::FILE_CONTENT_ROUTE, ['id' => $logoId]);
    }

    /**
     * @param array<int, array{game: Game, userGames: UserGame[]}> $games
     * @param array<int, UserSeries>                               $usersMap
     *
     * @return array<int, UserSeries>
     */
    private function buildSeries(array $games, array $usersMap): array
    {
        // Build series with cumulative points per match on this date
        $dateUserSeries = [];
        $dateUserCumulative = [];

        foreach (array_values($games) as $key => $gameData) {
            /** @var UserGame $userGame */
            foreach ($gameData['userGames'] as $userGame) {
                $user = $userGame->getUser();
                $userId = $user->getId();

                if (!isset($dateUserSeries[$userId])) {
                    $dateUserSeries[$userId] = [
                        'userId' => $userId,
                        'userName' => $user->getName(),
                        'userColor' => $user->getColor(),
                        'data' => [0 => 0.0],
                    ];
                    $dateUserCumulative[$userId] = 0.0;
                }

                $dateUserCumulative[$userId] += $userGame->getPoints();
                $dateUserSeries[$userId]['data'][] = $dateUserCumulative[$userId];
            }

            // Fill missing users with cumulative zero
            $this->fillMissingPoints($dateUserSeries, $dateUserCumulative, $key);
            $this->fillMissingUsers($dateUserSeries, $dateUserCumulative, $usersMap);
        }

        return $dateUserSeries;
    }

    /**
     * @param array<int, UserSeries> $series
     * @param array<int, int>        $jokerPoints
     */
    private function appendJokerPoints(array &$series, array $jokerPoints): void
    {
        foreach ($series as &$userSeries) {
            $lastValue = end($userSeries['data']);

            $userSeries['data'][] = $lastValue + ($jokerPoints[$userSeries['userId']] ?? 0.00);
        }

        unset($userSeries);
    }

    /**
     * @param array<int, UserSeries> $dateUserSeries
     * @param array<int, float>      $dateUserCumulative
     */
    private function fillMissingPoints(array &$dateUserSeries, array $dateUserCumulative, int $key): void
    {
        foreach ($dateUserSeries as $userId => $series) {
            if (count($series['data']) < $key + 2) {
                $dateUserSeries[$userId]['data'][] = $dateUserCumulative[$userId] ?? 0.0;
            }
        }
    }

    /**
     * @param array<int, UserSeries> $dateUserSeries
     * @param array<int, float>      $dateUserCumulative
     * @param array<int, UserSeries> $usersMap
     */
    private function fillMissingUsers(array &$dateUserSeries, array &$dateUserCumulative, array $usersMap): void
    {
        foreach ($usersMap as $user) {
            if (!isset($dateUserSeries[$user['userId']])) {
                $dateUserSeries[$user['userId']] = [
                    'userId' => $user['userId'],
                    'userName' => $user['userName'],
                    'userColor' => $user['userColor'],
                    'data' => [0 => 0.0, 1 => 0.0],
                ];
                $dateUserCumulative[$user['userId']] = 0.0;
            }
        }
    }
}
