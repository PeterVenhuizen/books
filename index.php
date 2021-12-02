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
    <title>Books</title>
    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <script defer src="js/functions.js"></script>
    <script defer src="js/index.js"></script>
</head>
<body>
    <div id="app">
        <nav><?php include 'components/nav.php'; ?></nav>
        <main>

            <form id="book-search">
                <a href="all/" class="show-all">Just show me everything!</a>
                <i class="fas fa-search"></i>
                <input type="search" name="search-term" id="search-books" placeholder="Search my books">
                <div class="search-options">
                    <input type="radio" id="general" name="category" value="general" checked>
                    <label for="general">General</label>

                    <input type="radio" id="title" name="category" value="title">
                    <label for="title">Title</label>

                    <input type="radio" id="author" name="category" value="author">
                    <label for="author">Author</label>
                </div>
                <button id="search-submit" hidden></button>
            </form>

            <section id="last-read">
                <!-- <h3>Most recently read books:</h3> -->

                <div id="last-ten"></div>
                <a href="latest/">More recently read books ...</a>
            </section>

            <ul class="genre-cloud" role="navigation" aria-label="Book genre tag cloud"></ul>

        </main>
    </div>
</body>
</html>