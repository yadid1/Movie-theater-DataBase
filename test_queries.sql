-- =============================================================
-- TEST QUERIES FOR PART B
-- Each query includes a description explaining its purpose.
-- =============================================================

-- -------------------------------------------------------------
-- Top 5 movies by tickets sold in November 2025.
-- Tests the v_movie_ticket_sales view and validates date filtering,
-- aggregation, and ordering by total tickets sold.
-- -------------------------------------------------------------
SELECT 
    title, 
    SUM(tickets_sold) AS total_tickets
FROM v_movie_ticket_sales
WHERE show_date BETWEEN '2025-11-01' AND '2025-11-30'
GROUP BY title
ORDER BY total_tickets DESC
LIMIT 5;


-- -------------------------------------------------------------
-- View all upcoming sold-out showtimes ordered by theatre and time.
-- Tests v_sold_out_showtimes and ensures seats sold = total seats.
-- -------------------------------------------------------------
SELECT *
FROM v_sold_out_showtimes
ORDER BY theatre_name, showDateTime;


-- -------------------------------------------------------------
-- View theatre utilization for the next 7 days.
-- Tests v_theatre_utilization_next_7_days view for correct
-- percentage calculation and date filtering.
-- -------------------------------------------------------------
SELECT *
FROM v_theatre_utilization_next_7_days
ORDER BY theatre_name, showDateTime;


-- -------------------------------------------------------------
-- Insert a ticket manually to test the AFTER INSERT trigger.
-- Should generate an INSERT audit entry in TicketAudit.
-- -------------------------------------------------------------
INSERT INTO Ticket (showtime_ID, seat_ID, customer_ID, price, status)
VALUES (1, 5, 3, 15.00, 'ACTIVE');


-- -------------------------------------------------------------
-- View TicketAudit to verify that the INSERT trigger logged the action.
-- Should show a row with action='INSERT' for the newly added ticket.
-- -------------------------------------------------------------
SELECT *
FROM TicketAudit
ORDER BY action_at DESC;


-- -------------------------------------------------------------
-- Attempt to double-sell the same seat.
-- Expected: BEFORE INSERT trigger blocks this with an error.
-- Tests seat availability enforcement.
-- -------------------------------------------------------------
INSERT INTO Ticket (showtime_ID, seat_ID, customer_ID, price, status)
VALUES (1, 5, 4, 15.00, 'ACTIVE');


-- -------------------------------------------------------------
-- Update a ticket to REFUNDED to test AFTER UPDATE refund trigger.
-- Expected: TicketAudit logs a 'REFUND' action.
-- -------------------------------------------------------------
UPDATE Ticket
SET status = 'REFUNDED'
WHERE ticket_ID = 1;


-- -------------------------------------------------------------
-- View audit entries for refunded ticket.
-- Should show a row where action='REFUND'.
-- -------------------------------------------------------------
SELECT *
FROM TicketAudit
WHERE ticket_id = 1
ORDER BY action_at DESC;


-- -------------------------------------------------------------
-- Test stored procedure: sell_ticket.
-- Validates showtime, seat, pricing, discount, insertion, and audit.
-- -------------------------------------------------------------
SET @new_ticket_id = 0;

CALL sell_ticket(
  1,             -- showtime_ID
  10,            -- seat_ID
  5,             -- customer_ID
  'STUDENT10',   -- discount code (optional)
  @new_ticket_id -- OUT ID
);


-- -------------------------------------------------------------
-- Show the ticket ID returned by the stored procedure.
-- -------------------------------------------------------------
SELECT @new_ticket_id AS new_ticket_id;


-- -------------------------------------------------------------
-- Verify the new ticket exists in the Ticket table.
-- -------------------------------------------------------------
SELECT *
FROM Ticket
WHERE ticket_ID = @new_ticket_id;


-- -------------------------------------------------------------
-- Verify the audit entry for the ticket created via sell_ticket.
-- -------------------------------------------------------------
SELECT *
FROM TicketAudit
WHERE ticket_id = @new_ticket_id;


-- -------------------------------------------------------------
-- Show all backup tables created by backup_script.sql.
-- Confirms that the backup script executed successfully.
-- -------------------------------------------------------------
SHOW TABLES
LIKE '%_bak_%';


-- -------------------------------------------------------------
-- Compare backup and current row counts for Theatre table.
-- Used to verify backup cloning operation.
-- -------------------------------------------------------------
SELECT COUNT(*) AS theatre_backup_count
FROM Theatre_bak_20251123;  -- update with your actual backup date

SELECT COUNT(*) AS theatre_current_count
FROM Theatre;


-- -------------------------------------------------------------
-- Example restore operation for the Showtime table.
-- Demonstrates how backed-up data can be restored.
-- -------------------------------------------------------------
TRUNCATE TABLE Showtime;

INSERT INTO Showtime
SELECT *
FROM Showtime_bak_20251123;  -- update with your actual backup date