# Digital ILP Helper — GitHub integration

Prepared against the current clean installer repository.

## Files to promote

- `app/Modules/ILP/` — complete Digital ILP Helper module, including its two SQL migrations.
- `installer/schema.sql` — adds the two ILP tables to the fresh-install schema.
- `installer/demo_seed.sql` — adds the six ILP permit types, requirements, the `ilp.helper.view` permission and its assignment to the demo `church_admin` role.
- `installer/constraints.sql` — adds ILP indexes, auto-increment definitions and the ILP requirement foreign key.

No change to `install/index.php` is required. The existing installer continues to install `schema.sql`, `demo_seed.sql` and `constraints.sql` exactly as before.

## Why the installer files are needed

The normal application `Migrator` scans `database/migrations/*.php`; it does not execute module SQL migrations. Existing modules such as Tourism/Restaurant/FreshFood therefore keep module SQL migrations for incremental development/upgrades while the clean installer carries the current final schema and demo data in its three installer SQL files.

The ILP module follows the same pattern.

## Fresh installation behavior

With **Install demo workspace** enabled:

1. ILP tables are created by `installer/schema.sql`.
2. ILP knowledge records and the ILP view permission are loaded by `installer/demo_seed.sql`.
3. Indexes, auto-increments and the ILP foreign key are applied by `installer/constraints.sql`.
4. The existing module route loader automatically discovers `app/Modules/ILP/routes.php`.
5. `/ilp` is available to the demo administrator because `ilp.helper.view` is assigned to role 1 in the demo seed.

With demo seed disabled, the tables still exist, but the ILP knowledge records and demo permission assignment are not populated, consistent with the existing installer design.
