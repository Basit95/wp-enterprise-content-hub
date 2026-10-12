# WP Enterprise Content Hub

[![CI](https://github.com/Basit95/wp-enterprise-content-hub/actions/workflows/ci.yml/badge.svg)](https://github.com/Basit95/wp-enterprise-content-hub/actions/workflows/ci.yml)

Enterprise-oriented WordPress plugin demonstrating scalable plugin architecture, Gutenberg development, REST APIs, automated testing, coding standards, Docker-based local development, and CI/CD.

## Features

- Custom `Resource` post type
- Custom `Department` taxonomy
- REST API endpoints
- Search and department filtering
- React-based Gutenberg block
- Dynamic frontend resource explorer
- Custom WordPress capabilities
- Repository pattern
- Service layer architecture
- PHPUnit unit tests
- WordPress Coding Standards with PHPCS
- JavaScript and SCSS linting
- Docker-based local development
- GitHub Actions CI
- PHP 8.1, 8.2, and 8.3 CI validation
- Responsive grid and list layouts

## Screenshots

### Enterprise Resource Explorer

![Enterprise Resource Explorer](docs/images/01-frontend-resource-explorer.png)

Searchable and filterable frontend resource directory with department metadata, responsive resource cards, and dynamic filtering.

### Gutenberg Block Editor

![Gutenberg Block Editor](docs/images/02-gutenberg-block-editor.png)

Custom React-based Gutenberg block with configurable department, number of resources, layout, search visibility, department filtering, and modified date controls.

### REST API

![REST API](docs/images/03-rest-api.png)

Custom REST API exposing enterprise resources with structured response data and pagination metadata.

### Department Filtering

![REST API Department Filter](docs/images/04-rest-api-department.png)

REST API filtering resources by department using the custom `department` query parameter.

### GitHub Actions CI

![GitHub Actions CI](docs/images/05-github-actions-ci.png)

Automated CI pipeline validating PHP 8.1, PHP 8.2, PHP 8.3, PHPUnit, PHPCS, JavaScript, SCSS, and Gutenberg production builds.

## Architecture

The plugin separates WordPress-specific infrastructure from application and domain logic to improve maintainability, testability, and extensibility.

```text
src/
├── Application/
│   └── ResourceQuery.php
├── Blocks/
│   └── ResourceExplorerBlock.php
├── Content/
│   ├── ResourcePostType.php
│   └── ResourceTaxonomy.php
├── Contracts/
│   └── ResourceRepository.php
├── Domain/
│   └── Resource/
│       └── Resource.php
├── Infrastructure/
│   └── WordPressResourceRepository.php
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

### Request Flow

```text
Gutenberg Block / REST Request
            ↓
     ResourceController
            ↓
      ResourceService
            ↓
   ResourceRepository
            ↓
WordPressResourceRepository
            ↓
        WP_Query
```

The `ResourceRepository` contract allows the application layer to remain independent from direct WordPress query implementation details.

This also makes the service layer easier to test with fake repository implementations.

## REST API

Base namespace:

```text
/wp-json/ech/v1
```

Available endpoints:

```text
GET /resources
GET /resources/{id}
GET /departments
```

### Resource Collection

```text
GET /wp-json/ech/v1/resources
```

Supported query parameters:

| Parameter | Description |
|---|---|
| `search` | Search resources by keyword |
| `department` | Filter by department slug |
| `page` | Pagination page number |
| `per_page` | Resources per page, maximum 50 |

Example:

```text
/wp-json/ech/v1/resources?search=security&department=engineering&page=1&per_page=6
```

Example response structure:

```json
{
  "data": [],
  "pagination": {
    "page": 1,
    "per_page": 6,
    "total": 0,
    "total_pages": 0
  }
}
```

### Departments

```text
GET /wp-json/ech/v1/departments
```

Returns available resource departments including IDs, names, slugs, and resource counts.

## Gutenberg Block

The plugin includes a custom block:

```text
Enterprise Resource Explorer
```

The block provides:

- Department selection
- Configurable resource count
- Grid and list layouts
- Search visibility control
- Department filter visibility control
- Modified date visibility control
- Editor preview
- Dynamic frontend filtering
- Responsive frontend rendering

Source files are located in:

```text
blocks/resource-explorer/
```

Production assets are generated into:

```text
build/resource-explorer/
```

## Security

The plugin includes:

- Custom WordPress capabilities
- Sanitized REST API parameters
- Validated pagination parameters
- Escaped frontend output
- Published-resource visibility rules
- Capability assignment during plugin activation

Custom capabilities are managed through:

```text
src/Security/Capabilities.php
```

## Requirements

- WordPress 6.6+
- PHP 8.1+
- Composer
- Node.js 22
- npm
- Docker Desktop

## Installation

Clone the repository:

```bash
git clone https://github.com/Basit95/wp-enterprise-content-hub.git
```

Enter the project directory:

```bash
cd wp-enterprise-content-hub
```

Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies:

```bash
npm install
```

Build Gutenberg assets:

```bash
npm run build
```

Start the local WordPress environment:

```bash
docker compose up -d
```

Open:

```text
http://localhost:8080
```

Complete the WordPress installation and activate:

```text
WP Enterprise Content Hub
```

from the WordPress Plugins screen.

## Development

Run the Gutenberg development watcher:

```bash
npm run start
```

Build production assets:

```bash
npm run build
```

The build process:

1. Compiles the Gutenberg editor bundle
2. Generates editor and frontend CSS
3. Generates WordPress asset metadata
4. Copies `block.json`
5. Copies the frontend interaction script

## Testing

Run PHPUnit:

```bash
composer test
```

Current unit tests cover:

- Resource domain model
- Resource serialization
- Resource query defaults
- Pagination boundaries
- Resource service behavior
- Invalid resource IDs
- Repository abstraction
- Pagination metadata

The project currently includes:

```text
11 tests
30 assertions
```

## PHP Coding Standards

Run PHPCS:

```bash
composer lint
```

Automatically fix supported violations:

```bash
composer lint:fix
```

The project uses WordPress Coding Standards while retaining PSR-4 class organization.

## JavaScript Quality

Run JavaScript linting:

```bash
npm run lint:js
```

Run SCSS linting:

```bash
npm run lint:css
```

Automatically fix supported SCSS issues:

```bash
npm run lint:css:fix
```

Format Gutenberg source files:

```bash
npm run format
```

## Local Quality Gate

Before committing changes, run:

```bash
composer lint
composer test
npm run lint:js
npm run lint:css
npm run build
```

All checks should pass before code is pushed.

## GitHub Actions CI

The repository includes an automated GitHub Actions workflow.

CI runs on pushes and pull requests and validates:

### PHP

- PHP 8.1
- PHP 8.2
- PHP 8.3
- Composer configuration
- Dependency installation
- PHPCS
- PHPUnit

### Gutenberg / JavaScript

- Node.js 22
- npm dependency installation
- JavaScript linting
- SCSS linting
- Gutenberg production build
- Generated asset verification

Workflow configuration:

```text
.github/workflows/ci.yml
```

## Docker Development Environment

The repository includes a Docker Compose environment containing:

- WordPress
- MySQL 8
- WP-CLI

Start:

```bash
docker compose up -d
```

Stop:

```bash
docker compose down
```

View containers:

```bash
docker compose ps
```

## Project Structure

```text
wp-enterprise-content-hub/
├── .github/
│   └── workflows/
│       └── ci.yml
├── blocks/
│   └── resource-explorer/
├── build/
│   └── resource-explorer/
├── docs/
│   └── images/
├── scripts/
│   └── copy-block-assets.mjs
├── src/
├── tests/
├── composer.json
├── composer.lock
├── docker-compose.yml
├── enterprise-content-hub.php
├── package.json
├── package-lock.json
├── phpcs.xml.dist
├── phpunit.xml.dist
└── README.md
```

## Engineering Goals

This project demonstrates WordPress engineering beyond traditional theme and simple plugin development.

The main goals are:

- Modular architecture
- Separation of concerns
- Dependency inversion
- Repository pattern
- Service layer design
- Testable business logic
- REST API design
- Modern Gutenberg development
- Automated code quality
- Reproducible local development
- Continuous integration
- Production-oriented project organization

## Release

Current stable release:

```text
v1.0.0
```

## License

GPL-2.0-or-later