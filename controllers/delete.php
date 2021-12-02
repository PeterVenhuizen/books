<?php
    // require_once('../config/includes.php');
    require_once('../config/includeFromBottom.php');

    if ($_SESSION['is_logged_in'] && $_SESSION['privileges'] > 0 && isset($_GET['id'])) {

        // delete the cover image
        $stmt = $db->run("SELECT `cover` FROM `books` WHERE `id` = ?", [$_GET['id']]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $file_pointer = $row['cover'];

            // Use unlink() function to delete a file
            if (file_exists($row['cover'])) unlink($row['cover']);
        }

        // delete from DB
        $db->run("DELETE FROM `books` WHERE `id` = ?", [$_GET['id']]);

        header('Location: http://localhost/books/');
    } else {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    exit;

?>