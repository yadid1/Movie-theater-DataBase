Movie Theatre Database – Part B (Logical Design & Build)

1. Overview

This project implements a movie theatre booking database in MariaDB based on the ERD from Part A.
The system supports theatres, auditoriums, seats, movies, showtimes, customers, tickets, refunds, reporting views, triggers, a ticket-selling stored procedure, and a backup script.

Main features:
	•	Normalized schema in 3NF with PK, FK, UNIQUE, CHECK, and NOT NULL constraints.
	•	Realistic seed data: multiple theatres, auditoriums, seat maps, movies, showtimes, customers, and tickets.
	•	Views for reporting (top movies, sold-out showtimes, utilization).
	•	Triggers for seat availability enforcement and audit logging.
	•	Stored procedure sell_ticket that validates, prices, and inserts tickets.
	•	Backup script that clones all main tables into dated backup tables.

⸻

2. Files

Suggested project structure:
	•	ddl.sql – CREATE DATABASE and CREATE TABLE statements with constraints and indexes.
	•	seed.sql – DML seed script with realistic data.
	•	views.sql – All required views.
	•	triggers.sql – TicketAudit table and triggers.
	•	stored_procedures.sql – sell_ticket stored procedure.
	•	backup_script.sql – Backup script that creates _bak_YYYYMMDD tables.
	•	relational_schema.pdf – Relational model and constraints.
	•	data_dictionary_partA.pdf – From Part A.
	•	assumptions_partA.pdf – From Part A.
	•	README.md – This file.

⸻

3. Prerequisites
	•	XAMPP with MariaDB running locally.

⸻

4. Load Order (Setup Steps)

Follow these steps in order for a clean setup.

Step 1: Create schema (DDL)
	1.	Open phpMyAdmin.
	2.	Start MySQL in XAMPP.
	3.	In phpMyAdmin, click the SQL tab.
	4.	Paste and run the contents of ddl.sql.

This will:
	•	Create database movie_theatre_db.
	•	Create all tables: Theatre, Auditorium, Seat, Movie, Showtime, Customer, Ticket, TicketAudit.
	•	Define all PKs, FKs, UNIQUE constraints, CHECKs (where supported), and indexes.

Step 2: Seed data (DML)
	1.	In phpMyAdmin, select the movie_theatre_db database.
	2.	Click the SQL tab.
	3.	Paste and run the contents of seed.sql.

This will:
	•	Insert at least 3 theatres and 10 auditoriums.
	•	Generate 3 seat maps (different auditorium sizes).
	•	Insert 12+ movies.
	•	Insert 80 showtimes.
	•	Insert 60 customers.
	•	Insert 400+ tickets with some seats remaining unsold.

Step 3: Create views
	1.	With movie_theatre_db selected, open the SQL tab.
	2.	Paste and run views.sql.

This will create:
	•	v_movie_ticket_sales
	•	v_sold_out_showtimes
	•	v_theatre_utilization_next_7_days

Step 4: Create audit table and triggers
	1.	With movie_theatre_db selected, open the SQL tab.
	2.	Paste and run triggers.sql.

This will:
	•	Create TicketAudit (if not already created in DDL).
	•	Create:
	•	trg_ticket_before_insert
	•	trg_ticket_after_insert
	•	trg_ticket_after_update_refund

Step 5: Create stored procedure

If your MariaDB version required running mysql_upgrade, do that once before this step.
	1.	With movie_theatre_db selected, open the SQL tab.
	2.	Paste and run stored_procedures.sql.

This will:
	•	Create the sell_ticket procedure.

Step 6: Run backup script
	1.	With movie_theatre_db selected, open the SQL tab.
	2.	Paste and run backup_script.sql.

This will:
	•	Create backup tables for the current date, with names like <Table>_bak_YYYYMMDD.


TEST QUERIES IN ANOTHER FILE

6. Notes and Assumptions
	•	All tables are designed to be in at least 3NF. The normalization rationale and expected cardinalities are described in relational_schema.pdf.
	•	Guest checkout is supported by allowing customer_ID in Ticket to be nullable (if that choice was made in your final schema).
	•	Refunds are modeled using Ticket.status, and refunds are audited via TicketAudit and triggers.
	•	Pricing is stored at the per-ticket level and calculated at the time of sale by the sell_ticket procedure.