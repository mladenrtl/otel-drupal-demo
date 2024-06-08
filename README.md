# OpenTelemetry Demo

Welcome to our OpenTelemetry Demo project! This README provides an overview of the project structure, components, and how to get started.

## Overview

This project consists of two main parts:

1. **Drupal in DDEV**: This part of the project involves developing a Drupal website using the DDEV local development environment.

2. **UserAPI and Frontend with Tempo, Grafana, Loki, and Prometheus**: This part of the project involves building a UserAPI using .NET, a frontend using Next.js, and utilizing Tempo for tracing, Grafana for visualization, Loki for log aggregation, and Prometheus for monitoring, all orchestrated with Docker Compose.

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

## To get started with the project, follow these steps:

1. **Clone the Repository**: Clone the project repository to your local machine.
2. **Set Up The Project**: `make create-project`
3. **Login as Admin**: `make login`
4. **Start Development**: Start the development environment using the appropriate Makefile commands.
5. **Happy Coding!**: Start developing your Drupal website and UserAPI/Frontend components.

### Load Testing

For load testing, we use k6. We use the k6 Docker image to run tests. The tests are located in the `k6-tests` directory.

To run the sample test with urls, follow these steps:
- Uncomment the `k6` service in the `docker-compose.yml` and `docker-compose.override.yml` files.
- Copy file `k6-tests/urls.dist.js to k6-tests/urls.js` and add URLs to test.
- Run k6 test with `make k6-test`
- To create and run a test script, do the following:
  - Create a new test script in the `k6-tests` directory.
  - Change command of k6 service in the `docker-compose.yml` file to run the new test script.
  - Run k6 test with `make k6-test`





