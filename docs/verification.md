# Verification report

Dummy 2.0.0, verified on 19 October 2026.

## PHP and Laravel matrix

Each environment used an isolated Linux container, a separate copy of the package, and a fresh Composer resolution. The containers used the official PHP images with GD enabled and Composer 2.10.3. These are actual PHP runtimes; the local Homebrew aliases were not used to simulate other versions.

| PHP | Laravel framework | Testbench | PHPUnit | Result |
| --- | --- | --- | --- | --- |
| 8.3.35 | 12.70.0 | 10.12.0 | 12.5.40 | 58 passed |
| 8.3.35 | 13.37.0 | 11.3.0 | 12.5.40 | 58 passed |
| 8.4.26 | 12.70.0 | 10.12.0 | 13.1.14 | 58 passed |
| 8.4.26 | 13.37.0 | 11.3.0 | 13.4.3 | 58 passed |
| 8.5.11 | 12.70.0 | 10.12.0 | 13.1.14 | 58 passed |
| 8.5.11 | 13.37.0 | 11.3.0 | 13.4.3 | 58 passed |

All six combinations passed the full suite: 348 test executions. Compatible dependency versions differ by environment. Every environment also passed Composer manifest validation, PHP linting, and the security advisory audit.

Coverage includes:

- Original helper commands, text-pool templates, configurable delimiters, and folder-only loading.
- Exact and ranged words, sentences, and paragraphs; title casing; zero counts; invalid values; output limits.
- Probability boundaries, nested loops, loop numbering, first/last/inner positions, and request helpers.
- JPEG/PNG/GIF crops, aspect ratios, center cropping, transparency, cache reuse/invalidation, and missing/outside paths.
- Ad markup and template overrides.
- Laravel publishing, Blade output, request-state isolation, and streamed responses.
- Static demo export, referenced resources, attribution files, project URL prefixes, repeat builds, and output-directory protection.

## Fresh application installations

A Composer ZIP archive of the package was extracted and installed into clean consumer projects. Composer consumers used a path repository with mirroring enabled, so their installed package files were independent copies of the distribution. The folder-only project copied just `dummy/` and had no Composer installation.

| Consumer | PHP | Laravel framework | Result |
| --- | --- | --- | --- |
| Plain PHP with Composer | 8.4.26 | — | Passed over HTTP |
| Plain PHP without Composer | 8.3.35 | — | Passed over HTTP |
| Fresh Laravel 12 application | 8.3.35 | 12.70.0 | Passed over HTTP |
| Fresh Laravel 13 application | 8.5.11 | 13.37.0 | Passed over HTTP |

The Laravel projects were created with `composer create-project laravel/laravel`. Both automatically discovered Dummy's provider, published assets and configuration, cached application configuration, and rendered a new Blade view through a real development HTTP server.

HTTP checks verified generated lorem counts and title casing, loop ranges/positions, 0%/100% probability behavior, query/path helpers, a served 96×54 image crop, served ad assets, and the absence of a native PHP session. The standalone consumers also preserved the application's memory limit.

## Dependency and distribution checks

- `composer update` resolved no newer compatible package versions in the main checkout.
- `composer outdated` reported no outdated installed packages in the main checkout.
- `composer audit` reported no security advisories or abandoned packages in the main checkout and the Composer test environments.
- Current direct development dependencies: Testbench 11.3.0 and PHPUnit 13.4.3. Current framework dependency: Laravel 13.37.0.
- Highlight.js 11.12.0 is the current upstream release, vendored with its BSD license. Dummy still has no third-party runtime dependency.
- The package archive includes the runtime, Laravel integration, assets, modern demo, and Highlight.js files. It excludes Composer dependencies, test fixtures, generated builds, and generated image caches.

## Hosted demo

`composer demo:build` creates a static snapshot in `build/demo`. Its URLs are relative, and all referenced local files are exported. No PHP files are included in the hosted bundle. The page states that it is a generated snapshot; PHP-driven randomization remains available when running locally.

The GitHub Pages workflow uses current official Actions versions and publishes [the demo](https://kerns.github.io/dummy/) after relevant changes reach `master`, or on manual dispatch.

## Limits

The Linux matrix and local macOS development environment were checked. Native Windows, Apache, PHP-FPM, third-party Laravel packages, and an actual long-running Octane worker were not exercised. Request rebinding and streamed responses are covered by the Laravel integration tests.
