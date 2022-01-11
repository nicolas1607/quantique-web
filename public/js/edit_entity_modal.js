const typeEditModal = [];
for (let i = 1; i <= document.currentScript.getAttribute('nb'); i++) {
    typeEditModal.push(document.currentScript.getAttribute('param' + i));
}

typeEditModal.forEach(function (type) {
    const edits = document.querySelectorAll('.edit-' + type);
    edits.forEach(function (edit) {
        let id;
        if (type == 'website') id = edit.id.substr(12);
        else if (type == 'user') id = edit.id.substr(9);
        else if (type == 'company') id = edit.id.substr(12);
        else if (type == 'password') id = edit.id.substr(13);
        let modal;
        if (type == 'password') modal = document.querySelector('#modal-edit-' + type);
        else modal = document.querySelector('#modal-edit-' + type + id);
        edit.addEventListener('click', () => {
            modal.style.visibility = 'visible';
            modal.className = modal.className + ' animate__animated animate__backInUp';
            navbar.style.opacity = 0.4;
            cont.style.opacity = 0.4;
            buttons.forEach(function (btn) {
                btn.style.pointerEvents = 'none';
            });
        });
    });
});