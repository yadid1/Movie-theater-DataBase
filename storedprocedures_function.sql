USE movie_theatre_db;
--NOTE run this line  if using older verison of mysql through xamppp 
 SET GLOBAL log_bin_trust_function_creators = 1;
-- =============================================================
-- Stored Procedure: sell_ticket
-- Purpose: Validates input, checks seat availability, calculates
-- ticket price (based on showtime format and optional discount
-- code), inserts a new Ticket row, and returns the new ticket_ID.
-- This procedure works together with Ticket triggers for auditing.
-- =============================================================

DELIMITER $$

CREATE PROCEDURE sell_ticket (
    IN  p_showtime_id   INT,
    IN  p_seat_id       INT,
    IN  p_customer_id   INT,           -- can be NULL for guest checkout
    IN  p_discount_code VARCHAR(20),   -- simple codes like 'STUDENT10'
    OUT p_ticket_id     INT
)
BEGIN
    DECLARE v_auditorium_showtime INT;
    DECLARE v_auditorium_seat     INT;
    DECLARE v_format              VARCHAR(10);
    DECLARE v_base_price          DECIMAL(8,2);
    DECLARE v_final_price         DECIMAL(8,2);
    DECLARE v_seat_taken          INT;

    -- 1) Validate showtime and get its auditorium + format
    SELECT auditorium_ID, format
    INTO v_auditorium_showtime, v_format
    FROM Showtime
    WHERE showtime_ID = p_showtime_id;

    IF v_auditorium_showtime IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Invalid showtime_id.';
    END IF;

    -- 2) Validate seat and get its auditorium
    SELECT auditorium_ID
    INTO v_auditorium_seat
    FROM Seat
    WHERE seat_ID = p_seat_id;

    IF v_auditorium_seat IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Invalid seat_id.';
    END IF;

    -- 3) Ensure seat belongs to same auditorium as showtime
    IF v_auditorium_seat <> v_auditorium_showtime THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Seat does not belong to the showtime auditorium.';
    END IF;

    -- 4) Check if seat is already sold (ACTIVE ticket exists)
    SELECT COUNT(*)
    INTO v_seat_taken
    FROM Ticket
    WHERE showtime_ID = p_showtime_id
      AND seat_ID     = p_seat_id
      AND status      = 'ACTIVE';

    IF v_seat_taken > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Seat already sold for this showtime.';
    END IF;

    -- 5) Determine base price by format
    IF v_format = 'IMAX' THEN
        SET v_base_price = 20.00;
    ELSEIF v_format = '3D' THEN
        SET v_base_price = 18.00;
    ELSE
        SET v_base_price = 15.00;
    END IF;

    SET v_final_price = v_base_price;

    -- 6) Apply simple discount codes (no separate table needed)
    IF p_discount_code IS NOT NULL THEN
        IF p_discount_code = 'STUDENT10' THEN
            SET v_final_price = v_base_price * 0.90;  -- 10% off
        ELSEIF p_discount_code = 'SENIOR15' THEN
            SET v_final_price = v_base_price * 0.85;  -- 15% off
        END IF;
    END IF;

    -- 7) Insert ticket (triggers will audit and enforce availability again)
    INSERT INTO Ticket (showtime_ID, seat_ID, customer_ID, price, status)
    VALUES (p_showtime_id, p_seat_id, p_customer_id, v_final_price, 'ACTIVE');

    -- 8) Return the new ticket_ID
    SET p_ticket_id = LAST_INSERT_ID();
END$$

DELIMITER ;