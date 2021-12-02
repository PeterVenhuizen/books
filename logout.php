<?php
    // include 'config/includes.php'; 
    include_once('./config/includeFromTop.php');
    session_start();
    session_destroy();

    header("Location: /users/?redirectedFrom=books/");
?>