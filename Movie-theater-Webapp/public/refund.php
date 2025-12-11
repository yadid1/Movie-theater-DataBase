<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

$ticketID = intval($_GET['ticket'] ?? 0);
$success = null;
$error = null;

if ($ticketID <= 0) {
    $error = "Invalid ticket ID.";
} else {
    // Get ticket info
    $stmt = $pdo->prepare("
        SELECT t.ticket_ID, t.status
        FROM Ticket t
        WHERE t.ticket_ID = :id
    ");
    $stmt->execute([':id' => $ticketID]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        $error = "Ticket not found.";
    } elseif ($ticket['status'] !== 'ACTIVE') {
        $error = "This ticket cannot be refunded (it is already {$ticket['status']}).";
    } else {
        try {
            // Mark ticket as refunded
            $update = $pdo->prepare("
                UPDATE Ticket
                SET status = 'REFUNDED'
                WHERE ticket_ID = :id
            ");
            $update->execute([':id' => $ticketID]);

            $success = "Ticket #{$ticketID} has been refunded successfully.";
        } catch (Exception $e) {
            $error = "Refund failed: " . $e->getMessage();
        }
    }
}
?>

<h2>Refund Ticket</h2>

<?php if ($error): ?>
    <div style="background:#5a1a1a; color:white; padding:1rem; margin-bottom:1rem;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="background:#1f5a1f; color:white; padding:1rem; margin-bottom:1rem;">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<p><a href="tickets.php">← Back to My Tickets</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
