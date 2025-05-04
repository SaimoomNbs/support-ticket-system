<?php
require_once 'db.php';

function content()
{
    global $conn;

    // Ensure session is started and user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        echo "<div class='alert alert-danger'>Access denied. Please log in.</div>";
        return;
    }

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Role-based ticket query
    if ($role === 'user') {
        $stmt = $conn->prepare("SELECT t.*, u.email FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.user_id = ? ORDER BY t.created_at DESC");
        $stmt->bind_param("i", $user_id);
    } else {
        $stmt = $conn->prepare("SELECT t.*, u.email FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="card shadow">
        <div class="card-header bg-blue-100 text-white">
            <h5 class="mb-0">All Tickets</h5>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Heading</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['heading']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= ucfirst($row['service']) ?> deparment</td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'open' ? 'success' : ($row['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date("h:i a, d M Y",strtotime($row['created_at']))) ?></td>
                                <td><a href="view_ticket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">No tickets found.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php
}

include("layout.php");
?>
