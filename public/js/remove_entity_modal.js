const typeRemove = [];
for (let i = 1; i <= document.currentScript.getAttribute('nb'); i++) {
    typeRemove.push(document.currentScript.getAttribute('param' + i));
}

typeRemove.forEach(function (type) {
    let deletes = document.querySelectorAll('.remove-' + type);
    for (let i = 0; i < deletes.length; i++) {
        let id;
        let del = deletes[i];
        if (type == 'user') id = del.id.substr(11);
        console.log(id);
        let modal = document.querySelector('#modal-remove-' + type + id);
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