<?php

namespace Kerns\Dummy\Tests;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Kerns\Dummy\DummyServiceProvider;
use Orchestra\Testbench\TestCase;

final class LaravelTest extends TestCase
{
    use CreatesAssets;

    protected function setUp(): void
    {
        $this->createAssets();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            $this->removeAssets();
        }
    }

    protected function getPackageProviders($app): array
    {
        return [DummyServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('dummy.url', '/vendor/dummy');
        $app['config']->set('dummy.assets_path', $this->directory . '/assets');
        $app['config']->set('dummy.cache_path', $this->directory . '/cache');
    }

    protected function defineRoutes($router): void
    {
        $router->get('/preview/{slug}', static function () {
            return Blade::render(<<<'BLADE'
                <h1>@php(dummy('text@headline'))</h1>
                @if(dumb_luck('100%'))<p>Always shown</p>@endif
                @if(dumb_luck('0%'))<p>Never shown</p>@endif
                <ul>
                @while(dumb_luck('3'))
                    <li>@php(dummy('text@headline'))</li>
                @endwhile
                </ul>
                <img src="@php(dummy('image/one@60x40'))" alt="Example">
                @if(dumb_question('url:mode=compact'))<p>Compact mode</p>@endif
                @if(dumb_question('segment:1=preview&2=story'))<p>Story route</p>@endif
                BLADE);
        });
    }

    public function test_original_helpers_work_in_blade_and_use_laravel_query_and_path(): void
    {
        $response = $this->get('/preview/story?mode=compact');
        $response->assertOk()->assertSee('A useful headline')->assertSee('Always shown')
            ->assertDontSee('Never shown')->assertSee('Compact mode')->assertSee('Story route')
            ->assertSee('/vendor/dummy/cache/');
        self::assertSame(3, substr_count($response->getContent(), '<li>'));
        $this->get('/preview/other')->assertOk()->assertDontSee('Compact mode')->assertDontSee('Story route');
        self::assertSame(PHP_SESSION_NONE, session_status());
    }

    public function test_laravel_requests_do_not_share_unfinished_loops(): void
    {
        $this->app['router']->get('/unfinished', static function () {
            dumb_luck('3');
            return 'done';
        });
        $this->get('/unfinished')->assertOk();
        $response = $this->get('/preview/story');
        $response->assertOk();
        self::assertSame(3, substr_count($response->getContent(), '<li>'));
    }

    public function test_generated_lorem_renders_through_blade(): void
    {
        $this->app['router']->get('/lorem', static fn () => Blade::render(
            '<p>@php(dummy("text@words:25"))</p><div>@php(dummy("text@paragraphs:2"))</div>',
        ));
        $response = $this->get('/lorem');
        $response->assertOk()->assertDontSee('Dummy:');
        preg_match('~<p>(.*?)</p><div>(.*?)</div>~s', $response->getContent(), $matches);
        self::assertCount(25, explode(' ', $matches[1]));
        self::assertCount(2, explode("\n\n", $matches[2]));
    }

    public function test_artisan_publishes_the_bundled_assets(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'dummy-assets', '--force' => true])->assertSuccessful();
        self::assertFileExists($this->directory . '/assets/text/time-ago.txt');
        self::assertFileExists($this->directory . '/assets/image/landscape/pingpong_foxxyz.jpg');
        $sources = array_map('realpath', array_keys(DummyServiceProvider::pathsToPublish(DummyServiceProvider::class, 'dummy-config')));
        self::assertContains(realpath(__DIR__ . '/../config/dummy.php'), $sources);
    }

    public function test_request_helpers_remain_available_while_sending_streamed_responses(): void
    {
        $this->app['router']->get('/stream/{slug}', static function () {
            return response()->stream(static function (): void {
                echo dumb_question('url:mode=compact') ? 'compact' : 'missing query';
                echo dumb_question('segment:2=story') ? ':story' : ':missing path';
            });
        });
        $request = Request::create('/stream/story?mode=compact');
        $kernel = $this->app->make(Kernel::class);
        $response = $kernel->handle($request);
        try {
            self::assertSame('compact:story', $this->captureOutput(fn () => $response->sendContent()));
        } finally {
            $kernel->terminate($request, $response);
        }
    }
}
