# Contributing to Dummy

## Development and verification

Install development dependencies and run the checks from the repository root:

```sh
composer install
composer validate --strict
composer lint
composer test
composer audit
```

The suite covers legacy helpers, generated lorem and title casing, nested loops and positions, query/path helpers, image cropping/transparency/caching, ads, standalone loading, Laravel publishing and Blade rendering, and the static demo export. The [Tests workflow](.github/workflows/tests.yml) resolves compatible dependencies for PHP 8.3–8.5 with Laravel 12 and 13.

See [the verification report](docs/verification.md) for tested versions, fresh-project installation results, and coverage limits.

## Maintaining the hosted demo

Edit `demo.php` and its assets in `demo_files/`. Preview the PHP demo locally:

```sh
php -S localhost:8000
```

Open [localhost:8000/demo.php](http://localhost:8000/demo.php). PHP needs the GD extension and write access to `dummy/cache`, or permission to create that directory.

The [hosted demo](https://kerns.github.io/dummy/) is a static snapshot. PHP runs during the build; the exported site contains HTML, CSS, JavaScript, and the selected images. It includes a note explaining that fresh random content requires running PHP locally.

Build and preview the static export:

```sh
composer demo:build
php -S localhost:8001 -t build
```

Open [localhost:8001/demo/](http://localhost:8001/demo/). The export also needs the PHP DOM extension. It uses relative resource URLs so it works below a project URL path.

The [Demo workflow](.github/workflows/demo.yml) rebuilds and publishes the site when demo-related changes reach `master`. It can also be run manually from GitHub Actions. Keep image credits and license files in the exported site when changing the build.
