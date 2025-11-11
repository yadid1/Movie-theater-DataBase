-- ======================================================
-- Database: movie_theatre_db
-- Author: Team 7 
-- Phase B - Logical Design & Implementation
-- ======================================================

CREATE DATABASE IF NOT EXISTS movie_theatre_db
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE movie_theatre_db;

-- ======================================================
-- 1. THEATRE TABLE
-- ======================================================
CREATE TABLE Theatre (
  theatre_ID INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  location VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- 2. AUDITORIUM TABLE
-- ======================================================
CREATE TABLE Auditorium (
  auditorium_ID INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  totalSeats INT NOT NULL CHECK (totalSeats > 0),
  theatre_ID INT NOT NULL,
  CONSTRAINT fk_auditorium_theatre
    FOREIGN KEY (theatre_ID)
    REFERENCES Theatre(theatre_ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- 3. SEAT TABLE
-- ======================================================
CREATE TABLE Seat (
  seat_ID INT AUTO_INCREMENT PRIMARY KEY,
  auditorium_ID INT NOT NULL,
  row_label VARCHAR(5) NOT NULL,
  seatNumber INT NOT NULL,
  seatType VARCHAR(20) DEFAULT 'STANDARD',
  CONSTRAINT fk_seat_auditorium
    FOREIGN KEY (auditorium_ID)
    REFERENCES Auditorium(auditorium_ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT uq_seat_position
    UNIQUE (auditorium_ID, row_label, seatNumber)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- 4. MOVIE TABLE
-- ======================================================
CREATE TABLE Movie (
  movie_ID INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  runtime INT NOT NULL CHECK (runtime > 0), -- in minutes
  releaseDate DATE NOT NULL,
  mpaaRating VARCHAR(10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- 5. SHOWTIME TABLE
-- ======================================================
CREATE TABLE Showtime (
  showtime_ID INT AUTO_INCREMENT PRIMARY KEY,
  movie_ID INT NOT NULL,
  auditorium_ID INT NOT NULL,
  format VARCHAR(10),
  language VARCHAR(20) NOT NULL,
  showDateTime DATETIME NOT NULL,
  CONSTRAINT fk_showtime_movie
    FOREIGN KEY (movie_ID)
    REFERENCES Movie(movie_ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_showtime_auditorium
    FOREIGN KEY (auditorium_ID)
    REFERENCES Auditorium(auditorium_ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
  -- Overlap prevention handled by trigger logic, not constraint
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- 6. CUSTOMER TABLE
-- ======================================================
CREATE TABLE Customer (
  customer_ID INT AUTO_INCREMENT PRIMARY KEY,
  firstName VARCHAR(60) NOT NULL,
  lastName VARCHAR(60) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phoneNumber VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- 7. TICKET TABLE
-- ======================================================
CREATE TABLE Ticket (
  ticket_ID INT AUTO_INCREMENT PRIMARY KEY,
  showtime_ID INT NOT NULL,
  seat_ID INT NOT NULL,
  customer_ID INT, -- nullable for guest checkout
  price DECIMAL(8,2) NOT NULL CHECK (price >= 0),
  status VARCHAR(15) DEFAULT 'ACTIVE',
  CONSTRAINT fk_ticket_showtime
    FOREIGN KEY (showtime_ID)
    REFERENCES Showtime(showtime_ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ticket_seat
    FOREIGN KEY (seat_ID)
    REFERENCES Seat(seat_ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ticket_customer
    FOREIGN KEY (customer_ID)
    REFERENCES Customer(customer_ID)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT uq_showtime_seat UNIQUE (showtime_ID, seat_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- INDEXES (for performance)
-- ======================================================
CREATE INDEX idx_showtime_date ON Showtime(showDateTime);
CREATE INDEX idx_movie_title ON Movie(title);
CREATE INDEX idx_ticket_status ON Ticket(status);
CREATE INDEX idx_customer_email ON Customer(email);

-- ======================================================
-- END OF DDL SCRIPT
-- ======================================================