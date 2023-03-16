<template>
    <section>
        <div class="sidecart bg-dark text-center">
            <div class="text-light h4 m-0 px-4 text-center mt-2">
                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-4">{{ $store.getters.localizedStrings.cart.title }} </div>
                    <div class="col-4 d-flex justify-content-end">
                    <a-button type="danger" @click="activeLink">X</a-button>
                    </div>
                </div>
                </div>
            <ul class="nav flex-column" v-for="(item) in carts" :key="item.id">
                <li class="nav-link d-flex flex-wrap flex-row">
                    <div class="col-12 text-light h5 text-center p-0">{{ item.name }}</div>
                    <div class="col-4 p-0">
                        <img
                        :src="item.options.image"
                            alt="" height="75px" width="100%">
                    </div>
                    <div class="col-3 bg-primary text-light justify-content-around d-flex flex-column">
                        <div class="product-quantity m-0 p-0"><span class="text-dark"><b>Qty:</b></span> <span
                                class="product-price-total">{{ item.qty }}</span></div>
                    </div>
                    <div class="sidecart-price pl-0 col-5 bg-primary text-right d-flex justify-content-end align-items-center flex-wrap text-light">
                        <a-button type="danger" class="h-25 text-center" @click="removeItem(item.rowId)">X</a-button>
                        <div class="product-price">{{ item.price }}</div>
                        <div class=""><span class="text-dark"><b>Total:</b></span> <span
                                class="product-price-total">{{ item.subtotal }}</span></div>
                    </div>
                </li>
            </ul>
            <div class="text-light h6 text-left mx-3">{{ $store.getters.localizedStrings.cart.total_price }}: <span class="text-success"
                    id="sidecart-total-products">{{ totalPrice | formatNumber}}</span></div>
            <div class="text-light h6 text-left mx-3">{{ $store.getters.localizedStrings.cart.tax }}: <span class="text-success" id="sidecart-frete">{{ tax | formatNumber}}</span></div>
            <div class="text-light h5 text-left mx-3">{{ $store.getters.localizedStrings.cart.total_tax }}: <span class="text-success"
                    id="sidecart-total">{{ total_tax | formatNumber}}</span></div>
            <div class="p-2">
                <button type="button" @click="goToCart" class="btn btn-success w-100">{{ $store.getters.localizedStrings.cart.payment }}</button>
                <button type="button" @click="removeAll()" class="btn btn-danger w-100">{{ $store.getters.localizedStrings.cart.delete_all }}</button>
            </div>
        </div>
    </section>
</template>
<script>
import httpRequest from '../axios'
export default {
    props: {
    isActive: {
      type: Boolean,
      default: true
    }
  },
    data() {
        return {
            carts: {},
            totalPrice: 1,
            tax: 1,
            total_tax: 1,
        }
    },
    methods: {
        getCart() {
            httpRequest.get('/api/carts')
                .then(response => {
                    console.log('Res', response)
                    this.carts = response.data.data;
                    this.totalPrice = response.data.total;
                    this.tax = response.data.tax;
                    this.total_tax = response.data.total_tax;
                })
        },
        removeItem(rowId) {
            httpRequest.get('/api/cart/remove/' + rowId)
            .then(response => {
                this.info = response
                Toast.fire({
                    icon: 'success',
                    title: response.data.message
                });
                this.getCart();
            }).catch(function (error) {
                Toast.fire({
                    icon: 'error',
                    title: error
                });
            });
        },
        removeAll() {
            httpRequest.delete('/api/cart/removeAll')
            .then(response => {
                this.info = response
                Toast.fire({
                    icon: 'success',
                    title: response.data.message
                });
                this.getCart();
            }).catch(function (error) {
                Toast.fire({
                    icon: 'error',
                    title: error
                });
            });
        },
        activeLink() {
            this.isActive = !this.isActive;
            this.$emit("updateActive", this.isActive);
        },
        goToCart() {
            this.$router.push({ name: "cart"});
        }
    },
    mounted() {
        console.log('Component mounted.')
        this.getCart();
    },
    created() {
    },
}
</script>
<style scoped>
.sidecart {
    width: 350px;
    min-width: 200px;
    height: 100vh;
    top: 0px;
    overflow: auto !important;
    /* right: -320px; */
    z-index: 9000;
    transition: 0.2s ease;
}

@media(max-width: 400px) {
    .sidecart {
        width: 100vw;
        right: -100vw;
    }
}

.open-cart {
    right: 0px !important;
}

.open-cart:before {
    content: '';
    position: fixed;
    top: 0px;
    width: 100vw;
    height: 100vh;
    left: 0px;
    right: 0px;
    bottom: 0px;
    pointer-events: none;
    background-color: black;
    opacity: 0.5;
    z-index: -1;
}

.product-quantity {
    position: relative;
}

.sidecart-price>div {
    margin: auto;
    width: 100%;
    white-space: nowrap;
}
</style>