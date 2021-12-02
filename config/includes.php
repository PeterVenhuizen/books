<?php
	$ROOT = $_SERVER['DOCUMENT_ROOT'];
    $BASE_PATH = $ROOT . '/books/';
    $USERS_PATH = $ROOT . '/users/';

    include_once($ROOT . '/config/db.php');

    require_once $USERS_PATH . 'controllers/Auth.php';
    $auth = new Auth($pdo);

    require_once $USERS_PATH . 'controllers/Util.php';
    $util = new Util();
?>