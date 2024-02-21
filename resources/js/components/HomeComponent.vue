<template>
    <!-- Slider -->
    <section>
        <section class="section-slide">
            <div class="wrap-slick1">
                <div class="slick1">
                    <div class="container slider">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <carousel v-if="sliders.length > 0">
                                            <div v-for="slide in sliders" :key="slide.id" class="image-contain">
                                                <h5 style="text:center">{{ slide.name }}</h5>
                                                <a :href="slide.url" target="_blank"><img :src="slide.file"
                                                        style="height:300px"></a>
                                            </div>
                                        </carousel>
                                        <carousel v-if="sliders.length === 0">
                                            <div class="image-contain">
                                                <h5 style="text:center">FaceBook</h5>
                                                <a href="https://www.facebook.com/" target="_blank"><img
                                                        src="../../../public/images/facebook.png" style="height:300px"></a>
                                            </div>
                                            <div class="image-contain">
                                                <h5 style="text:center">Instagram</h5>
                                                <a href="https://www.instagram.com/" target="_blank"><img
                                                        src="../../../public/images/Instagram.png" style="height:300px"></a>
                                            </div>
                                            <div class="image-contain">
                                                <h5 style="text:center">Tik Tok</h5>
                                                <a href="https://www.tiktok.com/en/" target="_blank"><img
                                                        src="../../../public/images/tiktok.png" style="height:300px"></a>
                                            </div>
                                        </carousel>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Banner -->
        <div class="sec-banner bg0 p-t-80 p-b-50">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto">
                        <!-- Block1 -->
                        <div class="block1 wrap-pic-w image-contain">
                            <img src="/images/Bubble Cafe.png" alt="IMG-BANNER" height="400px">

                            <a href="#" class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                                <div class="block1-txt-child2 p-b-4 trans-05">
                                    <div class="block1-link stext-101 cl0 trans-09">
                                        Shop Now
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Product -->
        <section class="bg0 p-t-23 p-b-140">
            <div class="container">
                <div class="p-b-10">
                    <h3 class="ltext-103 cl5">
                        {{ $store.getters.localizedStrings.product.overview }}
                    </h3>
                </div>

                <div class="flex-w flex-sb-m p-b-52">
                    <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                        <a href="/categories/1" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1">
                            {{ $store.getters.localizedStrings.product.all }}
                        </a>
                    </div>
                    <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                        <a href="#" class="stext-106 cl6 hov1 trans-04 m-r-32 m-tb-5 how-active1 text-hover">
                            <!-- {{$menu->name}} -->
                        </a>
                    </div>
                </div>

                <div>
                    <div class="row card-all">
                        <div v-for="product in products" :key="product.id" class="card-item">
                            <div>
                                <div class="block2 w-100">
                                    <div class="block2-pic hov-img0" style="width: 300px; height: 200px;">
                                        <img :src="product.file" class="image-contain w-100"
                                            style="object-fit: cover; width: 100%; height: 100%;">
                                        <a @click="goToDetail(product.id)"
                                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1 justify-content-center">
                                            Quick View
                                        </a>
                                    </div>

                                    <div class="block2-txt flex-w flex-t p-t-14">
                                        <div class="block2-txt-child1 flex-col-l ">
                                            <a @click="goToDetail(product.id)"
                                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                                {{ product.name }}
                                            </a>

                                            <span class="stext-105 cl3">
                                                <div v-if="!product.price_currency">
                                                    <p>
                                                        {{ product.price_sale < product.price ? product.price_sale :
                                                            product.price | formatNumber }} {{ initialValue.user_currency
                                                    }}</p>
                                                </div>
                                                <div v-if="product.price_currency">
                                                    <p>
                                                        {{ product.price_sale_currency < product.price_currency ?
                                                            product.price_sale_currency : product.price_currency |
                                                            formatNumber }} {{ initialValue.user_currency }} 
                                                    </p>
                                                </div>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- @endforeach -->
                    </div>
                    <div class="pagination-wrapper">
                        <span>Rows per page:</span>
                        <span class="total-page">{{ this.meta.from }}-{{ this.meta.to }} of {{ totalPage }}</span>
                        <a-button class="pagination-btn" @click="handlePrevPage">
                            <font-awesome-icon :icon="['fas', 'chevron-left']" />
                        </a-button>
                        <a-button class="pagination-btn" @click="handleNextPage">
                            <font-awesome-icon :icon="['fas', 'chevron-right']" />
                        </a-button>
                    </div>
                </div>
            </div>
        </section>
    </section>
</template>
<script>
import carousel from 'vue-owl-carousel'
import httpRequest from '../axios'
export default {
    components: {
        carousel
    },
    props: {
        msg: String
    },
    data() {
        return {
            sliders: {},
            products: {},
            CountData: 10,
            pageSize: 10,
            lastPage: '',
            totalPage: 2,
            page: 1,
            row: 10,
            meta: {
                "from": 1,
                "to": 10
            },
            initialValue: {
                user_currency: ''
            }
        }
    },
    methods: {
        getResult() {
            httpRequest
                .get('/api/sliders/list')
                .then(
                    ({ data }) => (
                        (this.sliders = data.data)
                    )
                );
            this.loaded = true;
        },
        getProductList(page) {
            httpRequest
                .get('/api/products/list?limit=' + 3 + '&page=' + page)
                .then(
                    ({ data }) => (
                        (this.lastPage = data.meta.last_page),
                        (this.products = data.data),
                        (this.totalPage = data.meta.total),
                        (this.checkPage()),
                        (this.checkRow())
                    )
                );
        },
        goToDetail(id) {
            this.$router.push({ name: "productDetail", params: { id: id } });
        },
        handlePrevPage() {
            if (this.page > 1) {
                this.page = this.page - 1;
            }
            this.checkPage();
            this.getProductList(this.page);
        },
        handleNextPage() {
            this.btn = false;
            if (this.page < this.lastPage) {
                this.page = this.page + 1;
            }
            this.checkPage();
            this.getProductList(this.page);
        },
        checkPage() {
            if (this.page == this.lastPage) {
                this.btnNext = false;
            } else {
                this.btnNext = true;
            }
            if (this.page == 1) {
                this.btnPrew = false;
            } else {
                this.btnPrew = true;
            }
        },
        checkRow() {
            this.meta.from = this.page * 3 - 3 + 1
            this.meta.to = this.meta.from + 3 - 1
            if (this.meta.to >= this.totalPage) {
                this.meta.to = this.totalPage
            }
        },
        getUser() {
            httpRequest
                .get('/api/admin/users/currentUser')
                .then((response) => {
                    this.initialValue.user_currency = response.data.data.currency;
                })
        },
    },
    mounted() {
        this.getResult()
        this.getProductList(this.page)
    },
    created() {
        this.$Progress.start()
        this.getResult()
        this.getProductList(this.page)
        this.getUser()
        this.$Progress.finish()
    },
}
</script>
<style lang="scss" scoped>
.container {
    max-width: 90%;
}

.card-item {
    width: 20%;
    margin: auto;
}

.card-all {
    display: flex;
    justify-content: center;
}

.slider {
    display: block;
    justify-content: center;
}
</style>
