// function search
const users = document.getElementsByClassName('user');
const input = document.querySelector('#search-input');
input.value = window.location.search.substr(8);
input.addEventListener('keyup', () => {
    for (let i = 0; i < users.length; i++) {
        const user = users[i];
        const userName = user.className.substr(17);
        if (input.value != "") {
            if (userName.toUpperCase().includes(input.value.toUpperCase())) {
                user.style.display = null;
            } else {
                user.style.display = 'none';
            }
        } else {
            user.style.display = null;
        }
    }
});

// function delete search
const cross = document.querySelector('#search-delete');
cross.addEventListener('click', () => {
    for (let i = 0; i < users.length; i++) {
        input.value = "";
        users[i].style.display = null;
    }
});