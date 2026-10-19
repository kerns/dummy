<?php

namespace Kerns\Dummy;

/** @internal Plain-text lorem ipsum generation, independent of published assets. */
final class Lorem
{
    private const SENTENCES = [
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
        'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
        'Praesent commodo cursus magna, vel scelerisque nisl consectetur et.',
        'Nullam quis risus eget urna mollis ornare vel eu leo.',
        'Maecenas faucibus mollis interdum.',
        'Cras justo odio, dapibus ac facilisis in, egestas eget quam.',
        'Vestibulum id ligula porta felis euismod semper.',
        'Integer posuere erat a ante venenatis dapibus posuere velit aliquet.',
        'Donec ullamcorper nulla non metus auctor fringilla.',
        'Aenean lacinia bibendum nulla sed consectetur.',
        'Curabitur blandit tempus porttitor.',
        'Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.',
        'Morbi leo risus, porta ac consectetur ac, vestibulum at eros.',
        'Etiam porta sem malesuada magna mollis euismod.',
        'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
        'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
        'Pellentesque ornare sem lacinia quam venenatis vestibulum.',
    ];

    public static function generate(string $unit, string $range, string $format = ''): ?string
    {
        $limit = match ($unit) {
            'words' => 10000,
            'sentences' => 1000,
            'paragraphs' => 100,
        };
        if (!in_array($format, ['', 'title'], true)
            || !preg_match('/\A(\d{1,5})(?:-(\d{1,5}))?\z/', $range, $matches)) {
            return null;
        }
        $min = (int) $matches[1];
        $max = (int) ($matches[2] ?? $min);
        if ($min > $limit || $max > $limit) {
            return null;
        }
        $count = rand(min($min, $max), max($min, $max));
        if ($count === 0) {
            return '';
        }
        if ($unit === 'words') {
            $words = [];
            while (count($words) < $count) {
                array_push($words, ...explode(' ', self::SENTENCES[array_rand(self::SENTENCES)]));
            }
            $text = rtrim(implode(' ', array_slice($words, 0, $count)), ',.;:') . '.';
        } elseif ($unit === 'sentences') {
            $text = self::sentences($count);
        } else {
            $paragraphs = [];
            for ($i = 0; $i < $count; $i++) {
                $paragraphs[] = self::sentences(rand(3, 6));
            }
            $text = implode("\n\n", $paragraphs);
        }
        // The built-in corpus is ASCII; no mbstring dependency is needed.
        return $format === 'title' ? ucwords(strtolower($text)) : $text;
    }

    private static function sentences(int $count): string
    {
        $sentences = [];
        $previous = null;
        for ($i = 0; $i < $count; $i++) {
            $index = $previous === null ? array_rand(self::SENTENCES)
                : ($previous + rand(1, count(self::SENTENCES) - 1)) % count(self::SENTENCES);
            $sentences[] = self::SENTENCES[$index];
            $previous = $index;
        }
        return implode(' ', $sentences);
    }
}
