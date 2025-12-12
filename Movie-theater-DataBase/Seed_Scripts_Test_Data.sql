-- ============================================================
-- DML SEED SCRIPT - Movie Theatre Database
-- ============================================================
USE movie_theatre_db;

-- Reset tables so seed script is repeatable (FK-safe)
SET FOREIGN_KEY_CHECKS = 0;

-- Child -> Parent delete order (FK-safe)
DELETE FROM Ticket;
ALTER TABLE Ticket AUTO_INCREMENT = 1;

DELETE FROM Showtime;
ALTER TABLE Showtime AUTO_INCREMENT = 1;

DELETE FROM Seat;
ALTER TABLE Seat AUTO_INCREMENT = 1;

DELETE FROM Auditorium;
ALTER TABLE Auditorium AUTO_INCREMENT = 1;

DELETE FROM Theatre;
ALTER TABLE Theatre AUTO_INCREMENT = 1;

DELETE FROM Movie;
ALTER TABLE Movie AUTO_INCREMENT = 1;

DELETE FROM Customer;
ALTER TABLE Customer AUTO_INCREMENT = 1;

DROP TABLE IF EXISTS HelperNumber;

SET FOREIGN_KEY_CHECKS = 1;

-- ===========================
-- 1. THEATRE (3)
-- ===========================
INSERT INTO Theatre (name, location) VALUES
('CineMax Downtown', 'Santa Ana, CA'),
('Sunset Plaza Cinema', 'Los Angeles, CA'),
('OceanView Theatres', 'San Diego, CA');

-- ===========================
-- 2. AUDITORIUM (10 total)
-- ===========================
INSERT INTO Auditorium (name, totalSeats, theatre_ID) VALUES
('Aud 1', 96, 1),   -- theatre 1
('Aud 2', 120, 1),
('Aud 3', 192, 1),
('Aud 1', 128, 2),  -- theatre 2
('Aud 2', 160, 2),
('Aud 3', 200, 2),
('Aud 1', 96, 3),   -- theatre 3
('Aud 2', 120, 3),
('Aud 3', 160, 3),
('Aud 4', 200, 3);

-- ===========================
-- 3. HELPER NUMBER TABLE (1–20)
-- ===========================
CREATE TABLE HelperNumber (
  n INT PRIMARY KEY
);

INSERT INTO HelperNumber (n)
VALUES (1),(2),(3),(4),(5),(6),(7),(8),(9),(10),
       (11),(12),(13),(14),(15),(16),(17),(18),(19),(20);

-- ===========================
-- 4. SEAT MAPS
--    Auditorium 1 → 8×12
--    Auditorium 2 → 10×16
--    Auditorium 3 → 12×20
-- ===========================

-- Auditorium 1 seats (8 rows × 12 seats)
INSERT INTO Seat (auditorium_ID, row_label, seatNumber, seatType)
SELECT
  1,
  r.row_label,
  h.n,
  'STANDARD'
FROM (
  SELECT 'A' AS row_label UNION ALL
  SELECT 'B' UNION ALL
  SELECT 'C' UNION ALL
  SELECT 'D' UNION ALL
  SELECT 'E' UNION ALL
  SELECT 'F' UNION ALL
  SELECT 'G' UNION ALL
  SELECT 'H'
) r
JOIN HelperNumber h ON h.n BETWEEN 1 AND 12;

-- Auditorium 2 seats (10 rows × 16 seats)
INSERT INTO Seat (auditorium_ID, row_label, seatNumber, seatType)
SELECT
  2,
  r.row_label,
  h.n,
  'STANDARD'
FROM (
  SELECT 'A' AS row_label UNION ALL
  SELECT 'B' UNION ALL
  SELECT 'C' UNION ALL
  SELECT 'D' UNION ALL
  SELECT 'E' UNION ALL
  SELECT 'F' UNION ALL
  SELECT 'G' UNION ALL
  SELECT 'H' UNION ALL
  SELECT 'I' UNION ALL
  SELECT 'J'
) r
JOIN HelperNumber h ON h.n BETWEEN 1 AND 16;

-- Auditorium 3 seats (12 rows × 20 seats)
INSERT INTO Seat (auditorium_ID, row_label, seatNumber, seatType)
SELECT
  3,
  r.row_label,
  h.n,
  'STANDARD'
FROM (
  SELECT 'A' AS row_label UNION ALL
  SELECT 'B' UNION ALL
  SELECT 'C' UNION ALL
  SELECT 'D' UNION ALL
  SELECT 'E' UNION ALL
  SELECT 'F' UNION ALL
  SELECT 'G' UNION ALL
  SELECT 'H' UNION ALL
  SELECT 'I' UNION ALL
  SELECT 'J' UNION ALL
  SELECT 'K' UNION ALL
  SELECT 'L'
) r
JOIN HelperNumber h ON h.n BETWEEN 1 AND 20;

-- ===========================
-- 5. MOVIES (12)
-- ===========================
INSERT INTO Movie (title, runtime, releaseDate, mpaaRating) VALUES
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

-- ===========================
-- 6. SHOWTIMES (80 generated)
-- ===========================
INSERT INTO Showtime (movie_ID, auditorium_ID, format, language, showDateTime)
SELECT
  ((rn - 1) MOD 12) + 1 AS movie_ID,
  ((rn - 1) MOD 10) + 1 AS auditorium_ID,
  CASE ((rn - 1) MOD 3)
    WHEN 0 THEN '2D'
    WHEN 1 THEN '3D'
    ELSE 'IMAX'
  END AS format,
  'EN' AS language,
  TIMESTAMP(
    DATE_ADD('2025-11-10', INTERVAL FLOOR((rn - 1) / 4) DAY),
    MAKETIME(12 + 3 * ((rn - 1) MOD 4), 0, 0)
  ) AS showDateTime
FROM (
  SELECT @row := @row + 1 AS rn
  FROM HelperNumber h1
  CROSS JOIN HelperNumber h2,
       (SELECT @row := 0) AS vars
) AS numbered
LIMIT 80;

-- ===========================
-- 7. CUSTOMERS (60)
-- ===========================
INSERT INTO Customer (firstName, lastName, email, phoneNumber) VALUES
('Alice','Garcia','cust1@example.com','5550000001'),
('Bob','Lopez','cust2@example.com','5550000002'),
('Carol','Nguyen','cust3@example.com','5550000003'),
('David','Hernandez','cust4@example.com','5550000004'),
('Eve','Patel','cust5@example.com','5550000005'),
('Frank','Kim','cust6@example.com','5550000006'),
('Grace','Martinez','cust7@example.com','5550000007'),
('Hugo','Ramirez','cust8@example.com','5550000008'),
('Ivy','Sanchez','cust9@example.com','5550000009'),
('Jack','Flores','cust10@example.com','5550000010'),
('Karen','Ortiz','cust11@example.com','5550000011'),
('Leo','Castillo','cust12@example.com','5550000012'),
('Maria','Reyes','cust13@example.com','5550000013'),
('Noah','Gonzalez','cust14@example.com','5550000014'),
('Olivia','Diaz','cust15@example.com','5550000015'),
('Paul','Morales','cust16@example.com','5550000016'),
('Quinn','Ruiz','cust17@example.com','5550000017'),
('Rosa','Vasquez','cust18@example.com','5550000018'),
('Sam','Cruz','cust19@example.com','5550000019'),
('Tina','Perez','cust20@example.com','5550000020'),
('Uma','Chavez','cust21@example.com','5550000021'),
('Victor','Ramos','cust22@example.com','5550000022'),
('Wendy','Torres','cust23@example.com','5550000023'),
('Xavier','Sandoval','cust24@example.com','5550000024'),
('Yara','Navarro','cust25@example.com','5550000025'),
('Zane','Herrera','cust26@example.com','5550000026'),
('Ana','Lopez','cust27@example.com','5550000027'),
('Bruno','Garcia','cust28@example.com','5550000028'),
('Cindy','Martinez','cust29@example.com','5550000029'),
('Diego','Soto','cust30@example.com','5550000030'),
('Elena','Vega','cust31@example.com','5550000031'),
('Felix','Alvarez','cust32@example.com','5550000032'),
('Gloria','Medina','cust33@example.com','5550000033'),
('Henry','Cortez','cust34@example.com','5550000034'),
('Isabel','Campos','cust35@example.com','5550000035'),
('Jorge','Pacheco','cust36@example.com','5550000036'),
('Karla','Mendoza','cust37@example.com','5550000037'),
('Luis','Aguilar','cust38@example.com','5550000038'),
('Mia','Olivares','cust39@example.com','5550000039'),
('Nate','Delgado','cust40@example.com','5550000040'),
('Oscar','Fuentes','cust41@example.com','5550000041'),
('Patty','Valdez','cust42@example.com','5550000042'),
('Ricky','Salazar','cust43@example.com','5550000043'),
('Sofia','Rosales','cust44@example.com','5550000044'),
('Tony','Lozano','cust45@example.com','5550000045'),
('Ursula','Gallegos','cust46@example.com','5550000046'),
('Vince','Camacho','cust47@example.com','5550000047'),
('Will','Arellano','cust48@example.com','5550000048'),
('Ximena','Carrillo','cust49@example.com','5550000049'),
('Yuri','Escobar','cust50@example.com','5550000050'),
('Zelda','Juarez','cust51@example.com','5550000051'),
('Adrian','Correa','cust52@example.com','5550000052'),
('Bianca','Serrano','cust53@example.com','5550000053'),
('Cesar','Moreno','cust54@example.com','5550000054'),
('Diana','Orellana','cust55@example.com','5550000055'),
('Eduardo','Benitez','cust56@example.com','5550000056'),
('Fatima','Renteria','cust57@example.com','5550000057'),
('Gustavo','Padilla','cust58@example.com','5550000058'),
('Helena','Mejia','cust59@example.com','5550000059'),
('Ian','Trujillo','cust60@example.com','5550000060');

-- ===========================
-- 8. TICKETS (400+)
-- ===========================
INSERT INTO Ticket (showtime_ID, seat_ID, customer_ID, price, status)
SELECT
  s.showtime_ID,
  st.seat_ID,
  ((@rc := @rc + 1 - 1) MOD 60) + 1 AS customer_ID,
  CASE 
    WHEN s.format = 'IMAX' THEN 20.00
    WHEN s.format = '3D'   THEN 18.00
    ELSE 15.00
  END AS price,
  'ACTIVE' AS status
FROM Showtime s
JOIN Seat st 
  ON st.auditorium_ID = s.auditorium_ID
CROSS JOIN (SELECT @rc := 0) AS vars
WHERE s.showtime_ID <= 8
ORDER BY s.showtime_ID, st.row_label, st.seatNumber
LIMIT 400;