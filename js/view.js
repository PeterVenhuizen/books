// deconstruct the URL
let pathname = window.location.pathname;
pathname = (pathname.endsWith('/')) ? pathname.slice(0, -1) : pathname; // remove trailing slash
const pathArray = pathname.replace('/books/', '').split('/');

const months = {
    0: 'January', 1: 'February', 2: 'March', 
    3: 'April', 4: 'May', 5: 'June',
    6: 'July', 7: 'August', 8: 'September',
    9: 'October', 10: 'November', 11: 'December'
};

const bookSearch = function(params) {

    postData('fetch/view.php', params)
        .then(data => {

            console.log(data);
            $('#books').append(
                $('<h1/>', { 'class': 'search-h1', html: `<em>${data['books'].length}</em> results for "<em>${params['match']}</em>"` } )
            );

            // display the book covers
            for (let book of data['books']) {

                $('#books').append(
                    $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/`, 'class': 'book' }).append(
                        $('<img/>', { 'src': `books/${book['cover']}`, 'alt': `${book['cover']} cover image` })
                    )
                );

            }

        })
        .catch((error) => {
            console.error(error);
        })    
}

const booksAndLetters = function(params) {

    $('#btn-load-more').show();

    postData('fetch/view.php', params)
        .then(data => {

            // check which letters are already there
            let letters = $('.big-letter').map(function() { return $(this).text(); }).get();
            let firstLetter = (letters.length) ? letters.pop() : 'A';

            // if the number of books is smaller than the limit, 
            // hide the show more button
            if (data['books'].length < params['limit']) {
                $('#btn-load-more').hide();
            }

            // display the book covers
            for (let book of data['books']) {

                // Add big letter
                if (firstLetter !== book['lastname'][0]) {
                    firstLetter = book['lastname'][0];
                    $('#books').append(
                        $('<div/>', { 'class': 'big-letter', text: firstLetter })
                    );
                }

                $('#books').append(
                    $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/`, 'class': 'book' }).append(
                        $('<img/>', { 'src': `books/${book['cover']}`, 'alt': `${book['cover']} cover image` })
                    )
                );
            }

        })
        .catch((error) => {
            console.error(error);
        })    
}

const booksAndYears = function(params) {

    $('#btn-load-more').show();

    postData('fetch/view.php', params)
        .then(data => {

            // check which letters are already there
            let years = $('.year').map(function() { return parseInt($(this).text()); }).get();

            // if the number of books is smaller than the limit, 
            // hide the show more button
            if (data['books'].length < params['limit']) {
                $('#btn-load-more').hide();
            }

            // display the book covers
            for (let book of data['books']) {

                // get the dates and check the year
                let readStart = new Date(book['read_start']);
                let readEnd = new Date(book['read_end']);
                if (!years.includes(readStart.getFullYear())) {
                    $('#books').append(
                        $('<div/>', { 'class': 'year', text: readStart.getFullYear() })
                    );
                    years.push(readStart.getFullYear());
                }

                // shorten the description, if necessary
                // Create description substring split at the first space after maxChar,
                // or at the end of the description if no space is found after maxChar
                const maxChar = 500;
                let desc = (book['description'].length > maxChar && book['description'].indexOf(' ', maxChar) !== -1) ? book['description'].substring(0, book['description'].indexOf(' ', maxChar)) : book['description'];

                if (book['description'].length > maxChar) {
                    desc += `<a href="${book['id']}/${clean4URL(book['title'])}/"> ... more</a>`;
                }

                $('#books').append(
                    $('<div/>', { 'class': 'read-book' }).append([
                        $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/` }).append($('<img/>', { 'src': `books/${book['cover']}`, 'alt': `${book['cover']} cover image` })), 
                        $('<div/>', { 'class': 'book-info' }).append([
                            $('<h2/>', { html: `<a href="${book['id']}/${clean4URL(book['title'])}/">${book['title']}</a>` }),
                            (book['series'].length) ? $('<span/>', { html: `(<a href="series/${clean4URL(book['series'])}/">${book['series']}</a> #${book['book_number']})` }) : '',
                            $('<h4/>', { text: 'by ' }).append($('<em/>', { html: `<a href="author/${clean4URL(book['author'])}/">${book['author']}</a>` })),
                            $('<p/>', { html: desc }),
                            $('<hr/>'),
                            $('<span/>', { 'class': 'last-read', html: `Last read from <time datetime="${book['read_start']}">${months[readStart.getMonth()]} ${readStart.getDate()}</time> till <time datetime="${book['read_end']}">${months[readEnd.getMonth()]} ${readEnd.getDate()}</time>` })
                        ])
                    ])
                );

            }

        })
        .catch((error) => {
            console.error(error);
        })    
}

const booksAndHeader = function(params) {

    $('#btn-load-more').show();

    postData('fetch/view.php', params)
        .then(data => {

            console.log(data['books'].length);

            // hide show more
            if (data['books'].length < params['limit']) {
                $('#btn-load-more').hide();
            }

            // display the book covers
            for (let [index, book] of data['books'].entries()) {

                if (index === 0) {
                    $('#books').append(
                        $('<h1/>', { text: (params['view'] === 'author') ? book['author'] : book['series'] })
                    );
                }

                $('#books').append(
                    $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/`, 'class': 'book' }).append(
                        $('<img/>', { 'src': `books/${book['cover']}`, 'alt': `${book['cover']} cover image` })
                    )
                );

            }

        })
        .catch((error) => {
            console.error(error);
        })    
}

const singleBook = function(params) {
    postData('fetch/view.php', params)
        .then(data => {

            const book = data['books'].pop();
            console.log(book);

            // get the dates and check if this book was or is being read
            let readStart = new Date(book['read_start']);
            let readEnd = new Date(book['read_end']);

            let readHistory = '';
            if (book['read_start'] === "1970-01-01" && book['read_end'] === "1970-01-01") {
                // not read
                readHistory = 'No reading history information available';
            } else if (book['read_end'] === "1970-01-01") {
                readHistory = `Started reading on <time datetime="${book['read_start']}">${months[readStart.getMonth()]} ${readStart.getDate()}</time>`;
            } else {
                readHistory = `Last read from <time datetime="${book['read_start']}">${months[readStart.getMonth()]} ${readStart.getDate()}</time> till <time datetime="${book['read_end']}">${months[readEnd.getMonth()]} ${readEnd.getDate()}</time>`;
            }

            $('#books').append(
                $('<div/>', { 'class': 'single-book' }).append([
                    $('<aside/>').append([
                        $('<img/>', { 'src': `books/${book['cover']}`, 'alt': `${book['cover']} cover image` }),
                        $('<div/>', { 'class': 'edit-delete' }).append([
                            $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/edit/` }).append($('<i/>', { 'class': 'fas fa-edit btn-edit'})),
                            $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/delete/` }).append($('<i/>', { 'class': 'fas fa-trash btn-delete'}))
                        ])
                    ]),
                    $('<div/>', { 'class': 'book-info' }).append([
                        $('<h2/>', { text: book['title'] }),
                        (book['series'].length) ? $('<span/>', { html: `(<a href="series/${clean4URL(book['series'])}/">${book['series']}</a> #${book['book_number']})` }) : '',
                        $('<h4/>', { text: 'by ' }).append($('<em/>', { html: `<a href="author/${clean4URL(book['author'])}/">${book['author']}</a>` })),
                        $('<p/>', { text: book['description'] }),
                        $('<hr/>'),
                        $('<span/>', { 'class': 'last-read', html: readHistory }),
                        $('<div/>', { 'class': 'genre-tags', html: book['genre'].split(';').map((g) => `<div class="genre-tag"><a href="genre/${clean4URL(g)}/">${g}</a></div>`).join('') }),
                    ])
                ])
            );

            $('#app').append([
                $('<div/>', { 'class': 'modal-wrapper hide' }).append(
                    $('<div/>', { 'class': 'modal warning' }).append([
                        $('<i/>', { 'class': 'fas fa-times modal-close' }),
                        $('<p/>', { html: 'Are you sure want to <em>permanently</em> remove this book?' }).prepend($('<i/>', { 'class': 'fas fa-exclamation-triangle' })),
                        $('<div/>').append([
                            $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/delete/`, text: 'Ok' }),
                            $('<span/>', { 'class': 'modal-close', text: 'Cancel' })
                        ])
                    ])
                )
            ]);

        })
        .catch((error) => {
            console.error(error);
        })  

    // delete
    $('body').on('click', '.btn-delete', function(e) {
        e.preventDefault();
        $('.modal-wrapper').removeClass('hide');
    });

    // hide delete
    $('body').on('click', '.modal-close', function(e) {
        $(this).closest('.modal-wrapper').addClass('hide');
    });
}

// view params
console.log(pathArray);
let params = { 
    view: pathArray.shift(),
    match: pathArray.shift(),
    category: pathArray.shift(),
    offset: 0,
    limit: 30
}
console.log(params);

// the above works for all cases, except for the single view
if (!isNaN(+params['view'])) { // see if it can be turned into a number
    params['match'] = params['view'];
    params['view'] = 'single';
}

switch (params['view']) {
    case 'search':
        // swap around match and category, because they are wrong in case
        // of searching for author or title
        if (params['category'] !== undefined) {
            [params['match'], params['category']] = [params['category'], params['match']];
        }
        console.log(params);
        bookSearch(params);
        break;
    case 'latest':
        booksAndYears(params);
        break;
    case 'title':
    case 'series':
    case 'author':
        booksAndHeader(params);
        break;
    case 'single':
        singleBook(params);
        break;
    default: // all & genre
        booksAndLetters(params);
}

// show more
$('#btn-load-more').click(() => {
    params['offset'] += params['limit'];

    switch (params['view']) {
        case 'all':
        case 'genre':
            booksAndLetters(params);
            break;
        case 'latest':
            booksAndYears(params);
            break;
        case 'series':
        case 'author':
            booksAndHeader(params);
            break;
    }
});

