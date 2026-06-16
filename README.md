# API Response

**API Response** is a Laravel package that provides a collection of reusable tools commonly required when building APIs and backend services.

Instead of repeatedly implementing the same helpers, validation rules, transformers, response formatting, and utility functions across multiple projects, API Response centralizes them into a single package designed to improve consistency, maintainability, and developer productivity.

The package is actively used throughout the ElyerrLabs ecosystem, including OAuth2 Passport Server and related services.

---

## Features

### JSON API Responses

Provides a standardized way to return API responses using the included `JsonResponser` trait.

Benefits:

* Consistent API responses
* Standardized success and error formats
* Cleaner controllers
* Better frontend integration
* Reduced boilerplate code

Example:

```php
return $this->showOne($user);
```

```php
return $this->showAll($users);
```

```php
return $this->errorResponse(
    'Resource not found',
    404
);
```

---

### Custom Validation Rules

API Response includes reusable validation rules for common scenarios.

#### BooleanRule

Converts and validates boolean values received from forms, APIs, and external services.

Supported examples:

```text
true
false
1
0
```

---

#### MoneyRule

Validates monetary values and converts them into integer-based storage formats.

Example:

```text
10.50
```

Stored as:

```text
1050
```

This follows the same approach used by payment providers such as Stripe, helping prevent floating-point precision issues.

---

#### StringOnlyRule

Ensures a value contains only valid string content.

Useful for:

* Names
* Labels
* Titles
* Categories

---

#### UndefinedValues

Allows validation against values considered empty, undefined, or invalid for your application.

Useful when consuming data from external APIs or user-generated payloads.

---

### Transformers

Generate transformers using:

```bash
php artisan api-response:transformer UserTransformer
```

Transformers help separate data presentation from business logic and create consistent API responses.

Example:

```php
class UserTransformer extends Transformer
{
    public function transform(User $user)
    {
        return [
            'id' => (int) $user->id,
            'name' => $user->name,
        ];
    }
}
```

---

### Helper Functions

The package includes several utility helpers frequently required in Laravel applications.

Examples include:

* Temporary password generation
* Unique code generation
* Date formatting
* Money formatting
* Array transformations
* Request flattening
* Slug generation
* Kebab-case conversion
* Time range validation
* File manipulation helpers

Example:

```php
money_to_cents('10.50');
```

Result:

```text
1050
```

---

### Asset Utilities

The package provides reusable utility methods through the Asset helpers.

Common use cases:

* Password generation
* Unique identifiers
* Date formatting
* Array processing
* File operations
* String manipulation

These utilities are designed to eliminate repetitive code commonly found across Laravel projects.

---

### Exception Handling

Includes utilities for standardizing exception reporting and API error responses.

This helps maintain consistent error handling throughout your application.

---

## Installation

### Stable Version

```bash
composer require elyerr/api-response
```

### Development Version

```bash
composer require elyerr/api-response dev-main
```

---

## Artisan Commands

### Install Package Resources

Publish the default resources used by the package.

```bash
php artisan api-response:install
```

---

### Create a Transformer

Generate a new transformer using the package stub.

```bash
php artisan api-response:transformer UserTransformer
```

---

## Why Use API Response?

Most Laravel applications end up implementing the same functionality repeatedly:

* API response formatting
* Validation rules
* Transformers
* Utility helpers
* Date handling
* Money handling
* Data normalization
* Exception formatting

API Response centralizes these concerns into a single package, allowing developers to focus on business logic instead of rewriting infrastructure code.

---

## Extensible by Design

API Response is intentionally lightweight and extensible.

You can:

* Create your own validation rules
* Extend transformers
* Add custom helpers
* Customize response formats
* Integrate package features into your own architecture

The package is designed to adapt to your workflow rather than forcing a specific development style.

---

## License

AGPL-3.0
