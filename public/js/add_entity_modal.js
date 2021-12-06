const typeAddModal = [];
for (let i = 1; i <= document.currentScript.getAttribute('nb'); i++) {
    typeAddModal.push(document.currentScript.getAttribute('param' + i));
}

typeAddModal.forEach(function (type) {
    const adds = [...Array.from(document.querySelectorAll('.add-' + type)),
    ...Array.from(document.querySelectorAll('.add-new' + type))];

    adds.forEach(function (add) {
        let id; let modal;
        if (type == 'user') id = add.id.substr(8);
        else if (type == 'contract') id = add.id.substr(12);
        else if (type == 'invoice') id = add.id.substr(11);
        else if (type == 'website') id = add.id.substr(11);
        if (type == 'invoice') modal = document.querySelector('#modal-invoice');
        else if (type == 'new-user') modal = document.querySelector('#modal-new-user');
        else modal = document.querySelector('#modal-' + type + id);
        add.addEventListener('click', () => {
            modal.style.visibility = 'visible';
            navbar.style.opacity = 0.4;
            cont.style.opacity = 0.4;
            buttons.forEach(function (btn) {
                btn.style.pointerEvents = 'none';
            });
            if (type == 'invoice') {
                const input = document.querySelector('#company');
                input.value = add.title.substr(20);
            }
        });
    });
});

// form add user with user exist
const userExist = document.querySelectorAll('#user-exist');
if (userExist) {
    for (let i = 0; i < userExist.length; i++) {
        userExist[i].addEventListener('change', () => {
            const firstname = document.querySelectorAll('#user-firstname');
            const lastname = document.querySelectorAll('#user-lastname');
            const email = document.querySelectorAll('#user-email');
            const value = userExist[i].value;
            if (userExist[i].value == "") {
                firstname[i].setAttribute('required', 'required');
                lastname[i].setAttribute('required', 'required');
                email[i].setAttribute('required', 'required');
            } else {
                userExist[i].value = value;
                firstname[i].removeAttribute('required');
                lastname[i].removeAttribute('required');
                email[i].removeAttribute('required');
            }
        });
    }
}
