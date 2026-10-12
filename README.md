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
- Docker local development
- GitHub Actions CI

## Architecture

The plugin separates WordPress-specific infrastructure from application and domain logic.

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

## Screenshots

### Enterprise Resource Explorer

![Enterprise Resource Explorer](docs/images/01-frontend-resource-explorer.png)

Searchable and filterable resource directory with department metadata, responsive cards, and dynamic frontend behavior.

### Gutenberg Block Editor

![Gutenberg Block Editor](docs/images/02-gutenberg-block-editor.png)

Custom React-based Gutenberg block with configurable department, resource count, layout, search, department filter, and modified date controls.

### REST API

![REST API](docs/images/03-rest-api.png)

Custom REST API exposing enterprise resources with pagination and structured response data.

### Department Filtering

![REST API Department Filter](docs/images/04-rest-api-department.png)

REST API filtering resources by department using the custom `department` query parameter.

### GitHub Actions CI

![GitHub Actions CI](docs/images/05-github-actions-ci.png)

Automated CI pipeline validating PHP 8.1, 8.2, 8.3, PHPUnit, PHPCS, JavaScript, SCSS, and Gutenberg production builds.