window.addEventListener('load', (e) => {

    // load latest ten books
    postData('controllers/view.php', { view: 'latest', offset: 0, limit: 10 })
        .then(data => {
            for (let book of data['books'].reverse()) {
                $('#last-ten').append(
                    $('<a/>', { 'href': `${book['id']}/${clean4URL(book['title'])}/`, 'class': 'recent-book' }).append(
                        $('<img/>', { 'src': `books/${book['cover']}`, 'alt': `${book['cover']} cover image` })
                    )
                );
            }
        })
        .catch((error) => {
            console.error(error);
        })

    // load genre word cloud
    postData('controllers/view.php', { view: 'cloud' })
        .then(data => {

            // count the occurences of each genre
            const allGenres = data['books'].map((b) => b['genre']).join(';').split(';');
            const genreCounts = new Counter(allGenres);

            // get and shuffle keys
            const keys = Object.keys(genreCounts).map(k => k);
            const keysShuffle = keys.sort((a, b) => 0.5 - Math.random());

            // get the total
            const total = allGenres.length;
            // absolutely to complicated, but maybe good for future reference
            // const total = Object.keys(genreCounts).reduce((acc, key) => { return acc + genreCounts[key]; }, 0);

            for (let k of keysShuffle) {
                
                if (genreCounts[k] > 1) {

                    // calculate data-weight
                    let dataWeight = 9;
                    const weightPerc = genreCounts[k] / total * 100;
                    if (weightPerc < 2.5) { dataWeight = 1 }
                    else if (weightPerc >= 2.5 && weightPerc < 5) { dataWeight = 2 }
                    else if (weightPerc >= 5 && weightPerc < 7.5) { dataWeight = 3 }
                    else if (weightPerc >= 7.5 && weightPerc < 10) { dataWeight = 4 }
                    else if (weightPerc >= 10 && weightPerc < 12.5) { dataWeight = 5 }
                    else if (weightPerc >= 12.5 && weightPerc < 15) { dataWeight = 6 }
                    else if (weightPerc >= 15 && weightPerc < 17.5) { dataWeight = 7 }
                    else if (weightPerc >= 17.5 && weightPerc < 20) { dataWeight = 8 }

                    // get the url friendly(-ish) genre name
                    // const urlGenre = k.replace('&', '').replace(/\s+/g, ' ').trim().replace(' ', '_').toLowerCase();

                    $('.genre-cloud').append(
                        $('<li/>').append(
                            $('<a/>', { 'data-weight': dataWeight, 'href': `genre/${clean4URL(k)}/`, text: k })
                        )
                    );
                
                }

            }

        })
        .catch((error) => {
            console.error(error);
        });

});

// SEARCH
$('#search-submit, .fa-search').click(function(e) {
    e.preventDefault();
    
    // get the search form data
    const searchForm = document.getElementById('book-search');
    const formData = new FormData(searchForm);
    const { 'search-term': searchTerm, category } = Object.fromEntries(formData);
    
    // only do something if the search term length is not zero
    if (searchTerm.length) {
        
        // clean up the search term for the url and DB comparisons
        const cleanTerm = clean4URL(searchTerm);
        window.location.href = (category === 'general') ? `search/${cleanTerm}/` : `search/${category}/${cleanTerm}/`;

    }

});