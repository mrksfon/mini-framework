# Architectural Decisions

## ADR-001: Use PHP 8.5

**Status:** Accepted

### Context

This project is built from scratch for learning and has no legacy compatibility requirements.

### Decision

The framework requires PHP 8.5 or newer.

### Consequences

- Modern PHP language features may be used from the beginning.
- CI only needs to test PHP 8.5 initially.
- Users with older PHP versions cannot install the project.

## ADR-002: Store Framework Source Code in `src/`

**Status:** Accepted

### Context

The framework initially stored its production classes in `core/`. As the
project grows, its source layout should remain conventional and immediately
recognizable to PHP library and framework developers.

### Decision

Framework production classes live in `src/`, and Composer maps the
`Framework\\` namespace prefix to that directory.

### Consequences

- New framework classes are added under `src/`.
- The existing `Framework\\` namespaces remain unchanged.
- Composer, PHPStan, Rector, and future tools must target `src/` consistently.
- Application code remains separate under `app/`.
