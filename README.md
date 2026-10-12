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