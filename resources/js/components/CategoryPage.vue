<template>
	<section>
		<div class="inner-header">
			<div class="container">
				<div class="pull-left">
					<h6 class="inner-title">{{ initialValue.edit_name }}</h6>
				</div>
				<div class="pull-right">
					<div class="beta-breadcrumb font-large">
						<a href="/">Home</a> / <span>Danh Mục</span>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="container">
			<div id="content" class="space-top-none">
				<div class="main-content">
					<div class="space60">&nbsp;</div>
					<div class="row">
						<div class="col-sm-3">
							<ul class="aside-menu border-top menu-side my-0 mx-0 bg-secondary text-white" v-for="menu in menus" :key="menu.id">
								<li class="ml-2 mt-2"><a @click="goToMenuDetail(menu.id)">{{ menu.name }}</a></li>
							</ul>
						</div>
						<div class="col-sm-9">
							<div class="beta-products-list">
								<h4>Sản Phẩm</h4>
								<div class="beta-products-details">
									<p class="pull-left">Tìm thấy {{ products.length }} sản phẩm</p>
									<div class="clearfix"></div>
								</div>

								<div class="row">
									<div v-for="product in products" :key="product.id" class="card-item col-sm-4">
										<div>
											<div class="block2 w-100">
												<div class="block2-pic hov-img0">
													<img :src="product.file" height="200px" class="image-contain w-100">
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
															<div
																v-if="product.price_sale == 0 || product.price_sale == null">
																<p>{{ product.price | formatNumber }}</p>
															</div>
														</span>
													</div>

												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="pagination-wrapper">
									<span>Rows per page:</span>
									<span class="total-page">{{ this.meta.from }}-{{ this.meta.to }} of {{ totalPage
									}}</span>
									<a-button class="pagination-btn" @click="handlePrevPage">
										<font-awesome-icon :icon="['fas', 'chevron-left']" />
									</a-button>
									<a-button class="pagination-btn" @click="handleNextPage">
										<font-awesome-icon :icon="['fas', 'chevron-right']" />
									</a-button>
								</div>
							</div> <!-- .beta-products-list -->

							<div class="space50">&nbsp;</div>
						</div>
					</div> <!-- end section with sidebar and main content -->


				</div> <!-- .main-content -->
			</div> <!-- #content -->
		</div> <!-- .container -->
	</section>
</template>
<script>
import httpRequest from '../axios'
export default {
	data() {
		return {
			form: this.$form.createForm(this),
			menus: {},
			products: {},
			meta: {
				"from": 1,
				"to": 6
			},
			id: this.$route.params.id,
			initialValue: {
				edit_name: '',
				edit_id: 1,
				edit_parent_id: '',
				edit_active: '',
				edit_description: '',
				content: '',
			},
		}
	},
	computed: {
	},
	methods: {
		async getMenuDetail() {
			const response = await
				httpRequest
					.get('/api/menus/edit/' + this.$route.params.id)
					.then(
						({ data }) => (
							console.log(data),
							this.initialValue.edit_name = data.name,
							this.initialValue.edit_parent_id = data.parent_id,
							this.initialValue.edit_description = data.description,
							this.initialValue.content = data.content,
							this.initialValue.edit_active = data.active,
							this.initialValue.edit_id = data.id
						)
					);
		},
		goToMenuDetail(id) {
			this.$router.push({ name: "Category", params: { id: id } });
			this.$router.go();
		},
		async getProductMenu() {
			const response = await
				httpRequest
					.get('/api/products/productMenu?menu_id=' + this.$route.params.id)
					.then(
						({ data }) => (
							(this.products = data.data)
								(this.totalPage = data.meta.total),
							(this.lastPage = data.meta.last_page),
							(this.checkPage()),
							(this.checkRow())
						)
					);
		},
		getMenu() {
			httpRequest
				.get('/api/menus/list')
				.then(
					({ data }) => (
						(this.menus = data.data)
					)
				);
		},
		handlePrevPage() {
			if (this.page > 1) {
				this.page = this.page - 1;
			}
			this.checkPage();
			this.getProductMenu(this.page);
		},
		handleNextPage() {
			this.btn = false;
			if (this.page < this.lastPage) {
				this.page = this.page + 1;
			}
			this.checkPage();
			this.getProductMenu(this.page);
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
	},
	mounted() {
		console.log('Component mounted.')
		this.getMenu()
		this.getProductMenu()
	},
	created() {
		this.$Progress.start()
		this.getMenuDetail(this.page)
		this.$Progress.finish()
	},
}
</script>

<style lang="scss" scoped>
.menu-side {
	height: 50px;
}
</style>