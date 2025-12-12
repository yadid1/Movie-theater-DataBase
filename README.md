Movie Theater Database and Web Application
Overview

This project is a full-stack Movie Theater Management System designed to model and simulate the core operations of a real-world movie theater. It integrates a relational database with a dynamic web application to allow users to browse movies, view showtimes, select seats, and purchase tickets.

The system demonstrates how structured data storage and web technologies work together to support common theater workflows.

System Functionality
Movie Catalog

The application maintains a catalog of movies stored in the database. Each movie includes key attributes such as title, MPAA rating, runtime, release date, and poster references. Movie information is dynamically retrieved and displayed through the web interface.

Theatres, Auditoriums, and Showtimes

Movies are associated with multiple showtimes, each scheduled at a specific theatre and auditorium. Showtimes include metadata such as format and language. This structure allows the system to accurately represent real theater scheduling and capacity constraints.

Seat Selection and Ticketing

For each showtime, the application displays a seating layout tied to the auditorium. Seat availability is tracked in real time through database queries. Users may select available seats and proceed with ticket creation, while the system enforces constraints to prevent duplicate seat reservations.

Each ticket is linked to a specific showtime, seat, auditorium, and theatre, ensuring data integrity and traceability.

Web Application

The web application is built using PHP with a structured backend and a clean frontend layout. Pages are dynamically rendered using database-driven content. The interface emphasizes clarity, usability, and consistency while supporting essential user interactions such as browsing, selecting, and purchasing.

Database Design

The database schema is fully normalized and models real-world theater operations. Core entities include movies, theatres, auditoriums, seats, showtimes, and tickets. Relationships are enforced using primary and foreign keys to maintain consistency and prevent invalid data states.

Project Structure

Movie-theater-DataBase
Contains SQL schema definitions, sample data, and database backup scripts.

Movie-theater-Webapp
Contains the PHP application, frontend assets, styling, and logic for user interaction.

Purpose

This project was developed to demonstrate proficiency in database design, backend development, and full-stack integration. It serves as both an academic project and a portfolio-ready example of a real-world transactional system.

Status

The application is a fully functional minimum viable product supporting movie browsing, showtime viewing, seat selection, and ticket purchasing.

This project was made by Bryan Orozco, Yadid Alamilla , Andrew Vu , Thatch bruce le, Yousuf Gul , Javi Guzman
