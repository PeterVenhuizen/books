<?php
    require_once('../config.php');

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
        $stmt = $pdo->prepare("UPDATE `books` SET 
            title = :title,
            author = :author,
            lastname = :lastname,
            description = :desc,
            genre = :genre,
            cover = :cover_url,
            lang = :lang,
            ean = :ean,
            is_ebook = :is_ebook,
            series = :series,
            book_number = :series_number,
            read_start = :read_start,
            read_end = :read_end
            WHERE id = :id");
        $stmt->execute($params);
        $messages['feedback'] = "I think that worked";
        $messages['success'] = True;
    } catch (PDOException $e) {
        //
        $messages['feedback'] = $e->getMessage();
    }

    // return json_encode($results);
    echo json_encode($messages);
    exit;

?>