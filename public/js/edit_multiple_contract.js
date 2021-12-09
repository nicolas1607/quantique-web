const typesEditContract = ['vitrine', 'commerce', 'google', 'facebook']
typesEditContract.forEach(function (type) {
    const btn = document.querySelectorAll('.edit-' + type + '-btn');
    const form = document.querySelectorAll('.edit-' + type + '-form');
    const check = document.querySelectorAll('.edit-' + type + '-check');
    const price = document.querySelectorAll('.edit-' + type + '-price');
    for (let i = 0; i < btn.length; i++) {
        btn[i].addEventListener('click', () => {
            if (form[i].style.display == 'none') {
                price[i].setAttribute('required', 'required');
                form[i].style.display = 'grid';
                check[i].checked = true;
                btn[i].setAttribute('style', 'background-color : #333');
                btn[i].innerHTML = '- ' + type;
            } else if (form[i].style.display == 'grid') {
                price[i].removeAttribute('required');
                form[i].style.display = 'none';
                check[i].checked = false;
                btn[i].setAttribute('style', 'background-color : #F3AA10');
                btn[i].innerHTML = '+ ' + type;
            }
        });
        if (price[i].value != null && price[i].value != '') {
            btn[i].click();
        }
    }
});