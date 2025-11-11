# Movie Theatre Database and Web Application

This project is a comprehensive database and web application designed to manage a multi-location movie theatre chain. The system handles movie listings, theatre and auditorium management, showtime scheduling, seat layouts, customer management, and ticket sales.

This project was developed in three main parts:
1.  **Part A: Conceptual Design** - An Entity-Relationship Diagram (ERD) to model the database.
2.  **Part B: Logical Design & Implementation** - Translation of the ERD into a relational schema, implemented in MariaDB with advanced SQL objects.
3.  **Part C: Application** - A functional web application built with PHP and CSS to interact with the database.

---

## Technology Stack LAMP

* **Database:** MariaDB
* **Backend:** PHP (using PDO for database connection)
* **Frontend:** HTML, CSS
* **Database Design:** Chen Notation (ERD), Relational Schema (3NF)

---

## Project Features

### Part A: Conceptual Design

* **ER Diagram:** A detailed ERD using Chen notation was created to map all entities, attributes, and relationships.
* **Key Entities:** `Theatre`, `Auditorium`, `Seat`, `Movie`, `Showtime`, `Customer`, `Ticket`, and `Pricing`.
* **Business Rules:** The model enforces key business rules, such as:
    * An auditorium belongs to exactly one theatre.
    * A showtime screens one movie in one auditorium with no overlaps.
    * A ticket represents one specific seat for one showtime.
    * Variable pricing is supported by showtime and seat type.
* **Documentation:** Includes a full data dictionary and a list of assumptions.

### Part B: Logical Design & Database Implementation

* **Relational Schema:** The ERD was converted to a 3NF relational schema with all primary keys, foreign keys, and constraints (`UNIQUE`, `CHECK`, `NOT NULL`) defined.
* **Data Implementation:** The database was populated with a large, realistic seed dataset, including theatres, movies, customers, and ticket sales.
* **Advanced Database Objects:**
    * **Views:**
        1.  `TopMoviesByTicketsSold`: Reports on top-performing movies.
        2.  `UpcomingSoldOutShowtimes`: Lists sold-out shows by theatre.
        3.  `TheatreUtilizationReport`: Calculates the percentage of seats sold for the next 7 days.
    * **Triggers:**
        1.  An `ON INSERT` trigger on the `Ticket` table validates seat availability before a sale.
        2.  An `ON UPDATE` trigger on the `Ticket` table frees a seat when a ticket status is changed to 'REFUNDED'.
    * **Stored Procedures:**
        1.  `sell_ticket()`: A single procedure that validates and processes a ticket sale, handling pricing, seat availability, and data insertion atomically.
    * **Indexes:** Implemented to optimize performance for common query patterns.

### Part C: Web Application

A user-facing web application built with PHP provides core functionality for customers and managers.

* **Movie Browsing:** List all movies with filtering by rating, format, theatre, and date.
* **Showtime Finder:** Allows users to select a theatre and date to see all available showtimes.
* **Seat Map:** A visual grid (HTML table or divs) displays the seat layout for a selected showtime, marking sold vs. available seats.
* **Ticket Purchase:** A simulated purchase flow that captures user input, validates seat selection, and calls the `sell_ticket` stored procedure.
* **Ticket Management:** A "My Tickets" page allows users to look up their purchases (by email or order code) and process refunds.
* **Reports:** Read-only pages that display the data from the three required database views.

---

## Security & Best Practices

The application was built with security and robustness in mind:

* **SQL Injection Prevention:** All database queries are executed using **PDO with prepared statements**.
* **Cross-Site Scripting (XSS) Prevention:** All user-generated content is escaped using `htmlspecialchars` before being rendered on a page.
* **Cross-Site Request Forgery (CSRF) Protection:** Forms that perform write actions (purchase, refund) are protected with CSRF tokens.
* **Input Validation:** All server-side inputs are strictly validated (e.g., IDs are integers, emails are validated with `FILTER_VALIDATE_EMAIL`).
* **Secure Configuration:** Database credentials and other secrets are stored in a `config.php` or `.env` file, which is excluded from the repository (via `.gitignore`).

---

## Project Structure