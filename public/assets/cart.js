const BASE_PATH = window.location.origin;
const API_PATH = BASE_PATH + '/api/v2/';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = API_PATH;

function addToCart(product_id, quantity) {
    let item = {
        id: product_id,
        quantity: quantity
    };

    axios.post('cart/add', { item: item })
    .then((response) => {
        /*successToast.fire({
            text: 'Fotografija je uspješno izbrisana',
        });*/

        console.log(response.data);
    })
    .catch((error) => {
        /*errorToast.fire({
            text: 'Greška u brisanju fotografije..! Molimo pokušajte ponovo.',
        });*/

        console.log(error);
    });
}