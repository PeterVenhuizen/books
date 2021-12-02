<?php
    // require_once('../config/includes.php');
    require_once('../config/includeFromBottom.php');

    // get the json POST data
    $params = json_decode(file_get_contents('php://input'), true);

    $messages = array(
        'feedback' => '',
        'success' => False
    );

    // check if the db doesn't already contain a book with the same title 
    // and the same author
    $stmt = $db->run("SELECT * FROM `books` WHERE `title` = ? AND `author` = ?",
        [$params['book-title'], $params['book-author']]);

    // no book found, continue
    if ($stmt->rowCount() == 0) {

        // save the image
        // to-do remove apostrophe
        $image = '../img/covers/' . strtolower(str_replace(' ', '_', $params['book-title'])) . '.' . end(explode('.', $params['book-cover-url']));
        copy($params['book-cover-url'], $image);

        // store in the mysql table
        $status = $db->run("INSERT INTO `books`
            (`title`, `author`, `lastname`, `description`, `genre`, `cover`, `lang`, `ean`, `is_ebook`, `series`, `book_number`, `read_start`, `read_end`)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $params['book-title'],
                $params['book-author'],
                $params['book-lastname'],
                $params['book-desc'],
                $params['book-genre'],
                $image,
                $params['book-lang'],
                $params['book-ean'],
                $params['book-is-ebook'],
                $params['book-series'],
                (int)$params['book-series-number'],
                date('Y-m-d H:i:s', strtotime($params['book-read-start'])),
                date('Y-m-d H:i:s', strtotime($params['book-read-end']))
            ]);
        $messages['success'] = True;

        // create the lastname view
        // CREATE VIEW `lastname` AS SELECT author, SUBSTRING_INDEX(author, ' ', -1) AS lastname FROM books GROUP BY lastname, author ORDER BY lastname
        // try {
        //     $stmt = $pdo->prepare("ALTER VIEW `lastname` AS SELECT author, SUBSTRING_INDEX(author, ' ', -1) AS lastname FROM books GROUP BY lastname, author ORDER BY lastname");
        //     $stmt->execute();
        // } catch (PDOException $e) {
        //     $messages['feedback'] = $e->getMessage();
        // }

    }

    // duplicate, don't upload
    else {
        $messages['feedback'] = "The database already contains a book with the title 
        of \"{$params['book-title']}\" by {$params['book-author']}.";
    }

    // return error/success messages
    echo json_encode($messages);
    exit;
?>