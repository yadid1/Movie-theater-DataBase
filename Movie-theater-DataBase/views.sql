USE movie_theatre_db;
-- VIEW 1 — Top-N Movies by Tickets Sold (time-bounded)
CREATE OR REPLACE VIEW v_movie_ticket_sales AS
SELECT 
    m.movie_ID,
    m.title,
    DATE(s.showDateTime) AS show_date,
    COUNT(CASE WHEN t.status = 'ACTIVE' THEN t.ticket_ID END) AS tickets_sold
FROM Movie m
JOIN Showtime s 
      ON s.movie_ID = m.movie_ID
LEFT JOIN Ticket t 
      ON t.showtime_ID = s.showtime_ID
GROUP BY 
    m.movie_ID,
    m.title,
    DATE(s.showDateTime);



-- VIEW 2 — Upcoming Sold-Out Showtimes per Theatre
CREATE OR REPLACE VIEW v_sold_out_showtimes AS
SELECT
    th.theatre_ID,
    th.name AS theatre_name,
    a.auditorium_ID,
    a.name AS auditorium_name,
    s.showtime_ID,
    m.title AS movie_title,
    s.showDateTime,
    sc.total_seats,
    COUNT(t.ticket_ID) AS active_tickets
FROM Showtime s
JOIN Auditorium a  ON a.auditorium_ID = s.auditorium_ID
JOIN Theatre th    ON th.theatre_ID = a.theatre_ID
JOIN Movie m       ON m.movie_ID = s.movie_ID
JOIN (
        SELECT auditorium_ID, COUNT(*) AS total_seats
        FROM Seat
        GROUP BY auditorium_ID
     ) sc 
     ON sc.auditorium_ID = s.auditorium_ID
LEFT JOIN Ticket t 
     ON t.showtime_ID = s.showtime_ID 
    AND t.status = 'ACTIVE'
WHERE s.showDateTime >= NOW()
GROUP BY
    th.theatre_ID, th.name,
    a.auditorium_ID, a.name,
    s.showtime_ID, m.title,
    s.showDateTime, sc.total_seats
HAVING active_tickets = sc.total_seats;


-- VIEW 3 — Theatre Utilization (Next 7 Days)
CREATE OR REPLACE VIEW v_theatre_utilization_next_7_days AS
SELECT
    th.theatre_ID,
    th.name AS theatre_name,
    a.auditorium_ID,
    a.name AS auditorium_name,
    s.showtime_ID,
    m.title AS movie_title,
    s.showDateTime,
    sc.total_seats,
    COUNT(t.ticket_ID) AS sold_seats,
    ROUND((COUNT(t.ticket_ID) / sc.total_seats) * 100, 2) AS utilization_percent
FROM Showtime s
JOIN Auditorium a  ON s.auditorium_ID = a.auditorium_ID
JOIN Theatre th    ON a.theatre_ID = th.theatre_ID
JOIN Movie m       ON s.movie_ID = m.movie_ID
JOIN (
        SELECT auditorium_ID, COUNT(*) AS total_seats
        FROM Seat
        GROUP BY auditorium_ID
     ) sc 
     ON sc.auditorium_ID = s.auditorium_ID
LEFT JOIN Ticket t 
     ON t.showtime_ID = s.showtime_ID
    AND t.status = 'ACTIVE'
WHERE s.showDateTime BETWEEN NOW() 
    AND DATE_ADD(NOW(), INTERVAL 7 DAY)
GROUP BY
    th.theatre_ID, th.name,
    a.auditorium_ID, a.name,
    s.showtime_ID, m.title,
    s.showDateTime, sc.total_seats;