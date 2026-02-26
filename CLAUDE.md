# CLAUDE.md - CleanProspecter DDD Refactoring Guide

## Project Overview

**CleanProspecter** is a CRM (Customer Relationship Management) application for business prospecting. Originally built as a clean architecture proof-of-concept (Uncle Bob style, ~2018, PHP 7.2), it is now being refactored to follow **Domain-Driven Design (DDD)** and **Hexagonal Architecture** principles using modern PHP 8.5.

## Architecture Vision

### Hexagonal Architecture = Clean Architecture

We treat Hexagonal Architecture and Clean Architecture as synonymous. The hexagon has:
- **Inside (Domain)**: Business rules, aggregates, value objects, domain events, repository interfaces
- **Ports (Application)**: Use cases (command handlers / query handlers), presenters, DTOs
- **Outside (Infrastructure)**: Persistence adapters, external service adapters (geolocation, storage, notifications)

### Bounded Contexts

For a CRM application at this stage, we keep a **single bounded context** (`Prospecting`) rather than splitting prematurely. If the application grows, natural boundaries may emerge (e.g., `Identity`, `Prospecting`, `Communication`). For now, the domain stays unified under `Prospecting` to avoid unnecessary complexity.

> **Rule**: Do not introduce multiple bounded contexts until there is a clear business reason for it. Premature splitting adds coupling via integration events without benefit.

If bounded contexts are introduced later, they communicate through **Integration Events** (not direct dependencies).

## Directory Structure (Target)

```
src/
  Prospecting/                          # Single bounded context
    Domain/
      Model/
        Organization/
          Organization.php              # Aggregate root
          OrganizationId.php            # Value Object (typed ID)
          OrganizationRegistered.php    # Domain Event
          OrganizationRelocated.php     # Domain Event
          OrganizationRepository.php    # Port (interface)
        Prospect/
          Prospect.php                  # Aggregate root
          ProspectId.php
          ProspectRepository.php
        User/
          User.php                      # Aggregate root
          UserId.php
          UserRepository.php
        Shared/
          Address.php                   # Value Object
          Email.php                     # Value Object
          PhoneNumber.php               # Value Object
          Logo.php                      # Value Object
          PersonName.php                # Value Object
      Event/
        DomainEvent.php                 # Interface
        DomainEventDispatcher.php       # Interface
    Application/
      Command/
        RegisterOrganization/
          RegisterOrganizationCommand.php
          RegisterOrganizationHandler.php
        RelocateOrganization/
          RelocateOrganizationCommand.php
          RelocateOrganizationHandler.php
        UpdateOrganizationProfile/
          UpdateOrganizationProfileCommand.php
          UpdateOrganizationProfileHandler.php
        RemoveOrganizationLogo/
          RemoveOrganizationLogoCommand.php
          RemoveOrganizationLogoHandler.php
      Query/
        ShowOrganization/
          ShowOrganizationQuery.php
          ShowOrganizationHandler.php
          ShowOrganizationPresenter.php # Port (interface)
          OrganizationReadModel.php     # DTO / Read Model
        ListMyOrganizations/
          ListMyOrganizationsQuery.php
          ListMyOrganizationsHandler.php
          ListMyOrganizationsPresenter.php
          OrganizationSummaryReadModel.php
        ShowMyAccount/
          ShowMyAccountQuery.php
          ShowMyAccountHandler.php
          ShowMyAccountPresenter.php
          AccountReadModel.php
      Port/
        TransactionManager.php          # Interface (closure-based)
        FileStorage.php                 # Interface
        GeoLocationService.php          # Interface
        Notifier.php                    # Interface
    Infrastructure/
      Persistence/
        InMemory/
          InMemoryOrganizationRepository.php
          InMemoryUserRepository.php
      GeoLocation/
        GeoLocationAdapter.php
        GeoPointResult.php
      Storage/
        FileStorageAdapter.php

tests/
  Unit/
    Prospecting/
      Domain/
        Model/
          Organization/
            OrganizationTest.php
          ...
      Application/
        Command/
          RegisterOrganization/
            RegisterOrganizationHandlerTest.php
          ...
        Query/
          ShowOrganization/
            ShowOrganizationHandlerTest.php
          ...
      Builder/                          # Test builders (DDD-compatible)
        OrganizationBuilder.php
        UserBuilder.php
        ...
      Double/                           # Test doubles (InMemory, Fake, Stub)
        InMemoryOrganizationRepository.php
        FakeFileStorage.php
        StubGeoLocationService.php
        SpyNotifier.php
```

## Architectural Rules

### 1. CQRS - Command Query Responsibility Segregation

- **Commands** (write operations) MUST NOT return values. They modify state and may raise domain events.
- **Queries** (read operations) inject a `Presenter` interface and return read models (DTOs). No domain entity is ever returned to the outside.
- Command handlers receive a `Command` DTO and call aggregate methods.
- Query handlers receive a `Query` DTO, read from repositories, and pass read models to the presenter.

```php
// Command - no return value
final class RegisterOrganizationHandler
{
    public function __invoke(RegisterOrganizationCommand $command): void
    {
        // ... build aggregate, persist, dispatch events
    }
}

// Query - uses presenter
final class ShowOrganizationHandler
{
    public function __invoke(ShowOrganizationQuery $query, ShowOrganizationPresenter $presenter): void
    {
        // ... fetch, build read model, call presenter->present(readModel)
    }
}
```

### 2. No "Impl" Suffix

Implementation classes are named after what they ARE, not that they are an implementation:
- `InMemoryOrganizationRepository` (not `OrganizationRepositoryImpl`)
- `RegisterOrganizationHandler` (not `RegisterOrganizationImpl`)

### 3. No Abstract Base Classes - Use Interface + Trait

Instead of `AbstractUseCase` or `BaseEntity`, compose behavior with **interfaces** and **traits** where factorization is needed. Prefer duplication over premature abstraction.

```php
// Good: Interface defines the contract
interface CommandHandler {}

// Good: Trait provides shared behavior if genuinely needed
trait RaiseDomainEvents
{
    private array $domainEvents = [];

    protected function raise(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

### 4. No Setters - DDD Aggregates With Verb Methods

Entities (aggregates) expose **intention-revealing methods** (verbs), not setters:

```php
final class Organization
{
    // BAD: $organization->setCorporateName('Acme');
    // GOOD:
    public static function register(
        OrganizationId $id,
        string $corporateName,
        // ...
    ): self { ... }

    public function relocate(Address $address): void { ... }
    public function attachLogo(Logo $logo): void { ... }
    public function removeLogo(): void { ... }
    public function assignToHolding(OrganizationId $holdingId): void { ... }
}
```

### 5. No Getters on Aggregates (CQRS Read Models)

Since we use CQRS, **read models** (DTOs) are used for queries. Aggregates don't need getters exposed to the outside. Internal getters may exist for domain logic only. Read models are plain `readonly` classes with public properties.

```php
// Read model for queries
final readonly class OrganizationReadModel
{
    public function __construct(
        public string $id,
        public string $corporateName,
        public ?string $email,
        public ?AddressReadModel $address,
        // ...
    ) {}
}
```

### 6. Value Objects for IDs

Each aggregate has a typed ID value object with a private constructor. No more `mixed $id`:

```php
final readonly class OrganizationId
{
    private function __construct(
        public string $value,
    ) {}

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

### 7. No BaseEntity - Aggregates Raise Domain Events

Instead of inheriting from `Base`, aggregates use a trait to collect domain events:

```php
final class Organization
{
    use RaiseDomainEvents;

    public static function register(...): self
    {
        $org = new self(...);
        $org->raise(new OrganizationRegistered($org->id));
        return $org;
    }
}
```

### 8. Domain Events (Not Value Objects for Events)

Events are simple immutable DTOs that record what happened in the domain. They are NOT value objects. Aggregates raise them; handlers or dispatchers process them.

```php
final readonly class OrganizationRegistered implements DomainEvent
{
    public function __construct(
        public OrganizationId $organizationId,
        public DateTimeImmutable $occurredOn = new DateTimeImmutable(),
    ) {}
}
```

### 9. Repository Instead of Gateway

- The term **Repository** is used for persistence ports (not "Gateway").
- Repository interfaces live in `Domain/Model/{Aggregate}/`.
- Repository implementations live in `Infrastructure/Persistence/`.
- The directory for entities is `Domain/` (not `Entity/`).

```php
interface OrganizationRepository
{
    public function nextId(): OrganizationId;
    public function save(Organization $organization): void;
    public function ofId(OrganizationId $id): Organization;
    public function remove(Organization $organization): void;
}
```

### 10. Simplified Transaction Manager (Closure-based)

```php
interface TransactionManager
{
    /** @template T
     *  @param callable(): T $operation
     *  @return T */
    public function transactional(callable $operation): mixed;
}
```

### 11. No User Injection in Use Cases

Access control is handled at the **controller/infrastructure layer**, NOT inside use cases. Use cases receive only the data they need (via Command/Query DTOs). The `UseCaseConsumer` concept is removed from the domain.

If a use case needs to know "who" (e.g., `ListMyOrganizations`), the organization ID is passed as a command/query parameter - not as an injected user context.

### 12. Authentication is Infrastructure

Authentication (`Login`, `RefreshUser`) is NOT a domain use case. It is handled entirely at the **infrastructure/framework layer** (e.g., Symfony Security, JWT, OAuth). The domain does not contain any authentication logic. The old `Login` and `RefreshUser` use cases are removed from the hexagon.

### 13. GeoLocation is Infrastructure

The `GeoLocation` service and `GeoPoint` response are **infrastructure concerns**. They live in `Infrastructure/GeoLocation/`. The domain may define a port interface in `Application/Port/GeoLocationService.php` if the domain needs to trigger geolocation.

### 14. Minimal Inheritance

Prefer **composition** and **duplication** over inheritance:
- No `Person` abstract class. `Organization` and `Prospect` each have their own properties.
- No `Event` abstract base class. `Call`, `Email`, etc. are standalone.
- Some duplication (email, phoneNumber on both Organization and Prospect) is acceptable and preferred over a shared hierarchy.

### 15. Clean Code Principles

The codebase follows **Clean Code** (Robert C. Martin) principles:

- **No comments in code.** Code must be self-explanatory through clear naming. The only acceptable annotations are PHPDoc type hints (`@var`, `@param`, `@return`, `@template`) required for static analysis.
- **Meaningful names.** Classes, methods, and variables must reveal intent. No abbreviations, no cryptic names.
- **Small functions.** Each method does one thing. Extract private methods with intention-revealing names instead of adding comments.
- **No dead code.** Remove unused imports, methods, and variables. Never comment out code — delete it (git has history).
- **Boy Scout Rule.** Leave the code cleaner than you found it.
- **Single Responsibility.** Each class has one reason to change.
- **DRY within reason.** Eliminate duplication only when the duplicated code serves the same purpose. Prefer duplication over wrong abstraction.
- **Fail fast.** Validate at construction time (value objects), throw exceptions early.
- **Immutability by default.** Use `readonly` classes and properties wherever possible. Mutable state only in aggregates where it's part of the domain model.

### 16. Value Objects Use Private Constructor + Named Constructor

Value objects always have a **private constructor** and expose one or more **named static constructors** that reveal intent:

```php
final readonly class OrganizationId
{
    private function __construct(
        public string $value,
    ) {}

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}

final readonly class Address
{
    private function __construct(
        public ?string $street,
        public ?string $postalCode,
        public ?string $city,
        public ?string $country,
    ) {}

    public static function create(?string $street, ?string $postalCode, ?string $city, ?string $country): self
    {
        return new self($street, $postalCode, $city, $country);
    }
}
```

## Modern PHP 8.5 Features to Use

- **`readonly` classes** for DTOs, value objects, read models, commands, queries, events
- **Constructor property promotion** everywhere
- **Enums** for types (OrganizationType, ProspectStatus, etc.)
- **Named arguments** where it improves readability
- **Union types and intersection types** where appropriate
- **`match` expressions** instead of switch
- **Fibers** if async is needed
- **First-class callable syntax** for closure-based patterns
- **`readonly` properties** on aggregates where applicable
- **Pipe operator** `|>` for functional-style transformations
- **Strict types** declared in every file
- **Return type declarations** on all methods

## Testing Strategy

### Philosophy: Test the Hexagon Input

We test the **application layer** (command/query handlers) as the entry point to the hexagon. This is the boundary where the outside world interacts with our domain.

### Test Naming Convention

- Use `#[Test]` attribute instead of `test` method prefix.
- Method names start with `should` and describe the expected behavior.
- No comments in tests — the method name IS the documentation.

```php
#[Test]
public function shouldRegisterAndRaiseDomainEvent(): void { ... }

#[Test]
public function shouldThrowWhenOwnerNotFound(): void { ... }
```

### No PHPUnit Mocks

Replace all Prophecy/PHPUnit mocks with hand-written test doubles:

| Old (Mock)                        | New (Test Double)                     |
|-----------------------------------|---------------------------------------|
| `$this->prophesize(OrgGateway)`   | `new InMemoryOrganizationRepository()`|
| `$this->prophesize(Storage)`      | `new FakeFileStorage()`               |
| `$this->prophesize(GeoLocation)`  | `new StubGeoLocationService()`        |
| `$this->prophesize(UserNotifier)` | `new SpyNotifier()`                   |
| `$this->prophesize(Presenter)`    | `new CollectingPresenter()`           |

- **InMemory repositories** store entities in an array and implement the real interface.
- **Fakes** provide a working but simplified implementation.
- **Stubs** return pre-configured responses.
- **Spies** record calls for assertions.

### Builder Pattern (DDD-compatible)

Builders construct aggregates using the **domain methods** (not setters):

```php
final class OrganizationBuilder
{
    private OrganizationId $id;
    private string $corporateName = 'ACME';
    // ...

    public static function anOrganization(): self { return new self(); }

    public function withCorporateName(string $name): self
    {
        $clone = clone $this;
        $clone->corporateName = $name;
        return $clone;
    }

    public function build(): Organization
    {
        return Organization::register(
            id: $this->id,
            corporateName: $this->corporateName,
            // ...
        );
    }
}
```

## Use Case Renaming Guide

From CRUD-oriented names to DDD/business-intent names:

| Old (CRUD)                     | New (DDD Intent)               | Type    |
|--------------------------------|--------------------------------|---------|
| `CreateOrganization`           | `RegisterOrganization`         | Command |
| `UpdateOrganization`           | `UpdateOrganizationProfile`    | Command |
| `RemoveOrganizationLogo`       | `RemoveOrganizationLogo`       | Command |
| `GetOrganization`              | `ShowOrganization`             | Query   |
| `FindMyOwnOrganizations`       | `ListMyOrganizations`          | Query   |
| `GetMyAccountInformation`      | `ShowMyAccount`                | Query   |
| `UpdateMyAccountInformation`   | `UpdateMyAccount`              | Command |

## Namespace Mapping

| Old                                          | New                                          |
|----------------------------------------------|----------------------------------------------|
| `Solean\CleanProspecter\Entity\*`            | `Solean\Prospecting\Domain\Model\*`         |
| `Solean\CleanProspecter\Gateway\Entity\*`    | `Solean\Prospecting\Domain\Model\*` (interfaces next to aggregates) |
| `Solean\CleanProspecter\Gateway\*`           | `Solean\Prospecting\Application\Port\*`     |
| `Solean\CleanProspecter\UseCase\*`           | `Solean\Prospecting\Application\Command\*` or `Application\Query\*` |
| `Solean\CleanProspecter\Exception\*`         | `Solean\Prospecting\Domain\Exception\*`     |
| `Solean\CleanProspecter\Traits\*`            | Inlined or moved to `Domain\Model\Shared\*` |

## Development Commands

```bash
make test          # Run test suite via Docker
make testdox       # Run tests with agile documentation output
make cs            # Code sniffer (PSR-2)
make cs-fix        # Auto-fix code style
```

## Refactoring Checklist

- [ ] Restructure directories to DDD layout (Domain/Application/Infrastructure)
- [ ] Introduce value objects for IDs (OrganizationId, UserId, ProspectId)
- [ ] Remove Base entity class, introduce RaiseDomainEvents trait
- [ ] Remove Person abstract class, inline properties
- [ ] Convert entities to aggregates with verb methods (no setters)
- [ ] Rename all use cases to business-intent names
- [ ] Split use cases into Command handlers and Query handlers (CQRS)
- [ ] Remove Impl suffix from all implementations
- [ ] Remove AbstractUseCase, use interfaces
- [ ] Remove UseCaseConsumer / user injection from use cases
- [ ] Replace Gateway terminology with Repository
- [ ] Simplify Transaction interface to closure-based TransactionManager
- [ ] Move GeoLocation to Infrastructure
- [ ] Introduce domain events on aggregates
- [ ] Create read models (readonly DTOs) for queries
- [ ] Update composer.json to PHP 8.5
- [ ] Replace Prophecy mocks with InMemory/Fake/Stub test doubles
- [ ] Update builders to use domain methods instead of setters
- [ ] Add value objects for Email, PhoneNumber, Address (already partially done)
- [ ] Use readonly classes, constructor promotion, enums throughout
- [ ] Remove UseCasesFacade (replaced by direct handler injection via DI container)
