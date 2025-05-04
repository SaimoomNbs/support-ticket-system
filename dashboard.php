<?php
require_once 'db.php';

function content() {
    ?>
    <h2>Welcome to My Website</h2>
    <p>This is the homepage using a shared layout file.</p>
    <?php
}

include("layout.php");
?>
