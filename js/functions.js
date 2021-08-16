async function postData(url = '', data = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
    return response.json();
}

function Counter(array) {
    array.forEach(val => this[val] = (this[val] || 0) + 1);
}

function clean4URL(str) {
    return str
        .replace(/\'|&|\.|\(|\)/g, '') // replace ', &, ., (, and )
        .replace(/\s+/g, ' ').trim() // reduce multiple spaces to one
        .replaceAll(/\s+/g, '_') // replace spaces by underscore
        .toLowerCase()
}

const createGenreElement = (v) => {

    // capitalize each first letter of the genre
    v = v.split(' ').map((x) => x[0].toUpperCase() + x.slice(1, )).join(' ');

    const $genreDiv = $('<div/>', { 'class': 'genre-tag', text: v }).append(
        $('<span/>', { 'class': 'delete-tag fas fa-times' } )
    );

    $('body').on('click', '.delete-tag', function(e) {
        $(this).closest('.genre-tag').remove();
    });

    return $genreDiv;
}