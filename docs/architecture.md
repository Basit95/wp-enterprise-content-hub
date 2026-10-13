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
```

---

## Source Structure

```text
src/
├── Application/
│   └── ResourceQuery.php
├── Blocks/
│   └── ResourceExplorerBlock.php
├── Cache/
│   └── ResourceCacheInvalidator.php
├── Cli/
│   ├── CacheCommand.php
│   └── ResourceCommand.php
├── Content/
│   ├── ResourcePostType.php
│   └── ResourceTaxonomy.php
├── Contracts/
│   ├── CacheStore.php
│   └── ResourceRepository.php
├── Domain/
│   └── Resource/
│       └── Resource.php
├── Infrastructure/
│   ├── CachedResourceRepository.php
│   ├── WordPressResourceRepository.php
│   └── WordPressTransientCache.php
├── Lifecycle/
│   ├── Activator.php
│   └── Deactivator.php
├── Rest/
│   ├── DepartmentController.php
│   └── ResourceController.php
├── Security/
│   └── Capabilities.php
├── Services/
│   └── ResourceService.php
└── Plugin.php
```

---

## Domain Layer

The domain layer contains the core `Resource` model.

```text
src/Domain/Resource/Resource.php
```

The `Resource` object represents resource data such as:

- ID
- Title
- Excerpt
- Content
- Status
- Permalink
- Departments
- Updated date

The domain model does not execute WordPress queries.

This keeps the core resource representation independent from WordPress-specific persistence logic.

---

## Application Layer

### ResourceQuery

```text
src/Application/ResourceQuery.php
```

`ResourceQuery` represents search and pagination requirements.

It contains values such as:

- Search keyword
- Department slug
- Page
- Resources per page
- Resource status

It also normalizes pagination values before they are used by infrastructure.

### ResourceService

```text
src/Services/ResourceService.php
```

`ResourceService` coordinates resource retrieval and pagination.

It depends on:

```text
ResourceRepository
```

instead of directly depending on:

```text
WP_Query
```

This keeps application logic independent from WordPress persistence details and makes the service easier to test.

---

## Contracts

### ResourceRepository

```text
src/Contracts/ResourceRepository.php
```

The repository contract defines:

```text
find()
search()
count()
```

Higher-level application code depends on this contract instead of a concrete WordPress implementation.

### CacheStore

```text
src/Contracts/CacheStore.php
```

The cache contract defines:

```text
get()
set()
flush()
```

This keeps caching consumers independent from the WordPress Transient API.

A different cache backend could later implement the same contract without requiring changes to the application layer.

---

## Repository Pattern

The project uses the Repository Pattern to isolate persistence logic.

```text
ResourceService
      ↓
ResourceRepository
      ↓
WordPressResourceRepository
      ↓
WP_Query
```

### WordPressResourceRepository

```text
src/Infrastructure/WordPressResourceRepository.php
```

This class is responsible for:

- Translating `ResourceQuery` into WordPress query arguments
- Running `WP_Query`
- Loading resources by ID
- Counting matching resources
- Mapping WordPress post data into domain `Resource` objects

This keeps WordPress-specific persistence logic outside `ResourceService`.

---

## Cache Decorator Pattern

Caching is added through a repository decorator.

```text
ResourceService
        ↓
CachedResourceRepository
        ↓
WordPressResourceRepository
        ↓
WP_Query
```

`CachedResourceRepository` implements the same `ResourceRepository` contract.

This means `ResourceService` does not need to know whether data comes from cache or directly from WordPress.

### Cache Hit Flow

```text
Request
   ↓
ResourceService
   ↓
CachedResourceRepository
   ↓
Cache Hit
   ↓
Return Cached Result
```

### Cache Miss Flow

```text
Request
   ↓
ResourceService
   ↓
CachedResourceRepository
   ↓
Cache Miss
   ↓
WordPressResourceRepository
   ↓
WP_Query
   ↓
Store Result in Cache
   ↓
Return Result
```

---

## Cached Resource Repository

```text
src/Infrastructure/CachedResourceRepository.php
```

The cached repository handles caching for:

- Individual resource lookups
- Resource search results
- Resource counts

Search and count cache keys are derived from query values such as:

- Search
- Department
- Page
- Per-page limit
- Status

Equivalent queries therefore resolve to the same cache key.

---

## WordPress Cache Implementation

```text
src/Infrastructure/WordPressTransientCache.php
```

The default cache implementation uses the WordPress Transient API.

The rest of the application does not directly call:

```text
get_transient()
set_transient()
```

Those WordPress-specific operations stay inside infrastructure.

---

## Versioned Cache Invalidation

The cache uses versioned keys.

Conceptually:

```text
ech_cache_1_<hash>
```

When cache invalidation occurs:

```text
Version 1
   ↓
Version 2
```

New requests use:

```text
ech_cache_2_<hash>
```

Old cache entries are no longer used by the application and can expire naturally.

This avoids scanning the database and deleting every generated transient key individually.

---

## Cache Invalidation

```text
src/Cache/ResourceCacheInvalidator.php
```

Resource cache invalidation is centralized.

The cache is invalidated when:

- A resource is created
- A resource is updated
- A resource is deleted
- Resource department assignments change
- A department is created
- A department is edited
- A department is deleted

The cache can also be cleared manually with:

```bash
wp ech cache clear
```

This keeps invalidation logic outside REST controllers and services.

---

## REST API Architecture

The plugin exposes a custom REST namespace:

```text
/wp-json/ech/v1
```

Available endpoints:

```text
GET /resources
GET /resources/{id}
GET /departments
```

### REST Request Flow

```text
HTTP Request
     ↓
ResourceController
     ↓
ResourceQuery
     ↓
ResourceService
     ↓
CachedResourceRepository
     ↓
WordPressResourceRepository
     ↓
WP_Query
```

REST controllers are responsible for HTTP and WordPress REST concerns.

Application and persistence logic remain outside the controller layer.

---

## REST API Query Parameters

The resource collection endpoint supports:

```text
search
department
page
per_page
```

Example:

```text
/wp-json/ech/v1/resources?search=security&department=engineering&page=1&per_page=6
```

Pagination is handled through `ResourceQuery` and `ResourceService`.

---

## Gutenberg Architecture

The plugin includes a custom React-based Gutenberg block:

```text
Enterprise Resource Explorer
```

Source files:

```text
blocks/resource-explorer/
```

Production assets:

```text
build/resource-explorer/
```

The block supports:

- Department selection
- Configurable resource count
- Grid layout
- List layout
- Search visibility
- Department filter visibility
- Modified date visibility
- Editor preview
- Dynamic frontend filtering

Initial content is server-rendered by PHP.

Interactive frontend filtering communicates with the custom REST API.

---

## Portable REST Endpoint Handling

The frontend does not hardcode:

```text
/wp-json/ech/v1/resources
```

Instead, PHP generates the endpoint using:

```php
rest_url( 'ech/v1/resources' )
```

The generated REST URL is exposed to the frontend through a data attribute.

The JavaScript then builds requests from that URL.

This improves compatibility with:

- WordPress subdirectory installations
- Custom WordPress URL settings
- Reverse proxies
- Non-standard deployment environments

---

## Frontend Security

Server-rendered output uses WordPress escaping and sanitization functions.

Examples include:

```text
esc_html()
esc_attr()
esc_url()
wp_kses_post()
```

Dynamic resource rendering avoids directly injecting REST response values through raw `innerHTML`.

The frontend creates DOM elements and assigns dynamic values using properties such as:

```text
textContent
href
```

This reduces DOM injection risk.

---

## WordPress Content Model

### Resource Post Type

The custom post type is:

```text
ech_resource
```

Resources support:

- Title
- Editor
- Excerpt
- Featured image
- Author
- Revisions

### Department Taxonomy

The custom taxonomy is:

```text
ech_department
```

Departments organize enterprise resources.

---

## Security and Capabilities

Custom capabilities are defined in:

```text
src/Security/Capabilities.php
```

The plugin uses custom capabilities for resource management.

Capabilities are assigned during plugin activation.

Security measures include:

- Custom capabilities
- Sanitized REST parameters
- Pagination validation
- Escaped frontend output
- Published-resource visibility
- Safe dynamic frontend rendering

---

## Plugin Activation Lifecycle

Activation is handled by:

```text
src/Lifecycle/Activator.php
```

The activation sequence is:

```text
Plugin Activation
       ↓
Add Capabilities
       ↓
Register Resource Post Type
       ↓
Register Department Taxonomy
       ↓
Flush Rewrite Rules
```

The post type and taxonomy are registered before `flush_rewrite_rules()` is executed.

This ensures WordPress knows about the custom content types before rewrite rules are regenerated.

---

## Plugin Deactivation Lifecycle

Deactivation is handled by:

```text
src/Lifecycle/Deactivator.php
```

Rewrite rules are flushed during the plugin lifecycle where required.

---

## WP-CLI Architecture

WP-CLI commands are located in:

```text
src/Cli/
```

### Cache Command

```text
src/Cli/CacheCommand.php
```

Command:

```bash
wp ech cache clear
```

Example:

```text
Success: Enterprise Content Hub cache cleared.
```

### Resource Command

```text
src/Cli/ResourceCommand.php
```

Command:

```bash
wp ech resources seed
```

Example first execution:

```text
Created resource: Application Security Policy
Created resource: Employee Onboarding Guide
Created resource: Financial Approval Policy
Success: Seeding complete. Created: 3, skipped: 0.
```

---

## Idempotent Resource Seeding

The resource seeding command is idempotent.

Seeded resources are assigned internal seed identifiers.

When the command is executed again, existing seeded records are detected.

Example:

```text
Skipped existing resource: Application Security Policy
Skipped existing resource: Employee Onboarding Guide
Skipped existing resource: Financial Approval Policy
Success: Seeding complete. Created: 0, skipped: 3.
```

This prevents repeated WP-CLI executions from continuously creating duplicate demo data.

---

## Docker Architecture

The development environment contains:

```text
WordPress
MySQL 8
WP-CLI
```

Docker Compose configuration:

```text
docker-compose.yml
```

WordPress and WP-CLI share the WordPress filesystem volume:

```text
wordpress_data:/var/www/html
```

They also share the plugin bind mount.

This allows WP-CLI to execute against the same WordPress installation used by the web container.

### Web Flow

```text
Browser
   ↓
WordPress Container
   ↓
MySQL Container
```

### WP-CLI Flow

```text
WP-CLI Container
      ↓
Shared WordPress Filesystem
      ↓
Same MySQL Database
```

---

## Testing Architecture

Unit tests are located in:

```text
tests/Unit/
```

Test support classes are located in:

```text
tests/Support/
```

Current test areas include:

- Resource domain model
- Resource serialization
- Resource query defaults
- Pagination boundaries
- Resource service behavior
- Invalid resource IDs
- Repository abstraction
- Pagination metadata
- Cached resource lookups
- Cached missing-resource lookups
- Cached search queries
- Cached count queries

The current suite contains:

```text
15 tests
```

---

## Test Doubles

### FakeResourceRepository

```text
tests/Support/FakeResourceRepository.php
```

This allows `ResourceService` to be tested without requiring WordPress.

### FakeCacheStore

```text
tests/Support/FakeCacheStore.php
```

This allows `CachedResourceRepository` to be tested without requiring WordPress transients.

Caching tests verify that repeated lookups use cached values rather than repeatedly calling the underlying repository.

---

## PHP Coding Standards

PHPCS configuration:

```text
phpcs.xml.dist
```

Run:

```bash
composer lint
```

Automatically fix supported violations:

```bash
composer lint:fix
```

The project follows WordPress Coding Standards while retaining PSR-4 class organization.

---

## JavaScript and SCSS Quality

JavaScript linting:

```bash
npm run lint:js
```

SCSS linting:

```bash
npm run lint:css
```

SCSS automatic fixes:

```bash
npm run lint:css:fix
```

Formatting:

```bash
npm run format
```

Production build:

```bash
npm run build
```

---

## GitHub Actions CI

CI configuration:

```text
.github/workflows/ci.yml
```

The workflow runs on pushes and pull requests.

### PHP Matrix

PHP validation runs against:

```text
PHP 8.1
PHP 8.2
PHP 8.3
```

The PHP workflow performs:

- Composer validation
- Dependency installation
- PHPCS
- PHPUnit

### Gutenberg / JavaScript

The JavaScript workflow uses:

```text
Node.js 22
```

It performs:

- `npm ci`
- JavaScript linting
- SCSS linting
- Gutenberg production build
- Generated asset verification

---

## Local Quality Gate

Before pushing changes, run:

```bash
composer lint
composer test
npm run lint:js
npm run lint:css
npm run build
```

All checks should pass before code is pushed.

---

## Continuous Integration Flow

```text
Git Push / Pull Request
          ↓
     GitHub Actions
          ↓
 ┌────────┴─────────┐
 ↓                  ↓
PHP Matrix      JS / Gutenberg
 ↓                  ↓
Composer            npm ci
PHPCS               JS lint
PHPUnit             SCSS lint
                    Build
                    Asset verification
```

---

## Architectural Decisions

### Why Repository Pattern?

The repository abstraction prevents application services from being coupled directly to WordPress persistence APIs.

Benefits include:

- Easier unit testing
- Replaceable persistence
- Cleaner application services
- Centralized WordPress query logic

### Why Cache Decorator?

Caching is implemented as a repository decorator rather than directly inside `ResourceService`.

Benefits include:

- `ResourceService` remains focused on application behavior
- Caching stays in infrastructure
- The cache implementation can be replaced
- Consumers do not need to know whether data is cached

### Why CacheStore Contract?

The `CacheStore` contract prevents the cached repository from depending directly on WordPress transients.

Future implementations could include:

```text
RedisCache
MemcachedCache
ObjectCacheStore
```

without changing the application layer.

### Why Versioned Cache Invalidation?

Deleting every generated transient would require locating every matching cache key.

Versioned invalidation moves the application to a new cache namespace instead.

Older cache entries become unused and expire naturally.

This keeps cache invalidation simple and efficient.

---

## Extension Possibilities

The architecture supports future infrastructure replacements without redesigning application services.

Examples:

```text
RedisCache implements CacheStore
```

```text
ExternalApiResourceRepository implements ResourceRepository
```

```text
DatabaseResourceRepository implements ResourceRepository
```

The application layer can continue depending on the same contracts.

---

## Architecture Summary

The architecture intentionally contains more structure than a traditional small WordPress plugin.

The project demonstrates production-oriented WordPress engineering through:

- Layered architecture
- Domain modeling
- Application services
- Repository pattern
- Decorator pattern
- Dependency inversion
- Cache abstraction
- Versioned cache invalidation
- REST API development
- Gutenberg development
- Secure frontend rendering
- WP-CLI administration
- Idempotent data seeding
- PHPUnit testing
- PHPCS
- JavaScript and SCSS linting
- Docker development
- GitHub Actions CI
- PHP version matrix validation

The result is a WordPress plugin architecture that remains maintainable, testable, extensible, and compatible with standard WordPress APIs.