<template>
    <section>
        <div class="container p-t-80">
            <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
                <a href="/" class="stext-109 cl8 hov-cl1 trans-04">
                    {{ $store.getters.localizedStrings.shop }}
                    <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                </a>

                <span class="stext-109 cl4">
                    {{ initialValue.edit_name }}
                </span>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <a-tabs v-model="activeKey" tab-position="left" :style="{ height: '600px' }">
                                <a-tab-pane v-for="(item, index) in initialValue.images" :key="item.id">
                                    <template slot="tab">
                                        <img :src="item.url" :alt="'Tab ' + (index + 1)" />
                                    </template>
                                    <div class="slick3 gallery-lb slick-initialized slick-slider slick-dotted tab-pane active"
                                        id="pic-1">
                                        <div class="slick-list draggable">
                                            <div class="slick-track" style="opacity: 1; width: 200px;">
                                                <div class="slick-track" style="opacity: 1; width: 200px;">
                                                <div class="item-slick3 slick-slide slick-current slick-active"
                                                    data-thumb="images/product-detail-01.jpg" data-slick-index="0"
                                                    aria-hidden="false"
                                                    style="width: 200px; position: relative; left: 0px; top: 0px; z-index: 999; opacity: 1;"
                                                    tabindex="0" role="tabpanel" id="slick-slide10"
                                                    aria-describedby="slick-slide-control10">
                                                    <div class="wrap-pic-w pos-relative">
                                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTHZAq08u4YaR0Jsu2CgeptdxC74y-9QEeFYEAb6YHP&s"
                                                            class="image-contain" alt="IMG-PRODUCT" height="300px">

                                                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                            :href="initialValue.edit_img" tabindex="0">
                                                            <i class="fa fa-expand"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </a-tab-pane>
                            </a-tabs>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">

                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            {{ initialValue.edit_name }}
                        </h4>

                        <span class="mtext-106 cl2">
                            {{ initialValue.edit_price_sale < initialValue.edit_price ? initialValue.edit_price_sale :
                                initialValue.edit_price | formatNumber }} </span>

                                <p class="stext-102 cl3 p-t-23">
                                    {{ initialValue.edit_description | truncate(30, '...') }}
                                </p>

                                <!--  -->
                                <div class="p-t-33">
                                    <div class="flex-w flex-r-m p-b-10">
                                        <div class="size-204 flex-w flex-m respon6-next">
                                            <a-form :form="form" id="form-container" @submit="addToCart">
                                                <!-- @if ($product->price !== NULL) -->
                                                <a-form-item class="form-item">
                                                    <a-input v-decorator="['product_id', {
                                                        initialValue: initialValue.edit_id,
                                                    }]" type="hidden" />
                                                </a-form-item>
                                                <a-form-item name="input-number" no-style>
                                                    <a-input-number v-decorator="['qty', {
                                                        initialValue: 1,
                                                    }]" :min="1" :max="100" />
                                                </a-form-item>
                                                <a-button html-type="submit"
                                                    class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 ">
                                                    {{ $store.getters.localizedStrings.product.detail.add_cart }}
                                                </a-button>
                                            </a-form>
                                        </div>
                                    </div>
                                </div>

                                <!--  -->
                                <div class="flex-w flex-m p-l-100 p-t-40 respon7">
                                    <div class="flex-m bor9 p-r-10 m-r-11">
                                        <a href="#"
                                            class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100"
                                            data-tooltip="Add to Wishlist">
                                            <i class="zmdi zmdi-favorite"></i>
                                        </a>
                                    </div>

                                    <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                        data-tooltip="Facebook">
                                        <i class="fab fa-1x fa-facebook"></i>
                                    </a>

                                    <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                        data-tooltip="Instagram">
                                        <i class="fab fa-1x fa-instagram-square"></i>
                                    </a>

                                    <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                        data-tooltip="Tik Tok">
                                        <i class="fab fa-1x fa-tiktok"></i>
                                    </a>
                                </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bor10 m-t-50 p-t-43 p-b-40">
            <!-- Tab01 -->
            <div class="tab01">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item p-b-10">
                        <a class="nav-link active" data-toggle="tab" href="#description" role="tab">{{
                            $store.getters.localizedStrings.product.detail.description }}</a>
                    </li>

                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#information" role="tab">{{
                            $store.getters.localizedStrings.product.detail.information }}</a>
                    </li>

                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">{{
                            $store.getters.localizedStrings.product.detail.review }} ({{ comments.length }})</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-t-43">
                    <!-- - -->
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <div class="how-pos2 p-lr-15-md">
                            <div class="detail-content" v-html="initialValue.content"></div>
                        </div>
                    </div>

                    <!-- - -->
                    <div class="tab-pane fade" id="information" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto info-item">
                                <ul class="p-lr-28 p-lr-15-sm">
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            {{ $store.getters.localizedStrings.product.detail.original_price }}
                                        </span>

                                        <span class="stext-102 cl6 size-206">
                                            {{ initialValue.edit_price }}
                                        </span>
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            {{ $store.getters.localizedStrings.product.detail.price_sale }}
                                        </span>

                                        <span class="stext-102 cl6 size-206">
                                            {{ initialValue.edit_price_sale }}
                                        </span>
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            {{ $store.getters.localizedStrings.product.detail.categories }}
                                        </span>

                                        <span class="stext-102 cl6 size-206">
                                            {{ initialValue.menuName }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <div class="p-b-30 m-lr-15-sm">
                                    <!-- Review -->
                                    <div class="flex-w flex-t p-b-68">
                                        <div class="wrap-pic-s size-109 bor0 of-hidden m-r-18 m-t-6">
                                            <img :src="initialValue.edit_img" alt="AVATAR" class="image-contain">
                                        </div>

                                        <div class="size-207">
                                            <div class="flex-w flex-sb-m p-b-17">
                                                <span class="mtext-107 cl2 p-r-20">
                                                    {{ initialValue.edit_name }}
                                                </span>

                                                <span class="fs-18 cl11">
                                                    <i class="zmdi zmdi-star"></i>
                                                    <i class="zmdi zmdi-star"></i>
                                                    <i class="zmdi zmdi-star"></i>
                                                    <i class="zmdi zmdi-star"></i>
                                                    <i class="zmdi zmdi-star-half"></i>
                                                </span>
                                            </div>

                                            <p class="stext-102 cl6">
                                                {{ initialValue.edit_description }}
                                            </p>
                                        </div>
                                    </div>
                                    <hr />
                                    <h4> {{ $store.getters.localizedStrings.comment.display }}</h4>
                                    <div v-if="comments.length > 0" class="mb-2">
                                        <div v-for="(item) in comments" :key="item.id" class="display-comment mb-2"
                                            style="margin-left:40px;">
                                            <div v-if="item.parent_id == 0">
                                                <div class="col-12">
                                                    <img :src="item.user.avatar" height="50px" width="50px"
                                                        class="image-contain">
                                                    <strong class="ml-2">
                                                        {{ item.user.name }}:
                                                    </strong>
                                                    <i class="ml-3">
                                                        {{ item.created_at }}
                                                    </i>
                                                </div>
                                                <p class="mt-2 ml-5">
                                                    {{ item.body }}
                                                </p>
                                                <a-form :form="form" @submit.prevent="createComment($event, item.id)">
                                                    <a-form-item hidden>
                                                        <a-input v-decorator="[
                                                            'product_id', {
                                                                initialValue: initialValue.edit_id,
                                                            }]" type="hidden">
                                                        </a-input>
                                                    </a-form-item>
                                                    <a-form-item class="form-item">
                                                        <a-input v-decorator="['body']"
                                                            placeholder="Write Something..."></a-input>
                                                    </a-form-item>
                                                    <a-button type="primary" html-type="submit" class="login-form-button">
                                                        {{ $store.getters.localizedStrings.comment.reply }}
                                                    </a-button>
                                                </a-form>
                                                <div v-if="item.replies.length > 0">
                                                    <div v-for="(item) in item.replies" :key="item.id" class="mb-2 mt-3"
                                                        style="margin-left:40px;">
                                                        <div v-if="item.parent_id != 0">
                                                            <div class="col-12">
                                                                <img :src="item.user.avatar" height="50px" width="50px"
                                                                    class="image-contain">
                                                                <strong class="ml-2">
                                                                    {{ item.user.name }}:
                                                                </strong>
                                                                <i class="ml-3">
                                                                    {{ item.created_at }}
                                                                </i>
                                                            </div>
                                                            <p class="mt-2 ml-5">
                                                                {{ item.body }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h4>{{ $store.getters.localizedStrings.comment.add_comment }}</h4>
                                    <a-form>
                                        <a-form-item v-model="form2.product_id">
                                            <a-input :value="initialValue.edit_id" type="hidden">
                                            </a-input>
                                        </a-form-item>

                                        <a-form-item class="form-item">
                                            <a-textarea v-model="form2.body" placeholder="Write Something..."></a-textarea>
                                        </a-form-item>
                                        <a-button type="primary" @click.prevent="createComment" class="login-form-button">
                                            {{ $store.getters.localizedStrings.comment.add_comment }}
                                        </a-button>
                                    </a-form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg6 flex-c-m flex-w size-302 m-t-73 p-tb-15">

            <span class="stext-107 cl6 p-lr-25">
                {{ $store.getters.localizedStrings.product.detail.categories }}: {{ initialValue.menuName }}
            </span>
        </div>

        <div class="container">
            <div class="p-b-45">
                <h3 class="ltext-106 cl5 txt-center">
                    {{ $store.getters.localizedStrings.product.relative }}
                </h3>
            </div>

            <div>
                <div class="row card-all">
                    <div v-for="product in products" :key="product.id" class="card-item">
                        <div>
                            <div class="block2 w-100">
                                <div class="block2-pic hov-img0">
                                    <img :src="product.file" height="200px" class="w-100 image-contain">
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
                                            <div v-if="product.price_sale != 0">
                                                <p>{{ product.price_sale | formatNumber }}</p>
                                            </div>
                                            <div v-if="product.price_sale == 0 || product.price_sale == null">
                                                <p>{{ product.price | formatNumber }}</p>
                                            </div>
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- @endforeach -->
                </div>
                <div class="pagination-wrapper mb-4">
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
</template>
<script>
import httpRequest from '../axios'
import { ref } from 'vue';
export default {
    beforeCreate() {
        this.form = this.$form.createForm(this, { name: 'comment_form' })
    },
    filters: {
        truncate: function (text, length, suffix) {
            return text.substring(0, length) + suffix;
        },
    },
    data() {
        return {
            activeKey: ref('1'),
            products: {},
            comments: {},
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
            id: this.$route.params.id,
            initialValue: {
                edit_name: '',
                edit_description: '',
                edit_price: 20,
                edit_price_sale: 30,
                edit_img: [],
                menus: [],
                images: [],
                menuName: '',
                edit_active: 1,
                edit_id: 1,
                content: '',
            },
            form2: {
                body: '',
            },
        }
    },
    methods: {
        async getProductDetail() {
            const response = await
                httpRequest
                    .get('/api/products/edit/' + this.$route.params.id)
                    .then(
                        ({ data }) => (
                            this.initialValue.edit_name = data.data.name,
                            this.initialValue.edit_description = data.data.description,
                            this.initialValue.edit_price = data.data.price,
                            this.initialValue.edit_price_sale = data.data.price_sale,
                            this.initialValue.content = data.data.content,
                            this.initialValue.edit_img = data.data.file,
                            this.initialValue.edit_active = data.data.active,
                            this.initialValue.edit_id = data.data.id,
                            this.initialValue.menuName = data.data.menuName,
                            this.initialValue.menus = data.data.menus.map((item) => {
                                return {
                                    id: item.id,
                                };
                            }),
                            this.initialValue.images = data.data.images.map((item) => {
                                return {
                                    id: item.id,
                                    name: item.name,
                                    url: item.url
                                };
                            })
                        )
                    );
            this.getRelativeProduct(this.initialValue.menus);
            this.loaded = true;
        },
        getRelativeProduct(ids, page) {
            try {
                const MenuIds = ids.reduce((acc, item) => {
                    return [...acc, item.id]
                }, [])
                httpRequest
                    .get('/api/products/relative?limit=' + 3 + '&page=' + this.page, {
                        params: {
                            ids: MenuIds
                        }
                    })
                    .then(
                        ({ data }) => (
                            (this.products = data.data),
                            (this.totalPage = data.total),
                            (this.lastPage = data.last_page),
                            (this.checkPage()),
                            (this.checkRow())
                        )
                    );
            } catch (error) {
                throw error;
            }
        },
        goToDetail(id) {
            this.$router.push({ name: "productDetail", params: { id: id } });
            this.$router.go();
        },
        getUser() {
            httpRequest
                .get('/api/admin/users/currentUser')
                .then((response) => {
                    this.initialValue.user_name = response.data.name;
                    this.initialValue.user_id = response.data.id;
                    this.initialValue.user_avatar = response.data.avatar;
                })
        },
        getComment() {
            httpRequest
                .get('/api/comment/' + this.$route.params.id)
                .then((data) => {
                    this.comments = data.data;
                })
        },
        createComment(e, id) {
            e.preventDefault();
            this.form
                .validateFields((err, values) => {
                    if (err) return;
                    let formData = new FormData();
                    if (!values.body) {
                        formData.append('product_id', this.$route.params.id)
                        _.each(this.form2, (value, key) => {
                            formData.append(key, value)
                        });
                    } else {
                        formData.append('parent_id', id)
                        _.each(values, (value, key) => {
                            formData.append(key, value)
                        });
                    }
                    httpRequest.post('/api/comment', formData).then((response) => {
                        this.info = response
                        Toast.fire({
                            icon: 'success',
                            title: 'Comment Success'
                        });
                        this.getComment();
                        this.form.resetFields();
                        this.form2.resetFields();
                    })
                }).catch(function (error) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Comment Failed'
                    });
                });
        },
        handlePrevPage() {
            if (this.page > 1) {
                this.page = this.page - 1;
            }
            this.checkPage();
            this.getRelativeProduct(this.initialValue.menus, this.page);
        },
        handleNextPage() {
            this.btn = false;
            if (this.page < this.lastPage) {
                this.page = this.page + 1;
            }
            this.checkPage();
            this.getRelativeProduct(this.initialValue.menus, this.page);
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
        addToCart(e) {
            e.preventDefault();
            this.form
                .validateFields((err, values) => {
                    if (err) return;
                    const formData = new FormData();
                    formData.append("qty", values.qty);
                    formData.append("product_id", values.product_id)
                    httpRequest.post('/api/add-cart', formData).then((response) => {
                        this.info = response
                        Toast.fire({
                            icon: 'success',
                            title: 'Add Cart Success'
                        });
                        this.form.resetFields();
                    })
                }).catch(function (error) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Add Cart Failed'
                    });
                });
        }
    },
    mounted() {
        console.log('Component mounted.')
    },
    created() {
        this.$Progress.start()
        this.getProductDetail(this.page)
        this.getUser()
        this.getComment()
        this.$Progress.finish()
    },
}
</script>
<style scoped>
.info-item {
    display: flex;
    justify-content: center;
}

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

.display-comment {
    border-style: ridge;
    padding: 20px;
}

.slider {
    display: block;
    justify-content: center;
}</style>