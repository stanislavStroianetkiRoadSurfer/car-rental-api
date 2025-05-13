## Installation

### Creating the containers

```bash
docker compose up # optionally -d --build --remove-orphans
```

### Installing the dependencies

```bash
docker compose exec app composer install
```

### Creating database 
```bash
docker compose exec app  bin/console doctrine:database:create --if-not-exists
```

### Loading fixtures
```bash
docker compose exec app  bin/console doctrine:fixtures:load
```

## Testing and Code Quality

### Testing
The project includes three categories of tests: **Unit**, **Integration**, and **Functional**. Refer to the [Symfony Documentation for Testing](https://symfony.com/doc/current/testing.html) for more details.

- **Unit Tests**: Fast tests executed without booting a kernel, focusing on isolated components (requires mocking).
  ```bash
  docker compose exec app composer test:unit
  ```

- **Integration and Functional Tests**: These tests boot a kernel to validate service integrations without mocks.
  ```bash
  docker compose exec app composer test:integration
  docker compose exec app composer test:functional
  ```

  Both types of tests reset the database before each test case and ensure a clean state.

- **Run All Tests**: Execute all test categories together:
  ```bash
  docker compose exec app composer test
  ```
