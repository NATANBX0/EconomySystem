<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use pocketmine\utils\TextFormat;

final class TimeUtils
{

    const SECOND = 'Segundo';

    const MINUTE = 'Minuto';

    const HOUR = 'Hora';

    const DAY = 'Dia';

    const MONTH = 'Mês';

    private static $pluralNames = [
        self::SECOND => 'Segundos',
        self::MINUTE => 'Minutos',
        self::HOUR => 'Horas',
        self::DAY => 'Dias',
        self::MONTH => 'Meses'
    ];

    public static function isExpired(int $time): bool
    {
        return $time <= time();
    }

    public static function getMinute(int $count = 1): int
    {
        return $count * 60;
    }

    public static function getHour(int $count = 1): int
    {
        $hour = self::getMinute(60);
        return $count * $hour;
    }

    public static function getDay(int $count = 1): int
    {
        return $count * self::getHour(24);
    }

    public static function getMonth(int $count = 1): int
    {
        return $count * self::getDay(30);
    }

    public static function formatTime(int $time): string
    {
        $days = floor($time / 86400);
        $hours = floor(($time % 86400) / 3600);
        $minutes = floor(($time % 3600) / 60);
        $seconds = $time % 60;

        $formattedTime = '';
        if ($days > 0) {
            $formattedTime .= $days . 'd ';
        }
        if ($hours > 0) {
            $formattedTime .= $hours . 'h ';
        }
        if ($minutes > 0) {
            $formattedTime .= $minutes . 'm ';
        }
        if ($seconds > 0 || empty($formattedTime)) {
            $formattedTime .= $seconds . 's';
        }

        return trim($formattedTime);
    }

    public static function fromString(string $timestr, string $defaultTimeType = 'm'): int
    {
        $timestr = TextFormat::clean(trim($timestr));
        $timestr = strtolower($timestr);
        $numbers = '';
        $letters = '';
        foreach (str_split($timestr) as $str) {
            if (is_numeric($str)) {
                $numbers .= $str;
            } else {
                $letters .= $str;
            }
        }
        if (strlen($numbers) > 0) {
            $numbers = (int) $numbers;
            if ($numbers > 0) {
                $letters = (strlen($letters) > 0) ? $letters : $defaultTimeType;
                switch ($letters) {
                    case 'm':
                    case 'month':
                    case 'mês':
                    case 'mes':
                        return self::getMonth($numbers);
                        break;
                    case 'day':
                    case 'd':
                    case 'dia':
                    case 'dias':
                        return self::getDay($numbers);
                        break;
                    case 'min':
                    case 'minutes':
                    case 'minutos':
                    case 'minuto':
                        return self::getMinute($numbers);
                        break;
                    case 'segundos':
                    case 's':
                    case 'seconds':
                    case 'segundo':
                        return $numbers;
                        break;
                    case 'h':
                    case 'hour':
                    case 'hours':
                    case 'hora':
                    case 'horas':
                        return self::getHour($numbers);
                        break;
                }
            }
        }
        return -1;
    }

    public static function toString(int $time): string
    {
        if ($time >= 60) {
            $month = self::getMonth();
            $day = self::getDay();
            $hour = self::getHour();
            $minute = self::getMinute();

            $months = floor($time / $month);
            $days = floor(($time % $month) / $day);
            $hours = floor(($time % $day) / $hour);
            $minutes = floor(($time % $hour) / $minute);
            $seconds = floor($time % $minute);

            $times = [];
            if ($months > 0) {
                $times[self::MONTH] = $months;
            }

            if ($days > 0) {
                $times[self::DAY] = $days;
            }

            if ($hours > 0) {
                $times[self::HOUR] = $hours;
            }

            if ($days <= 0 && $months <= 0) {
                $times[self::MINUTE] = $minutes;
                $times[self::SECOND] = $seconds;
            }

        } else if ($time > 0) {
            $times = [self::SECOND => $time];
        } else {
            return 'Agora mesmo';
        }
        $times = array_filter(
            $times,
            static function ($value): bool {
                return $value > 0;
            }
        );
        $times = array_map(
            static function (string $name, $time): string {
                if ($time > 1) {
                    $name = self::$pluralNames[$name];
                }
                return "$time $name";
            },
            array_keys($times),
            array_values($times)
        );

        if (count($times) > 1) {
            $theLast = array_pop($times);
            $times[] = 'e ' . $theLast;
        }
        return implode(' ', $times);
    }
}