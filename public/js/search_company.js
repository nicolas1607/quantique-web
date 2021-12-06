// function search
const input = document.querySelector('#search-input');
input.value = window.location.search.substr(8);
input.addEventListener('keyup', () => {
    for (let i = 0; i < companies.length; i++) {
        const company = companies[i];
        const companyName = company.className.substr(20);
        if (input.value != "") {
            if (companyName.toUpperCase().includes(input.value.toUpperCase())) {
                company.style.display = null;
            } else {
                company.style.display = 'none';
            }
        } else {
            company.style.display = null;
        }
    }
});

// function delete search
const cross = document.querySelector('#search-delete');
cross.addEventListener('click', () => {
    for (let i = 0; i < companies.length; i++) {
        input.value = "";
        companies[i].style.display = null;
    }
});