// function search
const notes = document.getElementsByClassName('note');
const input = document.querySelector('#search-input');
input.value = window.location.search.substr(8);
input.addEventListener('keyup', () => {
    for (let i = 0; i < notes.length; i++) {
        const note = notes[i];
        if (input.value != '' && select.value != '') {
            const noteName = note.className.substr(17);
            const noteType = note.getAttribute('type');
            if (noteName.toUpperCase().includes(input.value.toUpperCase()) && noteType.toUpperCase().includes(select.value.toUpperCase())) {
                note.style.display = null;
            } else {
                note.style.display = 'none';
            }
        } else if (input.value != '' && select == '') {
            const noteName = note.className.substr(17);
            if (noteName.toUpperCase().includes(input.value.toUpperCase())) {
                note.style.display = null;
            } else {
                note.style.display = 'none';
            }
        } else if (select.value != '' && input.value == '') {
            const noteType = note.getAttribute('type');
            if (noteType.toUpperCase().includes(select.value.toUpperCase())) {
                note.style.display = null;
            } else {
                note.style.display = 'none';
            }
        } else {
            note.style.display = null;
        }
    }
});

const select = document.querySelector('#search-select');
select.addEventListener('change', () => {
    for (let i = 0; i < notes.length; i++) {
        const note = notes[i];
        if (input.value != '' && select.value != '') {
            const noteName = note.className.substr(17);
            const noteType = note.getAttribute('type');
            if (noteName.toUpperCase().includes(input.value.toUpperCase()) && noteType.toUpperCase().includes(select.value.toUpperCase())) {
                note.style.display = null;
            } else {
                note.style.display = 'none';
            }
        } else if (select.value != "") {
            const noteType = note.getAttribute('type');
            if (noteType.toUpperCase().includes(select.value.toUpperCase())) {
                note.style.display = null;
            } else {
                note.style.display = 'none';
            }
        } else {
            note.style.display = null;
        }
    }
});