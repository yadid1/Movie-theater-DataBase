-- ======================================================
-- Database: movie_theatre_db
-- Author: Team FREAKS
-- Phase B - Logical Design & Implementation
-- ======================================================
--STILL NEED TO UPDATE DATA TO FIT SPECIFIC RELAISTIC REQUIREMENTS BAWSED ON PHASE B DOCUMENTATION

USE movie_theatre_db;

-- =============== THEATRES ===============
INSERT INTO Theatre (name, location)
VALUES
('CineMax Downtown', 'Santa Ana, CA'),
('Sunset Plaza Cinema', 'Los Angeles, CA'),
('OceanView Theatres', 'San Diego, CA');

-- =============== AUDITORIUMS ===============
INSERT INTO Auditorium (name, totalSeats, theatre_ID)
VALUES
('Aud 1', 96, 1), ('Aud 2', 120, 1), ('Aud 3', 192, 1),
('Aud 1', 128, 2), ('Aud 2', 160, 2), ('Aud 3', 200, 2),
('Aud 1', 96, 3),  ('Aud 2', 120, 3), ('Aud 3', 160, 3), ('Aud 4', 200, 3);

-- =============== SEATS (sample 8x12 for first auditorium) ===============
-- You can later generate the full map with a loop or CSV import
INSERT INTO Seat (auditorium_ID, row_label, seatNumber, seatType)
VALUES
(1,'A',1,'STANDARD'),(1,'A',2,'STANDARD'),(1,'A',3,'STANDARD'),
(1,'B',1,'STANDARD'),(1,'B',2,'STANDARD'),(1,'B',3,'STANDARD');

-- =============== MOVIES ===============
INSERT INTO Movie (title, runtime, releaseDate, mpaaRating)
VALUES
('Inception',148,'2010-07-16','PG-13'),
('The Dark Knight',152,'2008-07-18','PG-13'),
('Interstellar',169,'2014-11-07','PG-13'),
('Oppenheimer',180,'2023-07-21','R'),
('Avatar',162,'2009-12-18','PG-13'),
('The Matrix',136,'1999-03-31','R'),
('Dune',155,'2021-10-22','PG-13'),
('Top Gun: Maverick',130,'2022-05-27','PG-13'),
('Frozen',102,'2013-11-27','PG'),
('Toy Story 4',100,'2019-06-21','G'),
('The Batman',176,'2022-03-04','PG-13'),
('Spider-Man: Across the Spider-Verse',140,'2023-06-02','PG');

-- =============== SHOWTIMES (sample) ===============
INSERT INTO Showtime (movie_ID, auditorium_ID, format, language, showDateTime)
VALUES
(1,1,'2D','EN','2025-11-10 18:00:00'),
(2,2,'IMAX','EN','2025-11-10 20:30:00'),
(3,3,'3D','EN','2025-11-11 17:00:00'),
(4,4,'2D','EN','2025-11-11 19:00:00'),
(5,5,'3D','ES','2025-11-12 16:30:00'),
(6,6,'2D','EN','2025-11-12 19:00:00'),
(7,7,'IMAX','EN','2025-11-13 18:15:00'),
(8,8,'2D','EN','2025-11-13 20:45:00'),
(9,9,'2D','EN','2025-11-14 17:15:00'),
(10,10,'3D','EN','2025-11-14 19:30:00');

-- =============== CUSTOMERS ===============
INSERT INTO Customer (firstName, lastName, email, phoneNumber)
VALUES
('Alice','Garcia','alice@gmail.com','7141234567'),
('Bob','Lopez','bob@gmail.com','7142223333'),
('Carol','Nguyen','carol@gmail.com','9491112222'),
('David','Hernandez','david@gmail.com','5624445555'),
('Eve','Patel','eve@gmail.com','3109990000');

-- =============== TICKETS (sample) ===============
INSERT INTO Ticket (showtime_ID, seat_ID, customer_ID, price, status)
VALUES
(1,1,1,14.00,'ACTIVE'),
(1,2,2,14.00,'ACTIVE'),
(1,3,3,14.00,'REFUNDED'),
(2,4,4,18.00,'ACTIVE'),
(2,5,5,18.00,'ACTIVE');