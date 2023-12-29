<?php

namespace Raccoon\Utils;

use Traversable;

class Debug {

    const RESET = "\033[0m";
    const RED = "\033[41m\033[37m"; // Background Red
    const GREEN = "\033[42m\033[37m"; // Background Green
    const YELLOW = "\033[43m\033[37m"; // Background Yellow
    const BLUE = "\033[44m\033[37m"; // Background Blue
    const WHITE = "\033[47m\033[30m"; // Background White
    const CYAN = "\033[46m\033[37m"; // Background Cyan
    const MAGENTA = "\033[45m\033[37m"; // Background Magenta

    public static function log($message) {
        self::printMessage("L", $message, self::WHITE);
    }

    public static function success($message) {
        self::printMessage("S", $message, self::GREEN);
    }

    public static function info($message) {
        self::printMessage("I", $message, self::BLUE);
    }

    public static function warning($message) {
        self::printMessage("W", $message, self::YELLOW);
    }

    public static function comment($message) {
        self::printMessage("C", $message, self::CYAN);
    }

    public static function error($message) {
        self::printMessage("E", $message, self::RED);
    }

    private static function printMessage($initial, $message, $color) {
        $timestamp = date(' [Y-m-d H:i:s] ');
        echo PHP_EOL;
        echo $color .' [' .$initial. '] '. self::RESET. $timestamp. self::RESET .': '. $message . PHP_EOL;
    }

    public static function object($data) {
        if (is_array($data) || $data instanceof Traversable) {
            self::printMessage("O", PHP_EOL.json_encode($data, JSON_PRETTY_PRINT), self::MAGENTA);
        } else {
            self::error('Invalid data type. Expected array or collection.');
        }
    }

    public static function table($data) {
        if (is_array($data) && !empty($data)) {
            $header = array_keys($data[0]);
            $rows = array_map('array_values', $data);

            $table = array_merge([$header], $rows);
            $tableString = self::buildTable($table);

            self::printMessage("T", PHP_EOL.$tableString, self::WHITE);
        } else {
            self::error('Invalid data type or empty array.');
        }
    }

    private static function buildTable($table) {
        $lines = [];

        $columnWidths = array_map('max', ...array_map(function ($column) {
            return array_map('strlen', $column);
        }, $table));


        // Header line
        $lines[] = '┌' . implode('┬', array_map(function ($width) {
                return str_repeat('─', $width + 2);
            }, $columnWidths)) . '┐';

        // Header
        $lines[] = '│ ' . implode(' │ ', array_map(function ($value, $width) {
                return str_pad($value, $width, ' ', STR_PAD_BOTH);
            }, $table[0], $columnWidths)) . ' │';

        // Separator line
        $lines[] = '├' . implode('┼', array_map(function ($width) {
                return str_repeat('─', $width + 2);
            }, $columnWidths)) . '┤';

        // Rows
        foreach (array_slice($table, 1) as $row) {
            $lines[] = '│ ' . implode(' │ ', array_map(function ($value, $width) {
                    return str_pad($value, $width, ' ', STR_PAD_BOTH);
                }, $row, $columnWidths)) . ' │';
        }

        // Footer line
        $lines[] = '└' . implode('┴', array_map(function ($width) {
                return str_repeat('─', $width + 2);
            }, $columnWidths)) . '┘';

        return implode(PHP_EOL, $lines);
    }
}