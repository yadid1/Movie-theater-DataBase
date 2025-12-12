# Movie Theater Database – XAMPP Setup Guide (macOS)

This repo contains SQL scripts to provision a MySQL database for a Movie Theater system. The instructions below are tailored for XAMPP on macOS and use phpMyAdmin for imports.

## Prerequisites
- XAMPP installed on macOS.
- Repo cloned under XAMPP htdocs: `/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/`.
- MySQL running via XAMPP.

## Start XAMPP Services
1. Open the XAMPP manager app: `/Applications/XAMPP/XAMPP Control.app`.
2. Start `Apache` and `MySQL`.

## Open phpMyAdmin
- Go to `http://localhost/phpmyadmin` in your browser.

## Create the Database
In phpMyAdmin:
1. Click `Databases`.
2. Create a new database, for example `movie_theatre_db` with collation `utf8mb4_general_ci`.

## Import Order (Recommended)
Import each SQL file into the `movie_theatre_db` database in this order:
1. `movie_theatre_db_DDL.sql` – Creates tables, keys, constraints.
2. `Seed_Scripts_Test_Data.sql` – Inserts test data.
3. `views.sql` – Defines views dependent on tables.
4. `trigger.sql` – Registers triggers after tables/procs exist.
5. `storedprocedures_function.sql` – Adds procedures and functions.

## Where to Find the Files
Files are in:
- `/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/`

Key files:
- `movie_theatre_db_DDL.sql`
- `views.sql`
- `storedprocedures_function.sql`
- `trigger.sql`
- `Seed_Scripts_Test_Data.sql`
- `test_queries.sql`
- `back_up_script.sql`

## Import via phpMyAdmin (GUI)
For each file:
1. Select the `movie_theatre_db` database in the left sidebar.
2. Click the `Import` tab.
3. Choose the SQL file (from the path above).
4. Leave format as `SQL`, click `Go`.

If an import fails:
- Check the error message; some objects require earlier files to be imported first.
- Re-run imports in the order listed above.
- Ensure the selected database is `movie_theatre_db` before each import.

## Import via Terminal (alternative)
You can also import using the MySQL CLI that ships with XAMPP.

1. Get MySQL credentials (default often `root` with no password on XAMPP; set one if prompted).
2. Run imports using `zsh`:

```
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/movie_theatre_db_DDL.sql"

/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/views.sql"

/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/storedprocedures_function.sql"

/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/trigger.sql"

/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/Seed_Scripts_Test_Data.sql"
```

Then validate:

```
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/test_queries.sql"
```

## Common XAMPP Notes
- If `root` has a password, the CLI will prompt after `-p`.
- If imports complain about SQL mode, you can temporarily relax strict modes in `my.cnf` or run `SET sql_mode = '';` in phpMyAdmin before importing, then restore as needed.
- Ensure `sql_safe_updates` is off if seed scripts perform updates without keys.

## Troubleshooting
- Table/foreign key errors: Re-check import order or drop the database and re-import from step 1.
- Function/trigger creation errors: Confirm you’re connected to `movie_theatre_db` and that DDL ran successfully.
- Collation issues: Use `utf8mb4` consistently for tables/columns.

## Folder Structure Tip (Optional)
This repo currently has a nested folder (`Movie-theater-DataBase/Movie-theater-DataBase`). If desired, you can move the inner SQL files up one level for convenience.

```
mv /Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/* \
	 /Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/
```

Adjust import paths accordingly if you do this.


# Movie Theater Database – XAMPP Setup Guide (Windows)

This section covers XAMPP on Windows using phpMyAdmin and the MySQL CLI.

## Prerequisites
- XAMPP installed on Windows.
- Repo under htdocs: `C:\xampp\htdocs\Movie-theater-DataBase\` (default XAMPP path).
- MySQL service running in XAMPP Control Panel.

## Start XAMPP Services
1. Open `XAMPP Control Panel`.
2. Start `Apache` and `MySQL`.

## Open phpMyAdmin
- Navigate to `http://localhost/phpmyadmin`.

## Create the Database
In phpMyAdmin:
1. Click `Databases`.
2. Create `movie_theatre_db` with collation `utf8mb4_general_ci`.

## Import Order (Recommended)
Import to `movie_theatre_db` in this order:
1. `movie_theatre_db_DDL.sql`
2. `views.sql`
3. `storedprocedures_function.sql`
4. `trigger.sql`
5. `Seed_Scripts_Test_Data.sql`

Optional:
- `test_queries.sql` – Validate schema/data.
- `back_up_script.sql` – Example backup (adapt paths).

## File Locations (Windows)
`C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\`

## Import via phpMyAdmin (GUI)
For each file:
1. Select `movie_theatre_db` in the sidebar.
2. Go to `Import`.
3. Choose the SQL file from the path above.
4. Click `Go`.

## Import via Command Prompt (CLI)
Run as Administrator if needed. Defaults: user `root`, often empty password.

Commands (run one by one):

```
"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < ^
	"C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\movie_theatre_db_DDL.sql"

"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < ^
	"C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\views.sql"

"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < ^
	"C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\storedprocedures_function.sql"

"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < ^
	"C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\trigger.sql"

"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < ^
	"C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\Seed_Scripts_Test_Data.sql"
```

Validate:

```
"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < ^
	"C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\test_queries.sql"
```

Notes:
- The `^` is a Windows CMD line-continuation; omit it if you paste as a single line.
- If you use PowerShell, remove `^` and place the command and path on one line.
- If `root` has a password, you’ll be prompted after `-p`.
