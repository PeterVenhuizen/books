<?php
    // require_once('../config/includes.php');
    require_once('../config/includeFromBottom.php');

    // get the json POST data
    $params = json_decode(file_get_contents('php://input'), true);

    $messages = array(
        'feedback' => '',
        'success' => False
    );

    // replace the image
    if (!substr($params['cover_url'], 0, strlen('../img/covers/')) !== '../img/covers/') {
        $image = '../img/covers/' . strtolower(str_replace(' ', '_', $params['title'])) . '.' . end(explode('.', $params['cover_url']));
        copy($params['cover_url'], $image);
        $params['cover_url'] = $image;
    }

    try {
        $status = $db->run("UPDATE `books` SET
            `title` = ?, `author` = ?, `lastname` = ?, `description` = ?, 
            `genre` = ?, `cover` = ?, `lang` = ?, `ean` = ?, `is_ebook` = ?,
            `series` = ?, `book_number` = ?, `read_start` = ?, `read_end` = ?
            WHERE `id` = ?", [
                $params['title'], $params['author'], $params['lastname'], 
                $params['desc'], $params['genre'], $params['cover_url'],
                $params['lang'], $params['ean'], $params['is_ebook'], 
                $params['series'], (int)$params['series_number'], 
                $params['read_start'], $params['read_end'], $params['id']
            ]);
        if ($status) $messages['success'] = True;
    } catch (PDOException $e) {
        $messages['feedback'] = $e->getMessage();
    }

    // return json_encode($results);
    echo json_encode($messages);
    exit;

?>