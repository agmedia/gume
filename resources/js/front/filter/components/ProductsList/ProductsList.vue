<template>

    <div v-if="products.total">
        <!-- Filters -->
        <div class="bg-body-tertiary p-3 rounded-3 mb-3" >
            <div class="row align-items-center pt-1">
                <div class="col-12 d-md-flex d-block gap-2 ">
                    <div class="d-block w-100 mb-2 mb-md-0 me-1">
                        <select class="form-select rounded-pill" data-select='{
                  "classNames": {
                    "containerInner": ["form-select", "filter-select", "rounded-pill"]
                  }
                }' aria-label="Kategorija">
                            <option value="">Kategorija</option>
                            <option value="popular" selected>Ljetne gume</option>
                            <option value="match">Zimske gume</option>
                            <option value="new">Cjelogodišnje gume</option>
                        </select>
                    </div>
                    <div class="d-block w-100 mb-2 mb-md-0  me-1">
                        <select class="form-select rounded-pill" data-select='{
                  "classNames": {
                    "containerInner": ["form-select", "filter-select", "rounded-pill"]
                  },

                  "searchEnabled": true,
                   "searchPlaceholderValue": ["Pretraži"]
                }' aria-label="Širina">
                            <option value="">Širina</option>
                            <option value="145" >145</option>
                            <option value="155">155</option>
                            <option value="165">165</option>
                            <option value="175" selected>175</option>
                            <option value="185">185</option>
                            <option value="195">195</option>
                            <option value="205">205</option>
                            <option value="215">215</option>
                            <option value="225">225</option>
                        </select>
                    </div>
                    <div class="d-block w-100 mb-2 mb-md-0  me-1">
                        <select class="form-select rounded-pill" data-select='{
                  "classNames": {
                    "containerInner": ["form-select", "filter-select", "rounded-pill"]
                  },

                  "searchEnabled": true,
                   "searchPlaceholderValue": ["Pretraži"]
                }' aria-label="Visina">
                            <option value="">Visina</option>
                            <option value="35" >35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                            <option value="60">60</option>
                            <option value="65">65</option>
                            <option value="70">70</option>
                            <option value="80">80</option>
                        </select>
                    </div>
                    <div class="d-block w-100 mb-2 mb-md-0  me-1">
                        <select class="form-select  rounded-pill" data-select='{
                  "classNames": {
                    "containerInner": ["form-select", "filter-select", "rounded-pill"]
                  },

                  "searchEnabled": true,
                   "searchPlaceholderValue": ["Pretraži"]


                }' aria-label="Promjer" data-placeholder="Promjer" >

                            <option value="10" >R 10</option>
                            <option value="11">R 11</option>
                            <option value="12">R 12</option>
                            <option value="13">R 13</option>
                            <option value="14">R 14</option>
                            <option value="15">R 15</option>
                            <option value="16">R 16</option>
                            <option value="17">R 17</option>
                            <option value="18">R 18</option>
                            <option value="19">R 19</option>
                            <option value="20">R 20</option>
                        </select>
                    </div>

                    <!-- All filters offcanvas toggle -->
                    <nav class="nav">
                        <a class="nav-link animate-underline px-2" href="#shopFilters" data-bs-toggle="offcanvas" aria-controls="shopFilters">
                            <i class="ci-filter me-1"></i>
                            <span class="animate-target text-nowrap">Svi filteri</span>
                        </a>
                    </nav>
                </div>


            </div>
        </div>


        <!-- Selected filters -->
        <div class="d-flex flex-wrap align-items-center gap-2 text-nowrap  mb-5 pb-4 border-bottom ">
            <button type="button" class="btn btn-sm btn-secondary rounded-pill me-1">
                <i class="ci-close fs-sm me-1 ms-n1"></i>
                Ljetne gume
            </button>
            <button type="button" class="btn btn-sm btn-secondary rounded-pill me-1">
                <i class="ci-close fs-sm me-1 ms-n1"></i>
                Širina 175
            </button>

            <div class="nav ps-1">
                <a class="nav-link fs-xs text-decoration-underline px-0" href="#!">Očisti filtere</a>
            </div>

        </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 0 gy-5" id="productGrid" >
                <!-- Toolbar-->
                    <div class="col" v-for="product in products.data">
                        <div class="animate-underline">
                            <a class=" ratio ratio-1x1 d-block mb-3" :href="origin + product.url">
                                <img loading="lazy" :src="product.image.replace('.webp', '-thumb.webp')" width="300" height="300" :alt="product.name"  class="rounded-4" >
                            </a>
                            <div class="w-100 min-w-0 px-0 pb-2  pb-sm-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="d-flex gap-1 fs-xs">
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star text-body-tertiary opacity-75"></i>
                                    </div>
                                    <span class="text-body-tertiary fs-xs">(2)</span>
                                </div>
                                <h3 class="pb-1 mb-2">
                                    <a class="d-block fs-sm fw-medium " :href="origin + product.url">
                                        <span class="animate-target">{{ product.name }}</span>
                                    </a>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="h5 lh-1 mb-0" v-if="product.special">{{ product.main_special_text }} <del class="text-body-tertiary fs-sm fw-normal">{{ product.main_price_text }}</del></div>
                                    <div class="h5 lh-1 mb-0" v-if="!product.special"> {{ product.main_price_text }}x  </div>
                                    <button type="button" :disabled="product.disabled" v-on:click="add(product.id, product.quantity)" class="product-card-button btn btn-icon btn-primary animate-slide-end ms-2" aria-label="Add to Cart">
                                        <i class="ci-shopping-cart fs-base animate-target"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        <pagination :data="products" align="center" :show-disabled="true" :limit="4" @pagination-change-page="getProductsPage"></pagination>


        <!-- Pagination -->
        <div class="text-center pt-5 mt-md-2 mt-lg-3 mt-xl-4 mb-xxl-3 mx-auto" style="max-width: 306px">
            <p class="fs-sm">Prikazujemo {{ products.from ? Number(products.from).toLocaleString('hr-HR') : 0 }} do {{ products.to ? Number(products.to).toLocaleString('hr-HR') : 0 }} od {{ products.total ? Number(products.total).toLocaleString('hr-HR') : 0 }} artikala</p>
            <div class="progress mb-3" role="progressbar" aria-label="Items shown" aria-valuenow="20" aria-valuemin="0" aria-valuemax="225" style="height: 4px">
                <div class="progress-bar bg-dark rounded-pill d-none-dark" style="width: 25%"></div>
                <div class="progress-bar bg-light rounded-pill d-none d-block-dark" style="width: 25%"></div>
            </div>

        </div>
    </div>
    </div>

    <div v-else>
            <div class="row" v-if="!products_loaded">
                <div class="col-md-12 d-flex justify-content-center mt-4">
                    <div class="spinner-border text-primary opacity-75" role="status" style="width: 9rem; height: 9rem;"></div>
                </div>
            </div>

            <div class="col-md-12 px-2 mb-4 mt-5" v-if="products_loaded && search_zero_result">
                <h2>Nema rezultata pretrage</h2>
                <p> Vaša pretraga za  <mark>{{ search_query }}</mark> pronašla je 0 rezultata.</p>
                <h4 class="h5">Savjeti i smjernica</h4>
                <ul class="list-style">
                    <li>Dvaput provjerite pravopis.</li>
                    <li>Ograničite pretragu na samo jedan ili dva pojma.</li>
                    <li>Budite manje precizni u terminologiji. Koristeći više općenitih termina prije ćete doći do sličnih i povezanih proizvoda.</li>
                </ul>
                <hr class="d-sm-none">
            </div>
            <div class="col-md-12 px-2 mb-4" v-if="products_loaded && navigation_zero_result">
                <h2>Trenutno nema proizvoda</h2>
                <p> Pogledajte u nekoj drugoj kategoriji ili probajte sa tražilicom :-)</p>
                <hr class="d-sm-none">
            </div>

    </div>




</template>

<script>
    export default {
        name: 'ProductsList',
        props: {
            ids: String,
            group: String,
            cat: String,
            subcat: String,
            author: String,
            publisher: String,
        },
        //
        data() {
            return {
                products: {},
                autor: '',
                nakladnik: '',
                start: '',
                end: '',
                sorting: '',
                search_query: '',
                page: 1,
                origin: location.origin + '/',
                hr_total: 'rezultata',
                products_loaded: false,
                search_zero_result: false,
                navigation_zero_result: false,
            }
        },
        //
        watch: {
            sorting(value) {
                this.setQueryParam('sort', value);
            },
            $route(params) {
                this.checkQuery(params);
            }
        },
        //
        mounted() {
            this.checkQuery(this.$route);

            /*console.log('twindow.AGSettings')
            console.log(window.AGSettings)*/
        },

        methods: {
            /**
             *
             */
            getProducts() {
                this.search_zero_result = false;
                this.navigation_zero_result = false;
                this.products_loaded = false;
                let params = this.setParams();

                axios.post('filter/getProducts', { params }).then(response => {
                    this.products_loaded = true;
                    this.products = response.data;
                    this.checkHrTotal();
                    this.checkSpecials();
                    this.checkAvailables();

                    if (params.pojam != '' && !this.products.total) {
                        this.search_zero_result = true;
                    }

                    if (params.pojam == '' && !this.products.total) {
                        this.navigation_zero_result = true;
                    }
                });
            },

            /**
             *
             * @param page
             */
            getProductsPage(page = 1) {
                this.products_loaded = false;
                this.page = page;
                this.setQueryParam('page', page);

                let params = this.setParams();
                window.scrollTo({top: 0, behavior: 'smooth'});

                axios.post('filter/getProducts?page=' + page, { params }).then(response => {
                    this.products_loaded = true;
                    this.products = response.data;
                    this.checkHrTotal();
                    this.checkSpecials();
                    this.checkAvailables();
                });
            },

            /**
             *
             * @param type
             * @param value
             */
            setQueryParam(type, value) {
                this.closeFilter();
                this.$router.push({query: this.resolveQuery()}).catch(()=>{});

                if (value == '' || value == 1) {
                    this.$router.push({query: this.resolveQuery()}).catch(()=>{});
                }
            },

            /**
             *
             * @return {{}}
             */
            resolveQuery() {
                let params = {
                    start: this.start,
                    end: this.end,
                    autor: this.autor,
                    nakladnik: this.nakladnik,
                    sort: this.sorting,
                    pojam: this.search_query,
                    page: this.page
                };

                return Object.entries(params).reduce((acc, [key, val]) => {
                    if (!val) return acc
                    return { ...acc, [key]: val }
                }, {});
            },

            /**
             *
             * @param params
             */
            checkQuery(params) {
                this.start = params.query.start ? params.query.start : '';
                this.end = params.query.end ? params.query.end : '';
                this.autor = params.query.autor ? params.query.autor : '';
                this.nakladnik = params.query.nakladnik ? params.query.nakladnik : '';
                this.page = params.query.page ? params.query.page : '';
                this.sorting = params.query.sort ? params.query.sort : '';
                this.search_query = params.query.pojam ? params.query.pojam : '';

                if (this.page != '') {
                    this.getProductsPage(this.page);
                } else {
                    this.getProducts();
                }
            },

            /**
             *
             * @return {{cat: String, start: string, pojam: string, subcat: String, end: string, sort: string, nakladnik: string, autor: string, group: String}}
             */
            setParams() {
                let params = {
                    ids: this.ids,
                    group: this.group,
                    cat: this.cat,
                    subcat: this.subcat,
                    autor: this.autor,
                    nakladnik: this.nakladnik,
                    start: this.start,
                    end: this.end,
                    sort: this.sorting,
                    pojam: this.search_query
                };

                if (this.author != '') {
                    params.autor = this.author;
                }
                if (this.publisher != '') {
                    params.nakladnik = this.publisher;
                }

                return params;
            },

            /**
             *
             */
            checkSpecials() {
                let now = new Date();

                for (let i = 0; i < this.products.data.length; i++) {
                    if (Number(this.products.data[i].main_price) <= Number(this.products.data[i].main_special)) {
                        this.products.data[i].special = false;
                    }
                }
            },

            /**
             *
             */
            checkAvailables() {
                let cart = this.$store.state.storage.getCart();

                if (cart) {

                    for (let i = 0; i < this.products.data.length; i++) {
                        this.products.data[i].disabled = false;

                        for (const key in cart.items) {
                            if (this.products.data[i].id == cart.items[key].id) {
                                if (this.products.data[i].quantity <= cart.items[key].quantity) {
                                    this.products.data[i].disabled = true;
                                }
                            }
                        }
                    }
                }
            },

            /**
             *
             */
            checkHrTotal() {
                this.hr_total = 'rezultata';

                if ((this.products.total).toString().slice(-1) == '1') {
                    this.hr_total = 'rezultat';
                }
            },

            /**
             *
             * @param id
             */
            add(id, product_quantity) {
                let cart = this.$store.state.storage.getCart();
                if (cart) {
                    for (const key in cart.items) {
                        if (id == cart.items[key].id) {
                            if (product_quantity <= cart.items[key].quantity) {
                                return window.ToastWarning.fire('Nažalost nema dovoljnih količina artikla..!');
                            }
                        }
                    }
                }

                this.$store.dispatch('addToCart', {
                    id: id,
                    quantity: 1
                });
            },

            /**
             *
             */
            closeFilter() {
                $('#shop-sidebar').removeClass('collapse show');
            }
        }
    };
</script>

<style>
</style>
