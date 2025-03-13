const BASE_PATH = window.location.origin;
const API_PATH = BASE_PATH + '/api/v2/';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = API_PATH;

function addToCart(product_id, quantity) {
    let item = {
        id: product_id,
        quantity: quantity
    };

    axios.post(API_PATH, { data: data.meta.image_id })
    .then((response) => {
        successToast.fire({
            text: 'Fotografija je uspješno izbrisana',
        })

        let elem = document.getElementById('image_id_' + data.meta.image_id);

        elem.parentNode.removeChild(elem);
    })
    .catch((error) => {
        errorToast.fire({
            text: 'Greška u brisanju fotografije..! Molimo pokušajte ponovo.',
        })
    })
}