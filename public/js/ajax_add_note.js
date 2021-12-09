function validateForm(id) {
    const form = document.querySelector('#form-add-note' + id)
    const user = form.querySelector('.user').id;
    const contract = form.id.substr(13);
    const msg = form.querySelector('#msg').value;
    $.ajax({
        type: 'POST',
        url: '/add_note.php',
        data: {
            user: user,
            contract: contract,
            msg: msg,
        }
    });
    // création du DOM
    const notepad = document.querySelector('#notepad' + contract)
        .querySelector('.notepad-notes');

    const notepadItem = document.createElement('div');
    {% set noteId = noteId + 1 %}
    notepadItem.id = "notepad-item" + {{ noteId }
};
notepadItem.className = "notepad-item";

const noteDate = document.createElement('p');
noteDate.className = "note-date";
var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0');
var yyyy = today.getFullYear();
today = dd + '/' + mm + '/' + yyyy;
noteDate.innerHTML = today;

const noteMsg = document.createElement('p');
noteMsg.className = "note-msg";
noteMsg.innerHTML = document.querySelector('#notepad' + contract)
    .querySelector('#msg').value
    .replaceAll('\n', '<br/>');

const navLink = document.createElement('a');
navLink.className = "nav-link";

navLink.href = "{{path('delete_note', {'note': noteId+1})}}";
navLink.href = decodeURI(navLink.href);
navLink.title = "Supprimer la note";

const navFont = document.createElement('i');
navFont.className = "fas fa-times";

notepadItem.appendChild(noteDate);
notepadItem.appendChild(noteMsg);
notepadItem.appendChild(navLink);
navLink.appendChild(navFont);

// récupère l'id max du #notepad+id
let noteId = 0;
const items = document.querySelector('#notepad' + contract).querySelectorAll('.notepad-item');
items.forEach(function (item) {
    let newId = item.id.substr(12);
    if (newId > noteId) noteId = newId;
})
if (document.querySelector('#notepad-item' + noteId)) {
    notepad.insertBefore(notepadItem, document.querySelector('#notepad-item' + noteId));
} else {
    document.querySelector('#notepad' + contract).querySelector('.notepad-notes')
        .appendChild(notepadItem);
}

// on reset le textarea
document.querySelector('#msg').value = "";
}