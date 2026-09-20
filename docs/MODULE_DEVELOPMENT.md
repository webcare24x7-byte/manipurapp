# ManipurApp Module Development

ManipurApp uses a modular PHP architecture. New features should normally be built as self-contained modules instead of adding business logic directly to the application core.

## 1. Typical module structure

```text
app/Modules/YourModule/
├── Controllers/
│   └── YourModuleController.php
├── Models/
│   └── YourModel.php
├── Services/
│   └── YourModuleService.php
├── Views/
│   ├── index.php
│   ├── create.php
│   ├── edit.php
│   └── view.php
├── Config/
├── routes.php
└── README.md
```

Not every module needs every directory. Use only the parts required by the feature.

## 2. Controllers

Controllers handle HTTP requests and responses.

Keep controllers **thin**:

```text
Request → Controller → Service → Model → Database
```

A controller should receive the request, call the appropriate service, and render or return the response.

Do not put complex business rules or large database queries directly in controllers.

## 3. Services

Services contain business logic and workflows.

Typical responsibilities include:

- Validation
- Business rules
- Calculations
- Coordinating multiple models
- Orchestrating workflows

Example:

```php
final class YourModuleService
{
    public function __construct(
        private YourModel $model
    ) {}

    public function create(array $data): int
    {
        // Validate and apply business rules.
        return $this->model->create($data);
    }
}
```

## 4. Models

Models handle database access.

Keep database access inside models rather than views or controllers.

Example:

```php
final class YourModel
{
    public function __construct(
        private Database $db
    ) {}

    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM your_table WHERE id = ?",
            [$id]
        );
    }
}
```

## 5. Views

Views are responsible for presentation.

Use the existing ManipurApp UI patterns and escape output appropriately:

```php
<h1><?= htmlspecialchars($item['name']) ?></h1>
```

Do not put database queries or complex business workflows in views.

## 6. Database migrations

Create a migration for new tables or schema changes.

For example:

```text
database/migrations/
└── 010_your_module.sql
```

For tenant-owned data, include `tenant_id` and follow the existing tenant conventions.

Follow the project's existing migration style instead of creating a separate database mechanism.

## 7. Routes

Define module routes through the module's route file and follow existing routing conventions.

Example:

```php
$router->get(
    '/admin/your-module',
    [YourModuleController::class, 'index']
);

$router->get(
    '/admin/your-module/create',
    [YourModuleController::class, 'create']
);
```

Avoid changing global routing unless the module genuinely requires it.

## 8. Multi-tenancy

ManipurApp is multi-tenant.

Tenant-owned queries must respect the current tenant:

```sql
WHERE tenant_id = ?
```

Never assume that data belongs to one global tenant.

A module must not expose records belonging to another tenant.

## 9. Permissions

Use the existing permission system.

A module might define permissions such as:

```text
yourmodule.view
yourmodule.create
yourmodule.edit
yourmodule.delete
```

Do not create a separate authentication or permission system inside a module.

## 10. Soft deletes

Where an entity follows the project's soft-delete convention, use:

```sql
deleted_at DATETIME NULL
```

and normally exclude deleted records:

```sql
WHERE deleted_at IS NULL
```

Follow the convention of the module you are extending.

## 11. Reuse existing functionality

Before creating a new module, check whether an existing module already provides the required functionality.

Prefer:

```text
Existing Service / Model
        ↓
Reuse
```

over duplicating the same business logic in another module.

This is especially important for shared functionality used by MemberApp, administration, AI, and other services.

## 12. MemberApp integration

If a module needs a member-facing experience, use the module's existing services/models where possible.

Conceptually:

```text
Module
   ↓
Service / Model
   ↓
MemberApp Controller
   ↓
PWA View
```

Do not duplicate module database tables simply to support MemberApp.

## 13. AI integration

If a module needs to be searchable through ManipurApp AI, expose a controlled application-level tool.

```text
AI Provider
     ↓
ManipurApp AI Tool
     ↓
Module Service
     ↓
Model
     ↓
Database
```

AI should not receive arbitrary SQL or direct database access.

## 14. Naming

Use clear module names such as:

```text
Restaurant
Tourism
CommercialRental
FreshFood
```

Classes normally use PascalCase:

```text
RestaurantController
RestaurantService
RestaurantModel
```

Use the database naming conventions already established in ManipurApp.

## 15. Do not break existing modules

Before modifying an existing module:

1. Understand its routes.
2. Understand its controller/service/model flow.
3. Identify dependencies.
4. Preserve existing behavior.
5. Add functionality without unnecessarily rewriting working code.

Established workflows such as Taxi and MemberApp should be treated carefully unless a change is explicitly required.

## 16. Testing

Before submitting a module, test:

### PHP syntax

```bash
find app -name "*.php" -print0 | xargs -0 -n1 php -l
```

### Database

- Run the migration on a clean database.
- Test the module with the installer/demo database.

### Tenant isolation

Verify that Tenant A cannot access Tenant B's records.

### Permissions

Verify that users without the required permission cannot perform protected actions.

### CRUD

Test:

```text
Create
Read
Update
Delete / Archive
```

### Responsive UI

Test member-facing screens on:

```text
Desktop
Tablet
Mobile
```

## 17. Example module

A simple Events module could look like:

```text
app/Modules/Events/
├── Controllers/
│   └── EventsController.php
├── Models/
│   └── Event.php
├── Services/
│   └── EventsService.php
├── Views/
│   ├── index.php
│   ├── create.php
│   ├── edit.php
│   └── view.php
├── routes.php
└── README.md
```

with a migration:

```text
database/migrations/
└── 011_events.sql
```

and permissions:

```text
events.view
events.create
events.edit
events.delete
```

## Architecture principle

Keep the main application flow simple:

```text
HTTP Request
     ↓
Controller
     ↓
Service
     ↓
Model
     ↓
Database
```

and:

```text
Controller
     ↓
View
```

The goal is to keep modules independent, understandable, testable, and reusable.

## Before submitting a module

Check:

- [ ] Existing behavior is preserved.
- [ ] Tenant boundaries are respected.
- [ ] Permissions are implemented.
- [ ] Database migrations are included.
- [ ] Routes are registered.
- [ ] Views are responsive.
- [ ] PHP syntax passes.
- [ ] No credentials or secrets are committed.
- [ ] Documentation explains the module.
- [ ] Existing services are reused where appropriate.
