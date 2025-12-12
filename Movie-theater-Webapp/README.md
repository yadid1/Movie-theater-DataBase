# Movie Theatre Web Application

A PHP + MySQL web app to browse movies, view showtimes, select seats, purchase tickets, request refunds, and view theatre reports.

## Requirements
- PHP 8+
- MySQL or MariaDB
- A web browser
- Optional: PHP built-in server (for local testing)

## Database Setup
Create the database, then import SQL files in order.

```
CREATE DATABASE movie_theatre_db;
```

Import order:
- `movie_theatre_db_DDL.sql`
- `movie_theatre_db_Data.sql` (or `Seed_Scripts_Test_Data.sql` if provided)
- `storedprocedures_functions.sql`
- `triggers.sql` (if included)

Verify tables exist: `Movie`, `Theatre`, `Auditorium`, `Seat`, `Showtime`, `Ticket`, `SoldSeats`.

### Import via phpMyAdmin
1. Open `http://localhost/phpmyadmin`.
2. Select `movie_theatre_db` → `Import`.
3. Upload each SQL file in the order above → `Go`.

### Import via CLI (Windows)
Run in Command Prompt (Administrator if needed). Default user is `root`.

```
"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < "C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\movie_theatre_db_DDL.sql"
"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < "C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\movie_theatre_db_Data.sql"
"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < "C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\storedprocedures_functions.sql"
"C:\xampp\mysql\bin\mysql.exe" -u root -p movie_theatre_db < "C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-DataBase\triggers.sql"
```

### Import via CLI (macOS)

```
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/movie_theatre_db_DDL.sql"
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/movie_theatre_db_Data.sql"
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/storedprocedures_functions.sql"
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p movie_theatre_db < \
	"/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-DataBase/triggers.sql"
```

## Running the Application

### Option A: PHP Built-in Server (recommended for quick testing)
1. Open a terminal in the `public` folder.
2. Start the server:

```
php -S localhost:8000
```

3. Visit `http://localhost:8000` in your browser.

### Option B: XAMPP (Windows/macOS)
1. Place the project in your XAMPP `htdocs` folder:
	 - Windows: `C:\xampp\htdocs\Movie-theater-DataBase\Movie-theater-Webapp\`
	 - macOS: `/Applications/XAMPP/xamppfiles/htdocs/Movie-theater-DataBase/Movie-theater-Webapp/`
2. Start `Apache` and `MySQL` in the XAMPP Control Panel.
3. Browse to:
	 - `http://localhost/Movie-theater-DataBase/Movie-theater-Webapp/public/`

## Configuration
Update database credentials in `config.php`:

```
$host = 'localhost';
$db   = 'movie_theatre_db';
$user = 'root';
$pass = '';
```

Change these if your environment requires different settings (e.g., a root password).

## Features
- Movies: Browse, search by title, filter by rating; view details.
- Showtimes: Select a theatre, view upcoming showtimes by date; open seat maps.
- Seat Selection: Interactive map shows available vs. sold seats.
- Ticket Purchase: Enter name, email, optional phone/discount; receive ticket ID.
- My Tickets: Lookup tickets by email; view movie, seat, price, status.
- Refunds: Refund active tickets; seat becomes available again.
- Reports: Top movies by tickets sold; sold-out showtimes; theatre utilization (next 7 days).

## Testing Checklist
- Purchase tickets; confirm appearance in My Tickets.
- Refund a ticket; confirm the seat is freed and map updates.
- Verify all report pages display expected data.
- Test movie search and rating filters.

## Completion
When all pages load, purchases and refunds work, seats update correctly, and reports show expected results, the app is operational.