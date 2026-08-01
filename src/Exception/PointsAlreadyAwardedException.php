<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PointsAlreadyAwardedException extends BadRequestHttpException
{
    public function __construct(
    ) {
        parent::__construct('game.user_games.with_points');
    }
}
