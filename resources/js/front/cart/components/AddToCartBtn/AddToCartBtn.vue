<template>

<div class="cart">
    <div class=" d-flex gap-3 pb-2 pb-lg-2 mb-0">
        <div class="count-input flex-shrink-0">
            <button type="button" class="btn btn-icon btn-lg" data-decrement aria-label="Decrement quantity">
                <i class="ci-minus"></i>
            </button>
            <input type="number" inputmode="numeric" pattern="[0-9]*" v-model="quantity" min="1" :max="available" class="form-control form-control-lg"  readonly>
            <button type="button" class="btn btn-icon btn-lg" data-increment aria-label="Increment quantity">
                <i class="ci-plus"></i>
            </button>
        </div>
        <button @click="add()" :disabled="disabled" class="btn btn-lg btn-dark w-100">Dodaj u košaricu</button>


    </div>
    <p style="width: 100%;" class="fs-md fw-light text-danger" v-if="has_in_cart">Imate {{ has_in_cart }} artikala u košarici.</p>
    </div>



</template>

<script>
export default {
    props: {
        id: String,
        available: String
    },

    data() {
        return {
            quantity: 1,
            has_in_cart: 0,
            disabled: false
        }
    },

    mounted() {
        let cart = this.$store.state.storage.getCart();
        if(cart) {
            for (const key in cart.items) {
                if (this.id == cart.items[key].id) {
                    this.has_in_cart = cart.items[key].quantity;
                }
            }
        }

        if (this.available == undefined) {
            this.available = 0;
        }

        this.checkAvailability();
    },

    methods: {
        add() {
            this.checkAvailability(true);

            if (this.has_in_cart) {
                this.updateCart();
            } else {
                this.add();
            }
        },
        /**
         *
         */
        addToCart() {
            let item = {
                id: this.id,
                quantity: this.quantity
            }

            this.$store.dispatch('addToCart', item);
        },

        /**
         *
         */
        updateCart() {
            let item = {
                id: this.id,
                quantity: this.quantity
            }

            this.$store.dispatch('updateCart', item);
        },

        /**
         *
         * @param add
         */
        checkAvailability(add = false) {
            if (add) {
                this.has_in_cart = parseFloat(this.has_in_cart) + parseFloat(this.quantity);
            }

            if (this.available <= this.has_in_cart) {
                this.disabled = true;
                this.has_in_cart = this.available;
            }
        }
    }
};
</script>
