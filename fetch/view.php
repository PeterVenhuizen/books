<?php
    require_once('../config.php');

    // get the json POST data
    $params = json_decode(file_get_contents('php://input'), true);

    $results = array(
        'feedback' => 'Nothing to see here...',
        'success' => False,
        'books' => []
    );

    // prepare the query
    switch ($params['view']) {
        case 'all':
            $stmt = $pdo->prepare("SELECT * FROM `books` ORDER BY `lastname`, `series`, `book_number` ASC LIMIT :limit OFFSET :offset");            
            break;
        case 'latest': 
            $stmt = $pdo->prepare("SELECT * FROM `books` WHERE CAST(`read_start` AS CHAR(10)) != '1970-01-01' AND CAST(`read_end` AS CHAR(10)) != '1970-01-01' ORDER BY `read_end` DESC, `read_start` DESC LIMIT :limit OFFSET :offset");
            break;
        case 'cloud':
            $stmt = $pdo->prepare("SELECT genre FROM `books`");
            break;
        case 'genre':
        case 'author':
        case 'series':
            $col = $params['view'];
            $stmt = $pdo->prepare("SELECT * FROM `books` WHERE LOWER(REPLACE(REPLACE(REGEXP_REPLACE(REGEXP_REPLACE($col, \"'|&\", ''), ' +', ' '), ' ', '_'), '.', '')) LIKE :needle ORDER BY lastname, series, book_number ASC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':needle', '%' . $params['match'] . '%', PDO::PARAM_STR);
            break;
        case 'single':
            $stmt = $pdo->prepare("SELECT * FROM `books` WHERE id = :id");
            $stmt->bindValue(':id', (int)$params['match'], PDO::PARAM_STR);
            break;
        case 'search':
            if (in_array($params['category'], ['title', 'author'])) {
                $col = $params['category'];
                $stmt = $pdo->prepare("SELECT * FROM `books` WHERE LOWER(REPLACE(REPLACE(REGEXP_REPLACE(REGEXP_REPLACE($col, \"'|&\", ''), ' +', ' '), ' ', '_'), '.', '')) LIKE :needle ORDER BY lastname, series, book_number ASC");
                $stmt->bindValue(':needle', '%' . $params['match'] . '%', PDO::PARAM_STR);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM `books` WHERE MATCH(title, author, description, lang, series, genre) AGAINST (:needle IN BOOLEAN MODE)  ORDER BY lastname, series, book_number ASC");
                $stmt->bindValue(':needle', str_replace('_', ' ', $params['match']) . '*', PDO::PARAM_STR);
            }
            break;
    }

    // run the query
    try {
        if (in_array($params['view'], ['all', 'latest', 'genre', 'series', 'author'])) {
            $stmt->bindValue(':offset', (int)$params['offset'], PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$params['limit'], PDO::PARAM_INT);
        }
        $stmt->execute();
    } catch (PDOException $e) {
        $results['feedback'] = $e->getMessage();
    }

    // see if there are any results
    if ($stmt->rowCount() > 0) {
        $results['success'] = True;

        // get all results into an array
        $records = array_map(function($row) { return $row; }, $stmt->fetchAll() );
        $results['books'] = $records;
    } else {
        $results['feedback'] = 'No books found :(';
    }

    // return
    echo json_encode($results);
    exit;

?>