<template>
    <button class="product-card-button btn btn-icon btn-primary animate-slide-end ms-2" :disabled="disabled" @click="add()" type="button"><i class="ci-shopping-cart fs-base animate-target"></i></button>
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
            has_in_cart: false,
            disabled: false
        }
    },

    mounted() {
        let cart = this.$store.state.storage.getCart();



        if(cart) {
            for (const key in cart.items) {
                if (this.id == cart.items[key].id) {
                    this.has_in_cart = true;
                    this.quantity = cart.items[key].quantity;
                }
            }
        }

        this.checkAvailability();
    },

    methods: {
        add() {
            this.checkAvailability();

            if (this.has_in_cart) {
                this.updateCart();
            } else {
                this.addToCart();
            }

            this.quantity += 1;
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

        checkAvailability() {
            if (this.available < this.quantity) {
                this.disabled = true;
                this.quantity = this.available;
            }
        }
    }
};
</script>
