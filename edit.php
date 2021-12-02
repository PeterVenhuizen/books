<?php 
    include 'components/header.php';
    if (!$_SESSION['is_logged_in'] || $_SESSION['privileges'] < 1) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/books/">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <script defer src="js/functions.js"></script>
    <script defer src="js/edit.js"></script>
</head>
<body>
    <div id="app">
        <nav><?php include 'components/nav.php'; ?></nav>
        <main>

            <section id="form-section">
                <form action="" method="POST" id="book-form">
    
                    <input type="number" id="book-id" name="book-id" hidden>

                    <label for="book-title">Title: <span class="req">*</span></label>
                    <input type="text" name="book-title" id="book-title" required>
    
                    <div class="flex-row">
                        <div class="form-group">
                            <label for="book-author">Author(s): <span class="req">*</span></label>
                            <input type="text" name="book-author" id="book-author" placeholder="Separate multiple authors by a semicolon (&quot;;&quot;)" required>
                        </div>
        
                        <div class="form-group">
                            <label for="book-lastname">Author lastname: <span class="req">*</span></label>
                            <input type="text" name="book-lastname" id="book-lastname" placeholder="Lastname of the author to sort on" required>
                        </div>
                    </div>

                    <label for="book-desc">Description: </label>
                    <textarea name="book-desc" id="book-desc" cols="50" rows="10"></textarea>
    
                    <label for="book-genre">Genre(s): </label>
                    <div id="book-genres" name="book-genres"></div>
                    <input type="text" name="book-genre" id="add-book-genre" placeholder="Add each genre individually and click &quot;Add&quot;">
                    <button id="submit-genre">Add</button>
    
                    <label for="book-cover-url">Cover image URL: </label>
                    <input type="text" name="book-cover-url" id="book-cover-url" placeholder="Link to the cover image. The image will be downloaded automatically.">
    
                    <div class="flex-row">
                        <div class="form-group">
                            <label for="book-lang">Language: </label>
                            <input type="text" name="book-lang" id="book-lang">
                        </div>

                        <div class="form-group">
                            <label for="book-ean">EAN: </label>
                            <input type="text" name="book-ean" id="book-ean">
                        </div>
                    </div>
    
                    <label>Is it an eBook?</label>
                    <div>
                        <input type="radio" name="book-is-ebook" id="is-ebook" value=1>
                        <label for="is-ebook">Yes</label>
                    </div>
                    <div>
                        <input type="radio" name="book-is-ebook" id="isnt-ebook" value=0 checked>
                        <label for="isnt-ebook">No</label>
                    </div>
    
                    <div class="flex-row">
                        <div class="form-group">
                            <label for="book-series">Book series: </label>
                            <input type="text" name="book-series" id="book-series">
                        </div>

                        <div class="form-group">
                            <label for="book-series-number">Number in the series: </label>
                            <input type="number" name="book-series-number" id="book-series-number">
                        </div>
                    </div>
    
                    <div class="flex-row">
                        <div class="form-group">
                            <label for="book-read-start">Started on: </label>
                            <input type="date" name="book-read-start" id="book-read-start">
                        </div>

                        <div class="form-group">
                            <label for="book-read-end">Finished on: </label>
                            <input type="date" name="book-read-end" id="book-read-end">
                        </div>
                    </div>
    
                    <button id="submit-form" class="btn-submit">Submit</button>
    
                </form>
    
            </section>

        </main>

        <div class="banner-wrapper hide">
            <div class="banner">
                <i class="fas fa-times banner-close"></i>
            </div>
        </div>

    </div>
</body>
</html>