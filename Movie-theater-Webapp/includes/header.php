<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Theatre</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <h1 class="site-title">Movie Theatre</h1>
    <nav class="site-nav">
        <a href="index.php">Movies</a>
        <a href="showtimes.php">Showtimes</a>
        <a href="tickets.php">My Tickets</a>
        <div class="nav-group">
            <span>Reports ▾</span>
            <div class="nav-dropdown">
                <a href="reports_top_movies.php">Top Movies</a>
                <a href="reports_sold_out.php">Sold Out</a>
                <a href="reports_utilization.php">Utilization</a>
            </div>
        </div>
    </nav>
</header>
<main class="site-main">
