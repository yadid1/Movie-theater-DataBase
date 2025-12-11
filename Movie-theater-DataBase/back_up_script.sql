USE movie_theatre_db;

-- =============================================================
-- Backup Script
-- Purpose: Clone all main tables into backup copies
-- with names suffixed by _bak_YYYYMMDD.
-- On each run:
--   - Drops existing backup tables for today (if any)
--   - Re-creates them and copies current data.
-- =============================================================

SET @backup_date = DATE_FORMAT(CURDATE(), '%Y%m%d');

-- Helper pattern per table:
--   1) DROP TABLE IF EXISTS <tbl>_bak_YYYYMMDD;
--   2) CREATE TABLE <tbl>_bak_YYYYMMDD LIKE <tbl>;
--   3) INSERT INTO <tbl>_bak_YYYYMMDD SELECT * FROM <tbl>;

-- ========== THEATRE ==========
SET @tbl = 'Theatre';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== AUDITORIUM ==========
SET @tbl = 'Auditorium';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== SEAT ==========
SET @tbl = 'Seat';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== MOVIE ==========
SET @tbl = 'Movie';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== SHOWTIME ==========
SET @tbl = 'Showtime';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== CUSTOMER ==========
SET @tbl = 'Customer';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== TICKET ==========
SET @tbl = 'Ticket';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== TICKETAUDIT ==========
SET @tbl = 'TicketAudit';
SET @bak = CONCAT(@tbl, '_bak_', @backup_date);

SET @sql = CONCAT('DROP TABLE IF EXISTS ', @bak);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @bak, ' LIKE ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('INSERT INTO ', @bak, ' SELECT * FROM ', @tbl);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;