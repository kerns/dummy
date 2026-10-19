# Changelog

## 2.0.0 — 2026-10-19

- Add generated lorem ipsum with exact or ranged word, sentence, and paragraph counts and an optional title-case modifier, without a new dependency.
- Modernize the demo with blueprint colors, a graph-paper header, local syntax highlighting, and image thumbnails.
- Include complete Composer, Laravel/Blade, and standalone PHP setup instructions in the demo, with high-density image examples.
- Add a static demo exporter and GitHub Pages workflow, including the selected images and required attribution files.
- Support PHP 8.3–8.5 and declare the GD extension requirement.
- Preserve the existing global functions, command strings, output behavior, and drop-in installation.
- Add Composer function autoloading and automatic Laravel service-provider discovery.
- Add Laravel asset/config publishing and Blade installation examples.
- Remove native session startup and application memory-limit changes.
- Cache directory scans and text pools per request, and crop/resize images in one GD pass.
- Write cached crops atomically; distinguish same-name images in different folders and invalidate crops when source timestamps/sizes change.
- Fix obsolete PHP syntax, short-tag-dependent bundled templates, and PHP 8.5 GD deprecations.
- Handle missing assets and prevent asset selection outside the configured root.
- Integrate query/path helpers and reset request-local state with Laravel's request lifecycle.
- Add PHP/Laravel compatibility tests and a GitHub Actions matrix.
