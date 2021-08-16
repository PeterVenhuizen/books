<?php
    require_once('../config.php');

    // get the json POST data
    $params = json_decode(file_get_contents('php://input'), true);

    $messages = array(
        'feedback' => '',
        'success' => False
    );

    // check if the db doesn't already contain a book with the same title 
    // and the same author
    $query = "SELECT * FROM books WHERE title = :title AND author = :author";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute(
            array(
                ':title' => $params['book-title'],
                ':author' => $params['book-author']
            )
        );
    } catch (PDOException $e) {
        $messages['feedback'] = $e->getMessage();
    }

    // no book found, continue
    if ($stmt->rowCount() === 0) {

        // save the image
        // to-do remove apostrophe
        $image = '../img/covers/' . strtolower(str_replace(' ', '_', $params['book-title'])) . '.' . end(explode('.', $params['book-cover-url']));
        copy($params['book-cover-url'], $image);

        // store in the mysql table
        $query = "INSERT INTO books 
            (title, author, lastname, description, genre, cover, lang, ean, is_ebook, series, book_number, read_start, read_end)
            VALUES
            (:title, :author, :lastname, :desc, :genre, :cover, :lang, :ean, :is_ebook, :series, :book_number, :read_start, :read_end)";
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute(
                array(
                    ':title' => $params['book-title'],
                    ':author' => $params['book-author'],
                    ':lastname' => $params['book-lastname'],
                    ':desc' => nl2br($params['book-desc']),
                    ':genre' => $params['book-genre'],
                    ':cover' => $image,
                    ':lang' => $params['book-lang'],
                    ':ean' => $params['book-ean'],
                    ':is_ebook' => (int)$params['book-is-ebook'],
                    ':series' => $params['book-series'],
                    ':book_number' => (int)$params['book-series-number'],
                    ':read_start' => date('Y-m-d H:i:s', strtotime($params['book-read-start'])),
                    ':read_end' => date('Y-m-d H:i:s', strtotime($params['book-read-end']))
                )
            );
            $messages['success'] = True;

            // create the lastname view
            // CREATE VIEW `lastname` AS SELECT author, SUBSTRING_INDEX(author, ' ', -1) AS lastname FROM books GROUP BY lastname, author ORDER BY lastname
            // try {
            //     $stmt = $pdo->prepare("ALTER VIEW `lastname` AS SELECT author, SUBSTRING_INDEX(author, ' ', -1) AS lastname FROM books GROUP BY lastname, author ORDER BY lastname");
            //     $stmt->execute();
            // } catch (PDOException $e) {
            //     $messages['feedback'] = $e->getMessage();
            // }

        } catch (PDOException $e) {
            $messages['feedback'] = $e->getMessage();
        }
    }

    // duplicate, don't upload
    else {
        $messages['feedback'] = "The database already contains a book with the title 
        of \"{$params['book-title']}\" by {$params['book-author']}. No action has 
        been undertaken.";
    }

    // return error/success messages
    echo json_encode($messages);
    exit;
?>