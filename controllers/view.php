<?php
    // require_once('../config/includes.php');
    require_once('../config/includeFromBottom.php');

    // get the json POST data
    $params = json_decode(file_get_contents('php://input'), true);

    $results = array(
        'feedback' => 'Nothing to see here...',
        'success' => False,
        'books' => []
    );

    // select the query
    switch ($params['view']) {
        case 'all':
            $stmt = $db->run("SELECT * FROM `books` 
                ORDER BY `lastname`, `series`, `book_number` ASC 
                LIMIT ?, ?", 
                [$params['offset'], $params['limit']]);
            break;
        case 'latest': 
            $stmt = $db->run("SELECT * FROM `books`
                WHERE CAST(`read_start` AS CHAR(10)) != '1970-01-01'
                AND CAST(`read_end` AS CHAR(10)) != '1970-01-01'
                ORDER BY `read_end` DESC, `read_start` DESC
                LIMIT ?, ?",
                [$params['offset'], $params['limit']]);
            break;
        case 'cloud':
            $stmt = $db->run("SELECT `genre` FROM `books`");
            break;
        case 'genre':
        case 'author':
        case 'series':
            $col = $params['view'];
            $stmt = $db->run("SELECT * FROM `books`
                WHERE LOWER(REPLACE(REPLACE(REGEXP_REPLACE(REGEXP_REPLACE($col, \"'|&\", ''), ' +', ' '), ' ', '_'), '.', ''))
                LIKE ?
                ORDER BY `lastname`, `series`, `book_number` ASC
                LIMIT ?, ?",
                ['%' . $params['match'] . '%', $params['offset'], $params['limit']]);
            break;
        case 'single':
            $stmt = $db->run("SELECT * FROM `books` WHERE `id` = ?", [$params['match']]);
            break;
        case 'search':
            if (in_array($params['category'], ['title', 'author'])) {
                $col = $params['category'];
                $stmt = $db->run("SELECT * FROM `books`
                    WHERE LOWER(REPLACE(REPLACE(REGEXP_REPLACE(REGEXP_REPLACE($col, \"'|&\", ''), ' +', ' '), ' ', '_'), '.', ''))
                    LIKE ?
                    ORDER BY `lastname`, `series`, `book_number` ASC",
                    ['%' . $params['match'] . '%']);
            } else {
                $stmt = $db->run("SELECT * FROM `books`
                    WHERE MATCH(`title`, `author`, `description`, `lang`, `series`, `genre`)
                    AGAINST (? IN BOOLEAN MODE)
                    ORDER BY `lastname`, `series`, `book_number` ASC",
                    [str_replace('_', ' ', $params['match']) . '*']);
            }
            break;
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