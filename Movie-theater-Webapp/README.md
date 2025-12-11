READ ME – Movie Theatre Web Application

This is a PHP and MySQL movie theatre web application that allows users to browse movies, view showtimes, select seats, purchase tickets, request refunds, and view theatre reports.

1. Requirements

PHP 8 or newer
MySQL or MariaDB
A web browser
Optional: PHP built-in server (recommended for testing)

2. Database Setup

Create the database by running:

CREATE DATABASE movie_theatre_db;

Import the SQL files in this order:

movie_theatre_db_DDL.sql
movie_theatre_db_Data.sql
storedprocedures_functions.sql
triggers.sql (only if included)

After importing, verify that tables such as Movie, Theatre, Auditorium, Seat, Showtime, Ticket, and SoldSeats exist.

3. Running the Application

Place the project folder anywhere on your system.

Open a terminal and navigate inside the "public" folder.

Start the PHP development server by running:

php -S localhost:8000

Open your web browser and go to:

http://localhost:8000

The site should now be running.

4. Application Features

Movies
Users can browse all available movies, search by title, or filter by rating. Clicking a movie shows additional details.

Showtimes
Users can select a theatre and view upcoming showtimes grouped by date. Each showtime links to its seat map.

Seat Selection
An interactive seating map shows available seats and sold seats. Selecting a seat takes the user to the checkout page.

Ticket Purchase
Users enter their name and email, along with optional phone number and discount code. After submitting, the system creates a ticket and displays a success message along with a ticket ID.

My Tickets
Users can enter their email address to view all their purchased tickets. Each ticket displays movie information, seat, price, and ticket status.

Refunds
Active tickets include a refund button. Refunds set the ticket status to REFUNDED and release the seat so it becomes available again.

Reports
The application includes three reports:
Top movies by total tickets sold
Showtimes that are sold out
Theatre utilization for the next seven days

5. Configuration

Database connection settings can be changed in config.php.
Typical configuration looks like:

$host = 'localhost';
$db = 'movie_theatre_db';
$user = 'root';
$pass = '';

Update these if your system requires different credentials.

6. Testing Instructions

Purchase one or more tickets, then check the My Tickets page using the same email.
Refund a ticket and verify that the seat becomes available again on the seat map.
Use the Reports menu to confirm that the report pages display data correctly.
Test searching for movies and filtering by rating.

7. Completion

If all pages load correctly, tickets can be purchased and refunded, seats update properly, and reports display expected results, the application is fully operational.