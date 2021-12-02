// deconstruct the URL
let pathname = window.location.pathname;
pathname = (pathname.endsWith('/')) ? pathname.slice(0, -1) : pathname; // remove trailing slash
const pathArray = pathname.replace('/books/', '').split('/');

// get the id
postData('controllers/view.php', { view: 'single', match: parseInt(pathArray[0]) })
    .then(data => {
        if (data['books'].length) {
            
            book = data['books'].pop();
            // console.log(book);

            // fill the form
            $('#book-id').val(book['id']);
            $('#book-title').val(book['title']);
            $('#book-author').val(book['author']);
            $('#book-lastname').val(book['lastname']);
            $('#book-desc').val(book['description']);
            $('#book-cover-url').val(book['cover']);
            $('#book-lang').val(book['lang']);
            $('#book-ean').val(book['ean']);
            $('#book-series').val(book['series']);
            $('#book-series-number').val(parseInt(book['book_number']));
            if (book['read_start'] !== "1970-01-01") { $('#book-read-start').val(book['read_start']); }
            if (book['read_end'] !== "1970-01-01") { $('#book-read-end').val(book['read_end']); }

            // empty and add genre tags
            $('#book-genres').empty();
            for (let g of book['genre'].split(';')) {
                $('#book-genres').append(createGenreElement(g));
            }

            // check if is ebook
            if (book['is_ebook'] === '1') {
                $('#is-ebook').prop('checked', true);
            }

        } else {
            console.log('help');
        }
    })
    .catch((error) => {
        console.error(error);
    })

// add genre 
$('#submit-genre').click(function(e) {
    e.preventDefault();
    const genre = $('#add-book-genre').val();

    // check if empty
    if (genre.length) {

        // collect all current genres
        const currentGenres = [...document.querySelectorAll('.genre-tag')].map((el) => el.innerText.toLowerCase());

        // check if it is not a duplicate
        if (!currentGenres.includes(genre.toLowerCase())) {
            $('#book-genres').append(createGenreElement(genre));
        } else {
            // show duplicate error message and fade out after 5 seconds
        }

        // empty input field
        $('#add-book-genre').val('');

    } else {
        // show empty warning message and fade out
    }

});

$('body').on('click', '.banner-close', function(e) {
    $(this).closest('.banner-wrapper').addClass('hide');
});

// submit form
$('#submit-form').click(function(e) {
    jQuery('#book-form').submit(function(e) {
        e.preventDefault();

        // collect the form data
        const myForm = document.getElementById('book-form');
        const formData = new FormData(myForm);
        const myFormData = Object.fromEntries(formData);
        myFormData['book-genre'] = [...document.querySelectorAll('.genre-tag')].map((el) => el.innerText).join(';');

        // replace dashes by underscores, because MySQL doesn't like dashes
        Object.keys(myFormData).map((k, i) => {
            myFormData[k.replaceAll(/-/g, '_').replace('book_', '')] = myFormData[k];
            delete myFormData[k];
        });

        if (myFormData['series_number'] === '') { myFormData['series_number'] = '0'; }
        if (myFormData['read_start'] === '') { myFormData['read_start'] = '1970-01-01'; }
        if (myFormData['read_end'] === '') { myFormData['read_end'] = '1970-01-01'; }
        console.log(myFormData);

        postData('controllers/edit.php', myFormData)
            .then(data => {


                $('.banner').empty();
                $('.banner').append($('<i/>', { 'class': 'fas fa-times banner-close' }));

                // check return messages
                if (data['success']) {
                    $('.banner').append([
                        $('<p/>', { text: 'Updated completed successfully!' }).prepend($('<i/>', { 'class': 'fas fa-check' })),
                        $('<p/>', { text: 'You will be automatically redirected to the previous page in 3 seconds.' })
                    ]);

                    // reload 
                    setTimeout(() => {
                        window.location.href = window.location.pathname.replace('edit/', '');
                    }, 3000);

                } else {
                    $('.banner').append([
                        $('<p/>', { text: 'Hmm, something has gone wrong:' }).prepend($('<i/>', { 'class': 'fas fa-exclamation-circle' })),
                        $('<p/>', { text: data['feedback'] })
                    ]);
                }

                $('.banner-wrapper').removeClass('hide');            

            })
            .catch((error) => {
                // something went wrong
                console.error('Error:', error);
            })
    });
});