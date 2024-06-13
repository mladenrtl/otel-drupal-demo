# OpenTelemetry Demo

Welcome to our OpenTelemetry Demo project! This README provides an overview of the project structure, components, and how to get started.

## Overview

This project consists of two main parts:

1. **Drupal in DDEV**: This part involves developing a Drupal website using the DDEV local development environment.
2. **UserAPI and Frontend with Tempo, Grafana, Loki, and Prometheus**: This part involves building a UserAPI using .NET, a frontend using Next.js, and utilizing Tempo for tracing, Grafana for visualization, Loki for log aggregation, and Prometheus for monitoring, all orchestrated with Docker Compose.

## Requirements

Before you start, ensure you have the following tools installed:

- **Docker**: [Install Docker](https://docs.docker.com/get-docker/)
- **Docker Compose**: [Install Docker Compose](https://docs.docker.com/compose/install/)
- **DDEV**: [Install DDEV](https://ddev.readthedocs.io/en/stable/#installation)

## Components

### Drupal in DDEV

- **Description**: Drupal is a popular open-source content management system (CMS) for building websites and web applications.
- **Tools**: DDEV (local development environment for Drupal), Composer (dependency manager for PHP).
- **Commands**:
  - `make start-ddev`: Start the DDEV environment.
  - `make composer-install`: Install Drupal dependencies using Composer.
  - `make create-ddev`: Create a new Drupal project.
  - `make restart-ddev`: Restart the DDEV environment.
  - `make stop-ddev`: Stop the DDEV environment.

### UserAPI and Frontend with Tempo, Grafana, Loki, and Prometheus

- **Description**: UserAPI is a .NET web API providing backend functionality, and the Frontend is a Next.js application serving as the user interface. Tempo is used for distributed tracing, Grafana for visualization, Loki for log aggregation, and Prometheus for monitoring.
- **Tools**: Docker Compose, .NET (for UserAPI), Next.js (for Frontend), Tempo, Grafana, Loki, Prometheus.
- **Commands**:
  - `make start-docker-compose`: Start Docker Compose services.
  - `make stop-docker-compose`: Stop Docker Compose services.
  - `make restart-project`: Restart the entire project (DDEV and Docker Compose).
  - `make stop-project`: Stop both DDEV and Docker Compose services.

## Getting Started

To get started with the project, follow these steps:

1. **Clone the Repository**: Clone the project repository to your local machine.
2. **Set Up The Project**: Run `make create-project` to set up the project.
3. **Login as Admin**: Use `make login` to log in as an admin.
4. **Access the Frontend**: The frontend is available at [http://localhost:3003/](http://localhost:3003/).
5. **Start Development**: Use the appropriate Makefile commands to start the development environment.
6. **Happy Coding!**: Start developing your Drupal website and UserAPI/Frontend components.

### Import Content

1. Go to [http://my-demo-blog.ddev.site/admin/config/content/contentimport](http://my-demo-blog.ddev.site/admin/config/content/contentimport)
2. In the `Select Content Type` field, choose `Article`.
3. In the `Select Import Type` field, choose `Create New content`.
4. Select the file `web/content-import/opentelemetry_demo_blog_posts.csv` and click on Import.

### OpenTelemetry and Grafana

- Open Grafana at [http://localhost:3000](http://localhost:3000).
- Tempo and Loki are available at [http://localhost:3000/explore](http://localhost:3000/explore) by selecting `Tempo` or `Loki` from the dropdown menu.
- There are 2 dashboards available in Grafana:
  - `OpenTelemetry App` - [http://localhost:3000/d/otel-app/opentelemetry-app?orgId=1&refresh=15s](http://localhost:3000/d/otel-app/opentelemetry-app?orgId=1&refresh=15s)
    - This dashboard shows detailed metrics by service. Select the desired service from the dropdown in the `service` field.
  - `OpenTelemetry Apps RED` - [http://localhost:3000/d/otel_apps_red/opentelemetry-apps-red?orgId=1&refresh=5s](http://localhost:3000/d/otel_apps_red/opentelemetry-apps-red?orgId=1&refresh=5s)
    - This dashboard shows the RED (requests/errors/duration) metrics for all services.

### Load Testing

For load testing, we use k6. The tests are located in the `k6-tests` directory.

To run the sample test with URLs, follow these steps:
- Uncomment the `k6` service in the `docker-compose.yml` and `docker-compose.override.yml` files.
- Copy the file `k6-tests/urls.dist.js` to `k6-tests/urls.js` and add URLs to test.
- Run the k6 test with `make k6-test`.

To create and run a test script:
1. Create a new test script in the `k6-tests` directory.
2. Change the command of the k6 service in the `docker-compose.yml` file to run the new test script.
3. Run the k6 test with `make k6-test`.
