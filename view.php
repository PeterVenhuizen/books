<?php 
    include 'components/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/books/">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View</title>
    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <script defer src="js/functions.js"></script>
    <script defer src="js/view.js"></script>
</head>
<body>
    <div id="app">
        <nav><?php include 'components/nav.php'; ?></nav>
        <main>
            <div id="books"></div>
            <button id="btn-load-more" hidden>Load more books <i class="fas fa-arrow-down"></i></button>
        </main>

        <div class="banner-wrapper hide">
            <div class="banner warning">
                <i class="fas fa-times banner-close"></i>
                <p><i class="fas fa-exclamation-triangle"></i> Are you sure you want to <em>permanently</em> remove this book?</p>
                <div>
                    <a href="#">Ok</a>
                    <span class="banner-close">Cancel</span>
                </div>
            </div>
        </div>

    </div>
</body>
</html>