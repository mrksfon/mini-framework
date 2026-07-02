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
