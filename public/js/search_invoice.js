// function search
const invoices = document.getElementsByClassName('invoice');
const startDate = document.querySelector('#start-date');
const endDate = document.querySelector('#start-end');
startDate.addEventListener('change', () => {
    for (let i = 0; i < invoices.length; i++) {
        const start = startDate.value.split('-');
        const invoiceStartDate = new Date(start[0], start[1], start[2]);
        const invoice = invoices[i];
        const invoiceName = invoice.className.substr(20).split('-');
        const invoiceDate = new Date(invoiceName[0], invoiceName[1], invoiceName[2]);
        if (startDate.value != "") {
            if (invoiceDate >= invoiceStartDate) {
                invoice.style.display = null;
            } else {
                invoice.style.display = 'none';
            }
        } else {
            invoice.style.display = null;
        }
    }
});

endDate.addEventListener('change', () => {
    for (let i = 0; i < invoices.length; i++) {
        const start = endDate.value.split('-');
        const invoiceEndDate = new Date(start[0], start[1], start[2]);
        const invoice = invoices[i];
        const invoiceName = invoice.className.substr(20).split('-');
        const invoiceDate = new Date(invoiceName[0], invoiceName[1], invoiceName[2]);
        if (endDate.value != "") {
            if (invoiceDate <= invoiceEndDate) {
                invoice.style.display = null;
            } else {
                invoice.style.display = 'none';
            }
        } else {
            invoice.style.display = null;
        }
    }
});

// function delete search
// const cross = document.querySelector('#search-delete');
// cross.addEventListener('click', () => {
//     for (let i = 0; i < companies.length; i++) {
//         input.value = "";
//         companies[i].style.display = null;
//     }
// });