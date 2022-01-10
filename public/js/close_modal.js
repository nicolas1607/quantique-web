// functions fermer une modale
const modalElements = [...Array.from(document.querySelectorAll('.mdl-delete')),
...Array.from(document.querySelectorAll('.mdl-add-user')),
...Array.from(document.querySelectorAll('.mdl-add-contract')),
...Array.from(document.querySelectorAll('.mdl-add-invoice')),
...Array.from(document.querySelectorAll('.mdl-add-website')),
...Array.from(document.querySelectorAll('.mdl-edit-website')),
...Array.from(document.querySelectorAll('.mdl-edit-user')),
...Array.from(document.querySelectorAll('.mdl-edit-company')),
...Array.from(document.querySelectorAll('.mdl-add-new-user')),
...Array.from(document.querySelectorAll('.mdl-edit-password')),
...Array.from(document.querySelectorAll('.mdl-remove')),
...Array.from(document.querySelectorAll('.mdl-all-company'))];

modalElements.forEach(function (modal) {
    const close = modal.querySelector('.mdl-close');
    const cancel = modal.querySelector('.btn-cancel');
    // fermer avec button 'X'
    close.addEventListener('click', () => {
        modal.style.visibility = 'hidden';
        navbar.style.opacity = 1;
        cont.style.opacity = 1;
        buttons.forEach(function (btn) {
            btn.style.pointerEvents = 'auto';
        });
    });
    // fermer avec button 'Annuler'
    cancel.addEventListener('click', () => {
        modal.style.visibility = 'hidden';
        navbar.style.opacity = 1;
        cont.style.opacity = 1;
        buttons.forEach(function (btn) {
            btn.style.pointerEvents = 'auto';
        });
    });
    // fermer avec button 'Escape'
    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            modal.style.visibility = 'hidden';
            navbar.style.opacity = 1;
            cont.style.opacity = 1;
            buttons.forEach(function (btn) {
                btn.style.pointerEvents = 'auto';
            });
        }
    });
});