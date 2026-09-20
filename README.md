# ManipurApp v1.0.0

ManipurApp is an open-source, multi-tenant platform for local businesses, entrepreneurs, service providers and customers in Manipur.

## Quick start

1. Install PHP 8.2+ and MySQL/MariaDB.
2. Put the project in your web server directory.
3. Make sure the project root is writable during installation so the installer can write `config/database.php` and `storage/install.lock`.
4. Open `/manipurapp/` in your browser.
5. The root entry point redirects to `public/`; if the application is not installed yet, it opens the installer.
6. Enter your database credentials and click **Install ManipurApp**.
7. Leave **Install demo workspace** enabled if you want the included demo data.

The installer creates the database, installs the current schema, loads a sanitized demo dataset derived from the current tenant #1 test database, and writes the local database configuration.

### Demo account

- Workspace: **ManipurApp Demo Workspace**
- Email: `demo@manipurapp.local`
- Password: `Demo@12345`

Change or remove demo credentials before using a deployment for real users.

## Web root

For Apache/MAMP/XAMPP development, the project can live in a subdirectory such as:

`http://localhost:8888/manipurapp/`

The public entry point is `public/`. The repository root entry point redirects to it so application code is not intended to be served directly.

## Database

The installer uses:

- `installer/schema.sql` — database structure
- `installer/demo_seed.sql` — sanitized demo data
- `installer/constraints.sql` — indexes and foreign-key constraints

The demo seed is intentionally limited to the current tenant #1 dataset and shared catalog/permission records. Other tenant records are not included.

## AI configuration

AI features require the appropriate provider credentials. Do not commit real API keys. Configure them locally according to the module documentation.

## Development

The project uses plain PHP with a modular application structure. Existing module-specific migration and installation notes remain in the relevant module directories.

## Security

After installation, remove or restrict access to `/install/`. The installer creates `storage/install.lock` and refuses to reinstall while that lock exists.

Never commit production database credentials, API keys, private uploads, or real customer/member data.

## License

ManipurApp is released under the MIT License. See [LICENSE](LICENSE).


## First-time installation

1. Extract the project into your PHP web root.
2. Open `/manipurapp/`.
3. The installer opens automatically when `storage/install.lock` is absent.
4. Enter your MySQL/MariaDB credentials and choose whether to install the sanitized demo workspace.
5. The installer writes the local `config/database.php`, which is intentionally ignored by Git.
6. Configure `MANIPURAPP_GEMINI_API_KEY` if you want Gemini-powered features.

Do not commit generated local configuration or real API keys.
