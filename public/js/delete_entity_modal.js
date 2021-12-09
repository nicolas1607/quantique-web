const typeDelete = [];
for (let i = 1; i <= document.currentScript.getAttribute('nb'); i++) {
    typeDelete.push(document.currentScript.getAttribute('param' + i));
}

typeDelete.forEach(function (type) {
    let deletes = document.querySelectorAll('.delete-' + type);
    for (let i = 0; i < deletes.length; i++) {
        let id;
        let del = deletes[i];
        if (type == 'invoice') id = del.id.substr(14);
        else if (type == 'user') id = del.id.substr(11);
        else if (type == 'website') id = del.id.substr(14);
        else if (type == 'contract') id = del.id.substr(15);
        else if (type == 'company') id = del.id.substr(14);
        let modal;
        if (type == 'website') modal = document.querySelector('#modal-delete-' + type + id);
        else modal = document.querySelector('#modal-' + type + id);
        del.addEventListener('click', () => {
            modal.style.visibility = 'visible';
            navbar.style.opacity = 0.4;
            cont.style.opacity = 0.4;
            buttons.forEach(function (btn) {
                btn.style.pointerEvents = 'none';
            });
        });
    }
});