# laravel-compat

Static compatibility audit for legacy Laravel/PHP projects. Point it at a
project directory, get a `build/report.md` you can hand to a client and a
`build/phpcs.json` you can feed to other tools.

## What it does / what it does not

- does: runs PHPCompatibility via PHP_CodeSniffer over your source,
  aggregates findings into one Markdown report, plus the raw phpcs JSON.
- does not: no Laravel framework-level coverage (removed facades, methods,
  config keys are NOT checked); no auto-fixing; no web UI; no CI integration.

Laravel framework-level removals are not covered.

## Requirements

PHP >= 8.2 and Composer. The compatibility target defaults to 8.0
(`--php=8.0`), which is the only target validated so far; higher targets are
untested against the bundled PHPCompatibility 9.x ruleset.

## Install

```
composer require laravel-compat/laravel-compat
```

## Usage

```
vendor/bin/scan <path> [--php=8.0]
```

Example:

```
vendor/bin/scan ./app
```

Outputs `build/phpcs.json` and `build/report.md`, written to the root directory
that owns the project's `vendor/`.

## Sample output

See [docs/sample-report.md](docs/sample-report.md) for a real, sanitized
report run. A small excerpt of the Markdown table:

| # | File:Line | Type | Rule | Finding |
| --- | --- | --- | --- | --- |
| 1 | `app/Http/Controllers/Admin/System/DictController.php:84` | WARNING | `PHPCompatibility.ParameterValues.RemovedImplodeFlexibleParamOrder.Deprecated` | Passing the $glue and $pieces parameters in reverse order to implode has been deprecated since PHP 7.4; $glue should be the first parameter and $pieces the second |
| 2 | `app/Http/Controllers/Admin/System/DictController.php:113` | WARNING | `PHPCompatibility.ParameterValues.RemovedImplodeFlexibleParamOrder.Deprecated` | Passing the $glue and $pieces parameters in reverse order to implode has been deprecated since PHP 7.4; $glue should be the first parameter and $pieces the second |
| 3 | `routes/console.php:18` | WARNING | `PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass` | Closures / anonymous functions only have access to $this if used within a class or when bound to an object using bindTo(). Please verify. |

The report body is Chinese by design. Rule names and messages are passed
through unchanged from PHPCompatibility.

Honest disclosures:

- The sanitized sample comes from a 264-file Laravel 8 project scanned for
  PHP 8.0; it produced 3 findings.
- 2 of the 3 were real (reversed `implode()` arguments, fatal on PHP 8.0);
  1 was a false positive (a closure using `$this` inside `Artisan::command`,
  which Laravel binds to the command instance).
- `.blade.php` files are excluded from scanning on purpose.
- Wording in the report only states what was detected; it does not claim a
  fix is required.

## License

MIT. See [LICENSE](LICENSE).
