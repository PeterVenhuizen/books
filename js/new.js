// get bol.com API results
let offset = 0;
const limit = 10;

function getBooks(offset, limit) {
    const searchTerm = $('#search-books').val().replace(/ /g, '%2B');

    if (searchTerm.length !== 0) {
        
        async function fetchBooks() {
            let response = await fetch(`fetch/proxy.php?q=${searchTerm}&offset=${offset}&limit=${limit}`);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return await response.json();
        }

        async function loadBooks() {

            // empty any potential previous search results
            const $searchResults = $('#api-search-results');
            $('#api-search-results').empty();

            // get response data
            const data = await fetchBooks();
            console.log(data);

            // check if there are any results
            if (data['totalResultSize'] === 0) {

                // no result message
                $('#api-search-results').append(
                    $('<p/>', { text: `No results found for ${searchTerm}` })
                );

            } else {
                // to-do: include totalResultSize

                const { products } = data;
                for (let [index, book] of products.entries()) {

                    // Get metadata
                    const bookMeta = {
                        'title': book['title'],
                        'desc': '',
                        'author': [book['specsTag']],
                        'lastname': '',
                        'ean': book['ean'],
                        'coverImgUrl': '',
                        'lang': '',
                        'genres': []
                    }

                    // Get the lastname of the first author
                    bookMeta['lastname'] = bookMeta['author'][0].split(' ').pop();

                    // Description
                    try {
                        try {
                            bookMeta['desc'] = book['longDescription'].replace(/(<([^>]+)>)/gi, '');
                        } catch(e) {
                            // no long description, try to get the short description
                            bookMeta['desc'] = book['shortDescription'].replace(/(<([^>]+)>)/gi, '');
                        }
                    } catch {
                        // no short description either :/
                    }

                    // Cover image
                    try {
                        bookMeta['coverImgUrl'] = book['images'].filter(img => img['key'] === 'XL').map(i => i['url'])[0];
                    } catch(e) {
                        // no image available
                    }

                    // Language
                    try {
                        const inhoudAttr = book['attributeGroups'].filter((attr) => attr.title === 'Inhoud')[0]['attributes'];
                        bookMeta['lang'] = inhoudAttr.filter((el) => el.label === 'Taal')[0].value;
                    } catch(e) {
                        // can't determine the language
                    }

                    // Genres
                    function filterGenres(value, index, self) {
                        return self.indexOf(value) === index && value !== "Boeken";
                    }
                    for (let category of book['parentCategoryPaths']) {
                        bookMeta['genres'] = [...bookMeta['genres'], ...Object.keys(category['parentCategories']).map((key, value) => category['parentCategories'][key].name)];
                    }
                    bookMeta['genres'] = bookMeta['genres'].filter(filterGenres);

                    // console.log(index, bookMeta);
                    // create the api book article
                    $('#api-search-results').append(
                        $('<article/>', { 'class': 'book-meta', 'data-json': JSON.stringify(bookMeta) }).append([
                            $('<h3/>', { text: `${index + offset + 1} / ${offset + limit}` }),
                            $('<img/>', { 'src': `${bookMeta['coverImgUrl']}` }),
                            $('<div/>').append([
                                $('<h3/>', { text: `${bookMeta['title']}` }),
                                $('<h4/>', { text: `${bookMeta['author']}` })
                            ])
                        ])
                    );

                }

                // add autofill functionality
                $('body').on('click', '.book-meta', function() {

                    // get the metadata
                    const bookMeta = JSON.parse($(this).attr('data-json'));
                    // console.log(bookMeta);

                    // remove and add selected class
                    $('.book-meta').removeClass('search-selected');
                    $(this).addClass('search-selected');

                    // fill the form
                    $('#book-title').val(bookMeta['title']);
                    $('#book-author').val(bookMeta['author'].join(';'));
                    $('#book-lastname').val(bookMeta['lastname']);
                    $('#book-desc').val(bookMeta['desc']);
                    $('#book-cover-url').val(bookMeta['coverImgUrl']);
                    $('#book-lang').val(bookMeta['lang']);
                    $('#book-ean').val(bookMeta['ean']);

                    // empty and add genre tags
                    $('#book-genres').empty();
                    for (let g of bookMeta['genres']) {
                        $('#book-genres').append(createGenreElement(g));
                    }

                });

            }

        }

        loadBooks();

    }

}

// get the first 10 results
$('#search-submit, .fa-search').click(function(e) {
    e.preventDefault();
    getBooks(offset, limit);
});

// next 10
$('#next-ten').click(function(e) {
    offset += limit;
    $('#prev-ten').prop('disabled', false);
    getBooks(offset, limit);
})

// previous 10
$('#prev-ten').click(function(e) {
    if (offset - limit >= 0) {
        offset -= limit;
    } 
    
    if (offset === 0) {
        $('#prev-ten').prop('disabled', true);
    }
    getBooks(offset, limit);
});

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

$('body').on('click', '.modal-close', function(e) {
    $(this).closest('.modal-wrapper').addClass('hide');
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

        postData('fetch/new.php', myFormData)
            .then(data => {

                $('.modal').empty();
                $('.modal').append($('<i/>', { 'class': 'fas fa-times modal-close' }));
                $('.modal-wrapper').removeClass('hide');            

                // check return messages
                if (data['success']) {
                    $('.modal').append([
                        $('<p/>', { text: 'Book add successfully!' }).prepend($('<i/>', { 'class': 'fas fa-check' })),
                        $('<p/>', { text: 'You will be automatically redirected to the home page in 3 seconds.' })
                    ]);
                    $('.modal-wrapper').removeClass('hide');

                    // reload 
                    setTimeout(() => {
                        window.location.href = '/books/';
                    }, 3000);

                } else {
                    $('.modal').append([
                        $('<p/>', { text: 'Hmm, something has gone wrong:' }).prepend($('<i/>', { 'class': 'fas fa-exclamation-circle' })),
                        $('<p/>', { text: data['feedback'] })
                    ]);
                }

            })
            .catch((error) => {
                // something went wrong
                console.error('Error:', error);
            })
    });
});