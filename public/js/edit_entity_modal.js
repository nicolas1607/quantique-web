const typeEditModal = [];
for (let i = 1; i <= document.currentScript.getAttribute('nb'); i++) {
    typeEditModal.push(document.currentScript.getAttribute('param' + i));
}

typeEditModal.forEach(function (type) {
    const edits = document.querySelectorAll('.edit-' + type);
    edits.forEach(function (edit) {
        let id;
        let modal;
        if (type == 'website') id = edit.id.substr(12);
        else if (type == 'user') id = edit.id.substr(9);
        else if (type == 'company') id = edit.id.substr(12);
        else if (type == 'password') id = edit.id.substr(13);
        else if (type == 'invoice') id = edit.id.substr(12);
        if (type == 'password') modal = document.querySelector('#modal-edit-' + type);
        else if (type == 'invoice') modal = document.querySelector('.modal-edit-invoice');
        else modal = document.querySelector('#modal-edit-' + type + id);
        edit.addEventListener('click', () => {
            if (type == 'invoice') {
                const type = document.querySelector('.invoice' + id + '-type');
                const date = document.querySelector('.invoice' + id + '-date').innerHTML;
                const typeModal = modal.querySelectorAll('select option');
                const dateModal = modal.querySelector("input[type='date']");
                for (let i = 0; i < typeModal.length; i++) {
                    if (typeModal[i].value == type.innerHTML) {
                        typeModal[i].setAttribute('selected', 'selected');
                    }
                }
                const form = document.querySelector('.modal-edit-invoice form');
                form.setAttribute('action', window.location.origin + '/admin/invoice/edit/' + id);
                dateModal.value = date.split('/')[2] + '-' + date.split('/')[1] + '-' + date.split('/')[0];
            }
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