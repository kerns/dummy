**This directory must be writable by PHP to generate and reuse image crops.**

Dummy creates the configured cache directory if it is missing. In a drop-in
installation this is `dummy/cache`; Laravel defaults to
`public/vendor/dummy/cache`. Generated files can be deleted to clear the cache.
Do not make the entire application or Composer vendor directory writable.
