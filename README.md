# Mini Framework

A tiny PHP framework built from scratch to explore and understand Laravel internals.

## Project Status

This project is in early development and is intended for educational purposes only. It is not ready for production use.

## Requirements

- PHP 8.5 or higher
- Composer 2

## Installation

Clone the repository and install its dependencies:

```bash
git clone https://github.com/mrksfon/mini-framework.git
cd mini-framework
composer install
```

## Development Commands

Run the complete quality check:

```bash
composer check
```

Or run each tool separately:

```bash
composer test
composer analyse
composer lint
composer refactor
```

Rector is optional and is not included in the default quality check.

## Roadmap

- [ ] v0.1.0 — Router
- [ ] v0.2.0 — Request, Response, and Controllers
- [ ] v0.3.0 — Dependency Injection Container and Middleware
- [ ] v0.4.0 — Service Providers, Configuration, and Environment
- [ ] v0.5.0 — Database Layer and Mini ORM
- [ ] v0.6.0 — Route Model Binding, Migrations, and Console Commands

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).