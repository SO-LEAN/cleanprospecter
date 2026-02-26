[![PR CI](https://github.com/SO-LEAN/cleanprospecter/actions/workflows/on-pr.yml/badge.svg)](https://github.com/SO-LEAN/cleanprospecter/actions/workflows/on-pr.yml)
![maintanibility](https://api.codeclimate.com/v1/badges/b61cae7437cba2d564fb/maintainability)
![coverage](https://api.codeclimate.com/v1/badges/b61cae7437cba2d564fb/test_coverage)

# CleanProspecter

**CleanProspecter** is a PHP 8.5 CRM application for business prospecting, designed following **Domain-Driven Design (DDD)** and **Hexagonal Architecture** principles.

> A GOOD ARCHITECTURE MAXIMIZES THE NUMBER OF DECISIONS NOT MADE
> - UNCLE BOB

## Architecture

This project treats **Hexagonal Architecture** and **Clean Architecture** as synonymous concepts. The code is organized around the hexagon:

- **Domain** (inside the hexagon): Aggregates, value objects, domain events, repository interfaces
- **Application** (ports): Command/Query handlers (CQRS), presenters, read models
- **Infrastructure** (outside): Persistence adapters, external service adapters

### Key Principles

| Principle | Description |
|-----------|-------------|
| **CQRS** | Commands modify state (no return value). Queries use presenters and read models. |
| **DDD Aggregates** | Entities expose verb methods (`register`, `relocate`), not setters. |
| **Value Objects** | Typed IDs (`OrganizationId`, `UserId`), `Address`, `Email`, `PhoneNumber`. |
| **Domain Events** | Aggregates raise events (`OrganizationRegistered`, `OrganizationRelocated`). |
| **No Inheritance** | Composition over inheritance. Interfaces + traits instead of abstract base classes. |
| **Repository Pattern** | Domain defines repository interfaces; infrastructure implements them. |
| **Test Doubles** | InMemory repositories, Fakes, Stubs, Spies instead of PHPUnit mocks. |

For detailed architecture guidelines, see [CLAUDE.md](CLAUDE.md).

## Project Structure

```
src/
  Prospecting/
    Domain/
      Model/
        Organization/       # Aggregate, ID, events, repository interface
        Prospect/           # Aggregate, ID, repository interface
        User/               # Aggregate, ID, repository interface
        Shared/             # Value objects (Address, Email, PhoneNumber)
      Event/                # DomainEvent interface, dispatcher
    Application/
      Command/              # Write operations (RegisterOrganization, ...)
      Query/                # Read operations (ShowOrganization, ListMyOrganizations, ...)
      Port/                 # Infrastructure port interfaces
    Infrastructure/
      Persistence/          # InMemory repository implementations
      GeoLocation/          # Geolocation adapter
      Storage/              # File storage adapter
```

## User Stories

### Implemented

- [x] As prospector, I want to register an organization
- [x] As prospector, I want to list my organizations
- [x] As prospector, I want to show organization details
- [x] As prospector, I want to update an organization profile
- [x] As prospector, I want to remove an organization logo
- [x] As user, I want to show my account
- [x] As user, I want to update my account

### Planned

- [ ] As prospector, I want to register a prospect
- [ ] As prospector, I want to list my prospects
- [ ] As prospector, I want to log a phone call
- [ ] As prospector, I want to log an appointment
- [ ] As prospector, I want to log an email exchange
- [ ] As prospector, I want to log an SMS exchange

### Future

- Tags and categorization
- Auto-import events from email box, SMS, etc.
- Email marketing campaigns

## How It Works

### Command (Write)

Commands modify state and do not return values. Side effects are captured through domain events.

```php
$command = new RegisterOrganizationCommand(
    organizationId: OrganizationId::generate()->value,
    corporateName: 'Acme Corp',
    ownedBy: $currentOrganizationId,
    // ...
);

$handler = $container->get(RegisterOrganizationHandler::class);
$handler($command);
```

### Query (Read)

Queries return data through a presenter interface, using read models (readonly DTOs).

```php
$query = new ShowOrganizationQuery(organizationId: $id);
$presenter = new MyPresenterImplementation();

$handler = $container->get(ShowOrganizationHandler::class);
$handler($query, $presenter);

// The presenter received an OrganizationReadModel (readonly DTO)
```

### Transaction Management

```php
interface TransactionManager
{
    public function transactional(callable $operation): mixed;
}

// Usage in a handler
$this->transactionManager->transactional(function () use ($command) {
    $this->userRepository->save($user);
    $this->organizationRepository->save($organization);
});
```

## Developer Tools

### Prerequisites

- Docker & Docker Compose
- PHP 8.5 (for local development without Docker)

### Commands

All commands are available through the Makefile:

```console
$ make
```

| Command             | Description                                    |
|---------------------|------------------------------------------------|
| `make composer`     | Install dependencies                           |
| `make test`         | Run test suite                                 |
| `make testdox`      | Run tests with agile documentation output      |
| `make test-coverage`| Run tests and generate HTML coverage report    |
| `make cs`           | Code sniffer (PSR-2)                           |
| `make cs-fix`       | Auto-fix code style                            |

## License

MIT - See [composer.json](composer.json) for details.

## Author

Michel MAIER - [opensource@solean-it.com](mailto:opensource@solean-it.com)
