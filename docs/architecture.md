# WP Enterprise Content Hub Architecture

## Overview

WP Enterprise Content Hub is structured as an enterprise-oriented WordPress plugin with clear separation between domain logic, application logic, WordPress infrastructure, caching, REST APIs, Gutenberg integration, lifecycle handling, security, and operational tooling.

The main architectural goals are:

- Separation of concerns
- Dependency inversion
- Testable business logic
- Replaceable infrastructure
- WordPress compatibility
- Explicit cache boundaries
- Maintainable REST API design
- Modern Gutenberg development
- Reproducible local development
- Automated quality assurance
- Operational tooling through WP-CLI

---

## High-Level Architecture

```text
┌─────────────────────────────────────┐
│        Presentation / Entry         │
│                                     │
│ Gutenberg Block     REST API        │
│ WP-CLI Commands                     │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│          Application Layer          │
│                                     │
│ ResourceService                     │
│ ResourceQuery                       │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│             Contracts               │
│                                     │
│ ResourceRepository                  │
│ CacheStore                          │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│          Infrastructure             │
│                                     │
│ CachedResourceRepository            │
│ WordPressResourceRepository         │
│ WordPressTransientCache             │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│              WordPress              │
│                                     │
│ WP_Query                            │
│ Posts / Taxonomies                  │
│ Transients                          │
└─────────────────────────────────────┘