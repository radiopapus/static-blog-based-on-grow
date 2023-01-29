elasticlunr(function () {
    this.use(lunr.multiLanguage('en', 'ru'));
    this.addField('title')
    this.addField('content')
});

window.idx = elasticlunr.Index.load(window.index) // window.index in the index.json

function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

function renderSearchList(ids) {
    var html = ids.map((docId) => {
        let doc = window.index.documentStore.docs[docId];

        if (docId.includes("/ru/")) {
            return `<div style="clear: both"><a href=${docId.replace("/ru", "")}>${doc.title}</a></div>`
        }
        return `<div style="clear: both"><a href=${docId}>${doc.title}</a></div>`
    });

    document.getElementById('searchResults').innerHTML = html.join('')
}

let onInput = debounce(function () {
    let query = this.value

    if (query.length < 2) return

    // do search
    let ids = window.idx.search(query)
        .map((result) => result.ref)

    renderSearchList(ids)
})

document.getElementById('searchInput')
    .addEventListener('input', onInput, false);