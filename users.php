<?php
require_once 'db.php';

function content()
{
    global $conn;

    // Ensure only admin can access
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo "<div class='alert alert-danger'>Access denied.</div>";
        return;
    }

    $result = $conn->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC");
    ?>

    <div class="card shadow">
        <div class="card-header bg-blue-100 text-white">
            <h5 class="mb-0">User List</h5>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date("h:i a, d M Y",strtotime($user['created_at']))) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">No users found.</div>
            <?php endif; ?>
        </div>
    </div>

<?php
}

include("layout.php");
?>
