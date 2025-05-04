<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ticket ID.");
}

$ticket_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT t.*, u.email FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Ticket not found.");
}
$ticket = $result->fetch_assoc();

// Fetch replies for the ticket
$reply_stmt = $conn->prepare("SELECT r.*, u.email FROM ticket_replies r JOIN users u ON r.user_id = u.id WHERE r.ticket_id = ? ORDER BY r.created_at DESC");
$reply_stmt->bind_param("i", $ticket_id);
$reply_stmt->execute();
$replies_result = $reply_stmt->get_result();
$replies = $replies_result->fetch_all(MYSQLI_ASSOC);
$reply_stmt->close();

include("layout.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_update'])) {
    $new_status = $_POST['status'];
    $ticket_id = intval($_GET['id']); // already validated above

    // Update ticket status
    $stmt = $conn->prepare("UPDATE tickets SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $ticket_id);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>window.location.href = 'view_ticket.php?id={$ticket_id}';</script>";
        // $result =  '<div class="alert alert-warning alert-dismissible fade show" role="alert">Ticket status updated successfully. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $result =  '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $stmt->error . ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $ticket_id = $_REQUEST['id'];
    $reply = trim($_POST['reply']);
    $stmt = $conn->prepare("INSERT INTO ticket_replies (user_id, ticket_id, reply, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $user_id, $ticket_id, $reply);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>window.location.href = 'view_ticket.php?id={$ticket_id}';</script>";
        $result =  '<div class="alert alert-warning alert-dismissible fade show" role="alert">Ticket replied successfully. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $result =  '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $stmt->error . ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    $stmt->close();
}
function content()
{
    global $ticket, $replies;
?>

    <?php if (isset($result)) {
        echo $result;
    } ?>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <!-- <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="close">Close</option> -->
                        <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                        <option value="pending" <?= $ticket['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="close" <?= $ticket['status'] === 'close' ? 'selected' : '' ?>>Close</option>
                    </select>
                    <button type="submit" name="status_update" class="btn btn-primary mt-2">Update</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
    <!-- ticket details -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="ti ti-ticket"></i>
                <span class="p-l-5">Ticket #<?= htmlspecialchars($ticket['id']) ?></span>
            </h5>
        </div>
        <div class="card-body border-bottom py-2">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="d-inline-block mb-0"><?= htmlspecialchars($ticket['heading']) ?></h4>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-star">
                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="not available">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star icon-svg-warning wid-20">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-bottom card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <a href="#replyBox" class="btn btn-sm my-2 btn-light-success"><i class="me-2 feather icon-message-square"></i> Post a reply</a>
                    <button type="button" class="btn btn-sm my-2 btn-light-warning">
                        Status: <?= ucfirst($ticket['status']) ?>
                    </button>
                    <button type="button" class="btn btn-sm my-2 btn-light-danger"> <?= htmlspecialchars($ticket['service']) ?> deparment </button>
                </div>
            </div>
        </div>
        <div class="border-bottom card-body">
            <div class="row">
                <div class="col-sm-auto mb-3 mb-sm-0">
                    <div class="d-sm-inline-block d-flex align-items-center">
                        <img class="wid-60 img-radius mb-2" src="https://ui-avatars.com/api/?name=<?= htmlspecialchars($ticket['email']) ?>&color=57c149&background=EBF4FF" alt="Generic placeholder image ">
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <div class="">
                                <h4 class="d-inline-block"><?= htmlspecialchars($ticket['email']) ?></h4>
                                <p class="text-muted"><i class="wid-20 material-icons-two-tone text-center f-14 me-2">calendar_today</i> <?= htmlspecialchars(date("h:i a, d M Y", strtotime($ticket['created_at']))) ?></p>
                            </div>
                        </div>
                        <div class="col-auto">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block f-20">
                                    <a href="#" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="not available">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 icon-svg-danger wid-20">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="">
                        <?= nl2br(htmlspecialchars($ticket['description'])) ?>
                    </div>
                    <?php if ($ticket['attachment'] !== null): ?>
                    <div class="mt-5">
                        <img src="attachment/<?= nl2br(htmlspecialchars($ticket['attachment'])) ?>" width="100%" alt="file not found">
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- reply box -->
    <div class="card">
        <div class="card-header py-3">
            <h3>Your reply</h3>
        </div>
        <!-- <div class="card-body"></div> -->
        <div class="card-body py-2">
            <form action="" method="post">
                <textarea class="form-control border-0 shadow-none px-2" id="replyBox" name="reply" placeholder="Type your reply" rows="2" spellcheck="false"></textarea>
                <hr class="my-2" />
                <div class="d-sm-flex align-items-center">
                    <ul class="list-inline me-auto mb-0"></ul>
                    <ul class="list-inline ms-auto mb-0">
                        <li class="list-inline-item">
                            <button class="btn btn-primary" type="submit" name="submit">Reply</button>
                        </li>
                    </ul>
                </div>
            </form>
        </div>
    </div>

    <!-- reply details -->
    <?php if (!empty($replies)): ?>
        <?php foreach ($replies as $reply): ?>
            <div class="card ticket-card close-ticket">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-auto mb-3 mb-sm-0">
                            <div class="d-sm-inline-block d-flex align-items-center"><img class="media-object wid-60 img-radius" src="https://ui-avatars.com/api/?name=<?= htmlspecialchars($reply['email']) ?>&color=57c149&background=E0F1FF" alt="Generic placeholder image "></div>
                        </div>
                        <div class="col">
                            <div class="popup-trigger">
                                <div class="h5 font-weight-bold"><?= htmlspecialchars($reply['email']) ?> <small class="badge bg-light-secondary ms-2">Replied</small></div>
                                <div class="help-sm-hidden">
                                    <ul class="list-unstyled mt-2 mb-2 text-muted">
                                        <li class="d-sm-inline-block d-block mt-1 mx-1"><i class="wid-20 material-icons-two-tone text-center f-14 me-2">calendar_today</i> <?= date("h:i a, d M Y", strtotime($reply['created_at'])) ?></li>
                                    </ul>
                                </div>
                                <div class="help-md-hidden">
                                    <div class="bg-body mb-3 p-3"><?= nl2br(htmlspecialchars($reply['reply'])) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


<?php
}

?>