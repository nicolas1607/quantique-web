const typesAddContract = ['vitrine', 'commerce', 'google', 'facebook']
typesAddContract.forEach(function (type) {
    for (let i = 0; i < document.querySelectorAll('.edit-' + type + '-btn').length; i++) {
        const btn = document.querySelectorAll('.edit-' + type + '-btn')[i];
        btn.addEventListener('click', () => {
            const form = document.querySelectorAll('.edit-' + type + '-form')[i];
            const check = document.querySelectorAll('.edit-' + type + '-check')[i];
            const price = document.querySelectorAll('.edit-' + type + '-price')[i];
            if (form.style.display == 'none') {
                price.setAttribute('required', 'required');
                form.style.display = 'grid';
                check.checked = true;
                btn.setAttribute('style', 'background-color : #333');
                btn.innerHTML = '- ' + type;

            } else {
                price.removeAttribute('required');
                form.style.display = 'none';
                check.checked = false;
                btn.setAttribute('style', 'background-color : #F3AA10');
                btn.innerHTML = '+ ' + type;
            }
        });
    }
});