# ManipurApp Installer Assets

The installer uses these files in order:

1. `schema.sql`
2. `demo_seed.sql` (when demo data is selected)
3. `constraints.sql`

The seed is a sanitized, tenant-1-derived demonstration dataset. It does not include the other tenants from the development database.
