<template>
    <section>
        <div class="container">
            <div class="row">
                <a-form :form="form" id="form-container" @submit="updateCart">
                    <div class="col-lg-11 col-xl-11 m-b-50 w-100">
                        <div class="m-l-25 m-r--38 m-lr-0-xl">
                            <div class="wrap-table-shopping-cart">
                                <table class="table-shopping-cart">
                                    <tbody>
                                        <tr class="table_head">
                                            <th class="column-1">{{ $store.getters.localizedStrings.cart_page.image }}</th>
                                            <th class="column-2">{{ $store.getters.localizedStrings.cart_page.name }}</th>
                                            <th class="column-3">{{ $store.getters.localizedStrings.cart_page.price }}</th>
                                            <th class="column-4">{{ $store.getters.localizedStrings.cart_page.qty }}</th>
                                            <th class="column-5">{{ $store.getters.localizedStrings.cart_page.total }}</th>
                                            <th class="column-6">&nbsp;</th>
                                        </tr>
                                        <tr class="table_row" v-for="(item, index) in carts" :key="item.id">
                                            <td class="column-1">
                                                <div class="how-itemcart1 mt-3">
                                                    <img :src="item.product.file" alt="IMG" class="image-contain">
                                                </div>
                                            </td>
                                            <td class="column-2">
                                                <p class="mt-4">{{ item.product.name }}</p>
                                                <a-form-item class="mt-3" no-style>
                                                    <a-input
                                                        v-decorator="['carts.' + index + '.id', { initialValue: item.id, }]"
                                                        type="hidden" />
                                                </a-form-item>
                                            </td>
                                            <td class="column-3">
                                               <div v-if="!item.product.price_currency" class="mt-4">
                                        <p>
                                            {{ item.product.price_sale < item.product.price ? item.product.price_sale : item.product.price |
                                                formatNumber }} {{ initialValue.user_currency }}</p>
                                    </div>
                                    <div v-if="item.product.price_currency" class="mt-4">
                                        <p>
                                            {{ item.product.price_sale_currency < item.product.price_currency ?
                                                item.product.price_sale_currency : item.product.price_currency | formatNumber }} {{
                                        initialValue.user_currency }} </p>
                                    </div>
                                            </td>
                                            <td class="column-4">
                                                <a-form-item class="mt-3" no-style>
                                                    <a-input-number
                                                        v-decorator="['carts.' + index + '.qty', { initialValue: parseInt(item.qty), }]"
                                                        :min="1" :max="100" />
                                                </a-form-item>
                                            </td>
                                            <td class="column-5">
                                                <p class="mt-4">{{ item.total | formatNumber }} {{
                                                    initialValue.user_currency }}</p>
                                            </td>
                                            <td class="p-r-15 px-4">
                                                <div class="mt-4">
                                                    <a @click="removeItem(item.id)" style="color:red">{{
                                                        $store.getters.localizedStrings.cart_page.delete }}</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex-sb-m bor15 p-t-18 p-b-15 p-lr-40">
                                <div class="flex-m m-tb-5">
                                    <input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5 w-50" type="text"
                                        placeholder="Coupon Code">
                                    <div
                                        class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
                                        {{ $store.getters.localizedStrings.cart_page.coupon }}
                                    </div>
                                    <button html-type="submit"
                                        class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10 w-25 ml-2 text-center">{{
                                            $store.getters.localizedStrings.cart_page.update }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </a-form>
                <a-form :form="form" id="form-container" @submit="handlePayment">
                    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                        <h4 class="mtext-109 cl2 p-b-30">
                            {{ $store.getters.localizedStrings.cart_page.cart_total }}
                        </h4>
                        <div class="flex-w flex-t p-t-27 p-b-33">
                            <div class="size-208">
                                <span class="mtext-101 cl2">
                                    {{ $store.getters.localizedStrings.cart_page.total }}:
                                </span>
                            </div>

                            <div class="size-209 p-t-1">
                                <span class="mtext-110 cl2">
                                    {{ totalPrice | formatNumber }} {{
                                        initialValue.user_currency }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-w flex-t bor12 p-t-15 p-b-30">

                            <div class="size-100 p-r-18 p-r-0-sm w-full-ssm">
                                <div class="p-t-15">
                                    <span class="stext-112 cl8">
                                        {{ $store.getters.localizedStrings.cart_page.customer }}
                                    </span>

                                    <div class="bor8 bg0 m-b-12 mt-3">
                                        <a-form-item>
                                            <a-input v-decorator="[
                                                'name',
                                                {
                                                    initialValue: initialValue.edit_name,
                                                    rules: [{ required: true, message: 'Please input your name!' }],
                                                },
                                            ]" disabled />
                                        </a-form-item>
                                    </div>

                                    <div class="bor8 bg0 m-b-12">
                                        <a-form-item>
                                            <a-input v-decorator="[
                                                'phone',
                                                {
                                                    initialValue: initialValue.edit_phone,
                                                    rules: [{ required: true, message: 'Please input your phone!' }],
                                                },
                                            ]" />
                                        </a-form-item>
                                    </div>

                                    <div class="bor8 bg0 m-b-12">
                                        <a-form-item>
                                            <a-input v-decorator="[
                                                'address',
                                                {
                                                    initialValue: initialValue.edit_address,
                                                    rules: [{ required: true, message: 'Please input your Address!' }],
                                                },
                                            ]" />
                                        </a-form-item>
                                    </div>

                                    <div class="bor8 bg0 m-b-12">
                                        <a-form-item>
                                            <a-input v-decorator="[
                                                'email',
                                                {
                                                    initialValue: initialValue.edit_email,
                                                    rules: [{ required: true, message: 'Please input your email!' }],
                                                },
                                            ]" disabled />
                                        </a-form-item>
                                    </div>

                                    <div class="bor8 bg0 m-b-12">
                                        <a-form-item>
                                            <a-textarea :rows="4" placeholder="Input Content" v-decorator="['content']"
                                                :maxlength="500" />
                                        </a-form-item>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <button @click="showPaymentModal($event)"
                            class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer bg-primary">
                            {{ $store.getters.localizedStrings.cart_page.payment }}
                        </button>
                        <a-modal v-model="paymentModalVisible" title="Choose Payment Method" class="d-flex">
                            <!-- Content for payment methods -->
                            <button class="button" @click="selectPaymentMethod('VNPay',$event)"><img class="button-image" src="../../../public/images/download-logo-vector-vnpay-mien-phi.jpg"/></button>
                            <button class="button mt-3" @click="selectPaymentMethod('Momo',$event)"><img class="button-image" src="../../../public/images/image (3).png"/></button>
                            <button class="button mt-3" @click="selectPaymentMethod('OnePay',$event)"><img class="button-image" src="../../../public/images/31-1367011326_500x0.jpeg"/></button>
                            <button class="button mt-3" @click="selectPaymentMethod('Paypal',$event)"><img class="button-image" src="../../../public/images/196566.png"/></button>
                        </a-modal>
                    </div>
                </a-form>
            </div>
        </div>
    </section>
</template>
<script>
import httpRequest from '../axios'
export default {
    data() {
        return {
            paymentModalVisible: false,
            selectedPaymentMethod: null,
            form: this.$form.createForm(this),
            carts: {},
            totalPrice: 1,
            tax: 1,
            total_tax: 1,
            fields: [{
                rowId: '',
                qty: 1,
            }],
            initialValue: {
                edit_name: '',
                edit_id: 1,
                edit_email: '',
                edit_phone: '',
                edit_address: '',
                role: 1,
                user_currency: ''
            },
        }
    },
    computed: {
        inputValues() {
            return this.carts.map((item) => item.value);
        }
    },
    methods: {
        getCart() {
            httpRequest.get('/api/carts')
                .then(response => {
                    this.carts = response.data.carts;
                    this.totalPrice = response.data.total_all;
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
        async updateCart(e) {
            e.preventDefault();
            await this.form
                .validateFields((err, values) => {
                    if (err) return;
                    httpRequest.post('/api/cart', values).then((response) => {
                        this.info = response
                        this.flagModalAdd = false
                        Toast.fire({
                            icon: 'success',
                            title: '' + this.$store.getters.localizedStrings.user_management.add_user.success
                        });
                    })
                    this.getCart();
                    this.form.resetFields();
                })
        },
        getUser() {
            httpRequest
                .get('/api/admin/users/currentUser')
                .then((response) => {
                    this.initialValue.edit_name = response.data.data.name;
                    this.initialValue.edit_phone = response.data.data.phone;
                    this.initialValue.edit_address = response.data.data.address;
                    this.initialValue.edit_id = response.data.data.id;
                    this.initialValue.edit_email = response.data.data.email;
                    this.initialValue.role = response.data.data.admin;
                    this.initialValue.user_currency = response.data.data.currency;
                })
        },
        showPaymentModal() {
            this.paymentModalVisible = true;
        },
        selectPaymentMethod(method, e) {
            this.selectedPaymentMethod = method;
            this.handlePayment(e, method);
        },
        handlePayment(e, method) {
            e.preventDefault();
            if (this.selectedPaymentMethod) {
            this.form
                .validateFields((err, values) => {
                    values.method = method;
                    if (err) return;
                    httpRequest.post('/api/cart/payment', values).then((response) => {
                        this.info = response
                        console.log(response)
                        if (response.data.link) {
                            window.open(response.data.link, '_blank')
                        }
                        this.flagModalAdd = false
                        Toast.fire({
                            icon: 'success',
                            title: '' + this.$store.getters.localizedStrings.user_management.add_user.success
                        });
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    })
                    this.form.resetFields();
                })
            }
        },
    },
    mounted() {
        this.getCart();
        this.getUser();
        const status = this.$route.query.status;
        if (status === 'success') {
            Toast.fire({
                icon: 'success',
                title: 'Payment Success'
            });
            setTimeout(() => {
                this.$router.push({ name: 'cart' })
            }, 2000);
        } else if (status === 'fail') {
            Toast.fire({
                icon: 'error',
                title: 'Payment Failed'
            });
            setTimeout(() => {
                this.$router.push({ name: 'cart' })
            }, 2000);
        }
    },
    created() {
    },
}
</script>

<style scoped>
.button {
    width: 100%;
    height: 50px;
    padding: 10px;
    border: 0.5px solid;
    background-color: transparent;
    cursor: pointer;
    transition: font-weight 0.3s;
}

.button:hover {
    font-weight: bold;
}

.button-image {
    width: 40%;
    height: 120%;
    object-fit: cover;
}
</style>