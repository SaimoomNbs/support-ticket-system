<?php
include("layout.php");
// session_start();
require_once 'db.php';
// Ensure only user can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $heading = trim($_POST['heading']);
    $service = $_POST['service'];
    $status = $_POST['status']; // using the selected status
    $description = trim($_POST['description']);
    $attachment = null;

    // Handle file upload
    if (!empty($_FILES['file']['name'])) {
        $targetDir = "attachment/";
        $fileName = time() . "_" . basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $attachment = $fileName;
        } else {
            die("File upload failed.");
        }
    }

    // Insert into tickets table
    $stmt = $conn->prepare("INSERT INTO tickets (user_id, heading, service, status, description, attachment, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssss", $user_id, $heading, $service, $status, $description, $attachment);

    if ($stmt->execute()) {
        $ticket_id = $stmt->insert_id; // Get the ID of the newly inserted ticket
        $stmt->close();
        echo "<script>window.location.href = 'view_ticket.php?id={$ticket_id}';</script>";
        // header("Location: view_ticket.php?id=" . $ticket_id);
        exit();
        // $result =  "<div class='alert alert-success'>Ticket created successfully.</div>";
    } else {
        $result =  "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

function content()
{
?>
    <div class="card">
        <div class="card-body">
            <?php
            if (isset($result)) {
                echo $result;
            }
            ?>
            <form action="" method="post" enctype="multipart/form-data">
                <h2>Open A Ticket</h2>
                <hr>
                <div class="form-group mb-3">
                    <label class="form-label">Ticket heading</label>
                    <input type="text" name="heading" class="form-control" placeholder="Write shortly" required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Service</label>
                    <select name="service" class="form-control">
                        <option value="billing">Billing Dept.</option>
                        <option value="sales">Sales Dept.</option>
                        <option value="technical">Technical Dept.</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="close">Close</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5"></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Attachment</label>
                    <input type="file" class="form-control" name="file">
                </div>

                <button class="btn btn-primary" type="submit" name="submit">Create Ticket</button>
            </form>
        </div>
    </div>
<?php
}
