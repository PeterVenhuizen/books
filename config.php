<?php
    $host    = 'localhost';
    $db      = 'books';
    $user    = 'root';
    $pass    = 'mysql';
    $charset = 'utf8';

    $options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
    ];
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    try {
        $pdo = new \PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
?>