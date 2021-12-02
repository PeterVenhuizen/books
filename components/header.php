<?php
    // include('config/includes.php'); 
    include('config/includeFromTop.php'); 

    if (!$_SESSION['is_logged_in']) {
        $util->redirect('/users/?redirectedFrom=books/');
    }
?>