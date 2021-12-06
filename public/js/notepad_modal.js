const noteLinks = [...Array.from(document.getElementsByClassName('contract-info')),
...Array.from(document.getElementsByClassName('notepad-link'))];

if (document.querySelector('.notepad')) {
    for (let i = 0; i < noteLinks.length; i++) {
        let id;
        const noteLink = noteLinks[i];
        if (noteLink.className.includes('contract-info')) {
            id = noteLink.id.substr(8);
        } else {
            id = noteLink.id.substr(12);
        }
        const notepad = document.querySelector('#notepad' + id);
        const close = document.querySelector('#notepad-close' + id);
        // afficher la modal
        noteLink.addEventListener('click', () => {
            if (notepad.style.visibility = 'hidden') {
                notepad.style.visibility = 'visible';
                navbar.style.opacity = 0.4;
                cont.style.opacity = 0.4;
                buttons.forEach(function (btn) {
                    btn.style.pointerEvents = 'none';
                });
            }
        });
        // fermer la modal 'X'
        if (close) {
            close.addEventListener('click', () => {
                if (notepad.style.visibility = 'visible') {
                    notepad.style.visibility = 'hidden';
                    navbar.style.opacity = 1;
                    cont.style.opacity = 1;
                    buttons.forEach(function (btn) {
                        btn.style.pointerEvents = 'auto';
                    });
                }
            });
        }

    };
}

// fermer la modal 'Escape'
const notepads = document.getElementsByClassName('notepad');
window.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        for (let i = 0; i < notepads.length; i++) {
            if (notepads[i].style.visibility = 'visible') {
                notepads[i].style.visibility = 'hidden';
                navbar.style.opacity = 1;
                cont.style.opacity = 1;
                buttons.forEach(function (btn) {
                    btn.style.pointerEvents = 'auto';
                });
            }
        }
    }
});