<?php

namespace Kerns\Dummy\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LoremTest extends TestCase
{
    use CreatesAssets;

    protected function setUp(): void
    {
        $this->createAssets();
    }

    protected function tearDown(): void
    {
        $this->removeAssets();
    }

    public static function exactCounts(): array
    {
        return [
            ['words', 1], ['words', 50], ['words', 10000],
            ['sentences', 1], ['sentences', 7], ['sentences', 1000],
            ['paragraphs', 1], ['paragraphs', 3], ['paragraphs', 100],
        ];
    }

    #[DataProvider('exactCounts')]
    public function test_exact_counts_generate_plain_text_without_assets(string $unit, int $count): void
    {
        $GLOBALS['dummy_assets_path'] = $this->directory . '/unpublished';
        $output = $this->captureOutput(fn () => dummy("text@$unit:$count"));
        self::assertSame($count, $this->countUnits($unit, $output));
        self::assertSame($output, strip_tags($output));
        self::assertMatchesRegularExpression('/\A[A-Z].*\.\z/s', $output);
        if ($unit === 'paragraphs') {
            foreach (explode("\n\n", $output) as $paragraph) {
                $sentences = $this->countUnits('sentences', $paragraph);
                self::assertGreaterThanOrEqual(3, $sentences);
                self::assertLessThanOrEqual(6, $sentences);
            }
        }
    }

    public function test_ranges_are_inclusive_and_reversed_ranges_work(): void
    {
        foreach (['words', 'sentences', 'paragraphs'] as $unit) {
            foreach (['2-4', '4-2', '0-4'] as $range) {
                for ($i = 0; $i < 20; $i++) {
                    $count = $this->countUnits($unit, $this->captureOutput(fn () => dummy("text@$unit:$range")));
                    self::assertGreaterThanOrEqual($range === '0-4' ? 0 : 2, $count);
                    self::assertLessThanOrEqual(4, $count);
                }
            }
            self::assertSame(3, $this->countUnits($unit, $this->captureOutput(fn () => dummy("text@$unit:3-3"))));
            self::assertSame('', $this->captureOutput(fn () => dummy("text@$unit:0")));
            self::assertSame('', $this->captureOutput(fn () => dummy("text@$unit:0-0")));
        }
    }

    public static function invalidCounts(): array
    {
        return array_map(static fn ($command) => [$command], [
            'words:', 'words:-1', 'words:1.5', 'words:five', 'words:5-', 'words:1-2-3',
            'words:10001', 'words:5-10001', 'words:999999999999999999999',
            'sentences:1001', 'paragraphs:101', "words:5\n", 'words:<script>',
            'words:5,unknown', 'words:5,title,other',
        ]);
    }

    #[DataProvider('invalidCounts')]
    public function test_invalid_counts_use_dummy_errors_without_partial_text(string $command): void
    {
        $output = $this->captureOutput(fn () => dummy('text@' . $command));
        self::assertStringStartsWith('<em>Dummy:', $output);
        self::assertStringEndsWith('</em>', $output);
        self::assertStringNotContainsString('<script>', $output);
    }

    public function test_existing_named_pools_and_configurable_delimiters_still_work(): void
    {
        file_put_contents($this->directory . '/assets/text/words.txt', 'A named word pool');
        file_put_contents($this->directory . '/assets/text/paragraphs.txt', 'A named paragraph pool');
        self::assertSame('A named word pool', $this->captureOutput(fn () => dummy('text@words')));
        self::assertSame('A named paragraph pool', $this->captureOutput(fn () => dummy('text@paragraphs')));
        self::assertSame('A useful headline', $this->captureOutput(fn () => dummy('text@headline')));
        $GLOBALS['delimiter'] = '|';
        self::assertSame(12, $this->countUnits('words', $this->captureOutput(fn () => dummy('text|words:12'))));
    }

    public function test_optional_title_case_capitalizes_words_without_changing_counts_or_paragraph_breaks(): void
    {
        $words = $this->captureOutput(fn () => dummy('text@words:7,title'));
        self::assertSame(7, $this->countUnits('words', $words));
        $paragraphs = $this->captureOutput(fn () => dummy('text@paragraphs:2,title'));
        self::assertSame(2, $this->countUnits('paragraphs', $paragraphs));
        foreach (preg_split('/\s+/', $words . ' ' . $paragraphs) as $word) {
            self::assertMatchesRegularExpression('/\A[A-Z][a-z]*[,.;]?\z/', $word);
        }
        self::assertSame('', $this->captureOutput(fn () => dummy('text@words:0,title')));
        $normal = $this->captureOutput(fn () => dummy('text@words:50'));
        self::assertMatchesRegularExpression('/\s[a-z]/', $normal);
    }

    private function countUnits(string $unit, string $output): int
    {
        if ($output === '') {
            return 0;
        }
        return match ($unit) {
            'words' => count(preg_split('/\s+/', trim($output))),
            'sentences' => substr_count($output, '.'),
            'paragraphs' => count(explode("\n\n", $output)),
        };
    }
}
