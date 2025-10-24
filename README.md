This package provides automated refactoring rules for [Rector](https://getrector.com/) that are likely to produce risky
updates.

The Rector project are committed to shipping rules that can be trusted to run safely (see
[this discussion](https://github.com/rectorphp/rector/discussions/9398#discussioncomment-14582690) and
[this blog](https://getrector.com/blog/new-in-rector-015-complete-safe-and-known-type-declarations#content-known-and-safe-types-first-only).

This library exists for the times where you **want** to perform unsafe refactorings. You can use these rules to automate
the grunt work of modifying files, so you can instead spend your time carefully reviewing the changes.

## When would I use these rules?

* When you are preparing a major breaking release of a library
* When you are beginning a major modernisation of a project

You should also have:

* Comprehensive test coverage.
* A static analyser (e.g. phpstan) and have resolved any issues it identifies.
* Installed and run Rector with the standard / safe rules so that your code is modernised based on concrete types
  wherever possible.

We **do not** recommend using this package in your project long-term. The rules are intended to be for one-time
modernisation - you should install it, configure it, run it & then remove it.

## Installing and running the rules

Install the package with composer: `composer require ingenerator/risky-rector-rules`.

Ensure you have a clean working directory, and have already run and committed the changes from your existing Rector
configuration.

Choose the rules you wish to use and add them to your `rector.php` config file:

```php
return RectorConfig::configure()
    // ... your existing Rector config
    ->withRules([
      \Ingenerator\RiskyRectorRules\PhpDocToStrictTypes\AddParamTypeFromPhpDocRector::class,
      \Ingenerator\RiskyRectorRules\PhpDocToStrictTypes\AddPropertyTypeFromPhpDocRector::class,
      \Ingenerator\RiskyRectorRules\PhpDocToStrictTypes\AddReturnTypeFromPhpDocRector::class,
    ]);
```

Run Rector `vendor/bin/rector process`, carefully review the result, and commit the changes.

## Rules provided

### PhpDocToStrictTypes

This group of rules will add strict types throughout your code based on existing phpdoc tags. Any redundant phpdoc will
be removed as part of this process.

If your project is a library, these rules are almost guaranteed to produce breaking changes in your public API.

If you have a reasonable level of phpstan coverage proving that the phpdoc types in your codebase are correct, these
changes may be relatively safe. Relatively...!

| Name                            | Purpose                                                     |
|---------------------------------|-------------------------------------------------------------|
| AddParamTypeFromPhpDocRector    | Adds strict types to method parameters based on @param tags |
| AddPropertyTypeFromPhpDocRector | Adds strict return types to properties based on @var tags   |
| AddReturnTypeFromPhpDocRector   | Adds strict return types to methods based on @return tags   |

## Support this library

If you find this library useful, [please consider sponsoring my Open Source work](https://github.com/sponsors/acoulton).
One-time and recurring sponsorship helps me to spend time working on Open Source projects.

## Credits

The initial implementation of the AddParamTypeFromPhpDocRector was based on [a contribution by Michael Strelan to the
Drupal project](https://git.drupalcode.org/project/drupal/-/blob/cf322b485db62ec80680bd7148c576f20877ab36/core/lib/Drupal/Core/Rector/AddParamTypeFromPhpDocRector.php)
from March 2024.

At the time, that contribution had not been accepted and the implementation was no longer functional against current
Rector versions. Additionally, their implementation differed in a number of respects from my desired requirements.

The implementation shipped in this library has therefore diverged quite significantly from the original inspiration, but
Michael's work provided a useful starting point and so I'm very happy to credit it.

## Contributing

Contributions - whether changes or new rules - are very welcome. However, we strongly recommend opening an issue to
discuss your idea before working on any code. This will avoid wasting any time if your proposal does not fit with our
vision for the library.

If you do decide to contribute, you should follow our coding style and add tests for every change.

## Licence

Licensed under the [BSD-3-Clause Licence](LICENCE.md)
