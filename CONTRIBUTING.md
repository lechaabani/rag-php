# Contributing to RAG-PHP

Thank you for considering contributing to RAG-PHP! This document provides guidelines and instructions for contributing.

## Development Setup

```bash
# Clone the repository
git clone https://github.com/rag-php/rag-php.git
cd rag-php

# Install dependencies
composer install

# Start test infrastructure
docker compose up -d

# Run tests
composer test

# Run static analysis
composer analyse

# Check code style
composer cs-check
```

## Pull Request Process

1. **Fork** the repository and create your branch from `main`
2. **Write tests** for any new functionality
3. **Update documentation** if you're changing public APIs
4. **Run the full test suite** and ensure everything passes
5. **Follow PSR-12** coding standards (enforced by PHP-CS-Fixer)
6. **Write a clear PR description** explaining what and why

### PR Checklist

- [ ] Tests added/updated
- [ ] PHPStan level 9 passes
- [ ] PHP-CS-Fixer passes
- [ ] Documentation updated (if applicable)
- [ ] CHANGELOG.md updated

## Coding Standards

- PHP 8.2+ features: readonly classes, enums, DNF types, fibers
- `declare(strict_types=1)` in every file
- PSR-12 coding style
- PHPStan level 9 compliance
- Meaningful variable and method names

## Testing

- Unit tests: `packages/*/tests/Unit/`
- Integration tests: `packages/*/tests/Integration/`
- Minimum coverage: 95%

```bash
# Run all tests
composer test

# Run specific package tests
vendor/bin/phpunit --testsuite=Core
vendor/bin/phpunit --testsuite=OpenAI

# Generate coverage report
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html=coverage
```

## Reporting Bugs

Use [GitHub Issues](https://github.com/rag-php/rag-php/issues) with the bug report template. Include:

- PHP version and OS
- Steps to reproduce
- Expected vs actual behavior
- Relevant error messages or logs

## Feature Requests

Open a [GitHub Issue](https://github.com/rag-php/rag-php/issues) with the feature request template. Describe:

- The problem you're trying to solve
- Your proposed solution
- Alternative solutions you've considered

## Code of Conduct

Be respectful, inclusive, and constructive. We follow the [Contributor Covenant](https://www.contributor-covenant.org/).
