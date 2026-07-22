<?php

namespace App\Service;

class UserColorGenerator
{
    private const float GOLDEN_RATIO = 0.618033988749895;

    public function generate(int $seed): string
    {
        $hue = fmod($seed * self::GOLDEN_RATIO, 1);

        return $this->hslToHex(
            $hue * 360,
            70,
            50
        );
    }

    private function hslToHex(float $h, float $s, float $l): string
    {
        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            [$r, $g, $b] = [$c, $x, 0];
        } elseif ($h < 120) {
            [$r, $g, $b] = [$x, $c, 0];
        } elseif ($h < 180) {
            [$r, $g, $b] = [0, $c, $x];
        } elseif ($h < 240) {
            [$r, $g, $b] = [0, $x, $c];
        } elseif ($h < 300) {
            [$r, $g, $b] = [$x, 0, $c];
        } else {
            [$r, $g, $b] = [$c, 0, $x];
        }

        return sprintf(
            '%d, %d, %d',
            (int) (($r + $m) * 255),
            (int) (($g + $m) * 255),
            (int) (($b + $m) * 255)
        );
    }
}
