<?php
    require_once('../config.php');
    
    if (isset($_GET['id'])) {
        echo $_GET['id'];

        // delete the cover image
        try {
            $stmt = $pdo->prepare("SELECT cover FROM `books` WHERE id = :id");
            $stmt->bindValue(':id', (int)$_GET['id'], PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $file_pointer = $row['cover'];

            // Use unlink() function to delete a file
            if (file_exists($row['cover'])) unlink($row['cover']);
        }

        // delete from DB
        try {
            $stmt = $pdo->prepare("DELETE FROM `books` WHERE id = :id");
            $stmt->bindValue(':id', (int)$_GET['id'], PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    }

    header('Location: http://localhost/books/');
    exit;

?>