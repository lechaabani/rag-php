---
sidebar_position: 10
---

# Contributing

We welcome contributions to RAG-PHP! Here's how to get started.

## Development Setup

```bash
git clone https://github.com/rag-php/rag-php.git
cd rag-php
composer install
docker compose up -d
```

## Quality Standards

| Tool | Standard |
|------|----------|
| Code Style | PSR-12 via PHP-CS-Fixer |
| Static Analysis | PHPStan level 9 |
| Tests | PHPUnit 11, >95% coverage |
| Types | `strict_types=1`, readonly classes |

## Running Checks

```bash
# Tests
composer test

# Static analysis
composer analyse

# Code style check
composer cs-check

# Code style fix
composer cs-fix
```

## Pull Request Checklist

- [ ] Tests added/updated
- [ ] PHPStan level 9 passes
- [ ] PHP-CS-Fixer passes
- [ ] Documentation updated (if applicable)
- [ ] CHANGELOG.md updated

## Architecture Guidelines

1. **Interface-first** — Define the contract before the implementation
2. **Readonly by default** — Use `readonly` classes and properties
3. **No framework coupling** — Core must work standalone
4. **Test everything** — Every public method needs tests
