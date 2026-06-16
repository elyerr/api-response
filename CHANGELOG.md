# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog], and this project adheres to Semantic Versioning.

## [v2.0.0]

### Added

* Added `BooleanRule` for boolean value validation and normalization.
* Added `MoneyRule` for validating and converting monetary values into integer cents.
* Added `StringOnlyRule` for validating string-only inputs.
* Added `UndefinedValues` validation rule.
* Added `ReportError` exception for standardized API error reporting.
* Added structured exception logging with request, route, user, network, and server context.
* Added automatic sanitization of sensitive data in logs.
* Added `generateRandomString()` helper.
* Added `generateUniqueCode()` helper.
* Added `format_date()` helper with timezone support via `X-LOCALTIME` header.
* Added `format_money()` helper.
* Added `normalizeMoneyToCents()` helper.
* Added `verify_time_is_between()` helper.
* Added `transformConfigRequest()` helper for flattening nested arrays using dot notation.
* Added `array_count_dimension()` helper.
* Added `transformModel()` helper for Fractal model transformations.
* Added `transformCollection()` helper for Fractal collection transformations.
* Added `api-response:install` Artisan command.
* Added `api-response:transformer` Artisan command with custom transformer stub support.

### Changed

* Improved API response standardization through the `JsonResponser` trait.
* Improved transformer generation workflow.
* Improved package structure and developer experience.
* Updated package documentation.
* Updated compatibility with OAuth2 Passport Server v7+.

### Fixed

* Added automatic `204 No Content` responses for empty resources when applicable.
* Fixed helper autoload registration.
* Fixed transformer command registration.

### Compatibility

* Laravel 12+
* OAuth2 Passport Server v7.0+
