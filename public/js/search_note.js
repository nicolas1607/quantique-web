// function search
const notes = document.getElementsByClassName('note');
const input = document.querySelector('#search-input');
input.value = window.location.search.substr(8);
input.addEventListener('keyup', () => {
    for (let i = 0; i < notes.length; i++) {
        const note = notes[i];
        const noteName = note.className.substr(17);
        if (input.value != "") {
            if (noteName.toUpperCase().includes(input.value.toUpperCase())) {
                note.style.display = null;
            } else {
                note.style.display = 'none';
            }
        } else {
            note.style.display = null;
        }
    }
});

// function delete
const cross = document.querySelector('#search-delete');
cross.addEventListener('click', () => {
    for (let i = 0; i < notes.length; i++) {
        input.value = "";
        notes[i].style.display = null;
    }
});