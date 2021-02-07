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

function renderSearchList(refs) {

    let list = rawData.filter(d => refs.includes(d.id))

    var rrr = list.map((l) => {
            return `<div style="clear: both"><a href=${l.id}>${l.title}</a></div>`
        });
    

    document.getElementById('searchResults').innerHTML = rrr.join('')
}

let onInput = debounce(function () {
    if (this.value.length < 2) return

    let query = this.value

    let refs = idx.search(query).map((result) => result.ref)

    renderSearchList(refs)
})

document.getElementById('searchInput')
    .addEventListener('input', onInput, false);