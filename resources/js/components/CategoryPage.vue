<template>
    <section>
        <div class="inner-header">
		<div class="container">
			<div class="pull-left">
				<h6 class="inner-title">Sản phẩm Name</h6>
			</div>
			<div class="pull-right">
				<div class="beta-breadcrumb font-large">
					<a href="#">Home</a> / <span>Sản phẩm</span>
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
						<ul class="aside-menu">
							<li><a href="#">Name</a></li>
						</ul>
					</div>
					<div class="col-sm-9">
						<div class="beta-products-list">
							<h4>New Products</h4>
							<div class="beta-products-details">
								<p class="pull-left">Tìm thấy #</p>
								<div class="clearfix"></div>
							</div>

							<div class="row">
								<div class="col-sm-4">
									<div class="single-item">
										<div class="single-item-header">
											<a href="#"><img src="" alt="" height="250px"></a>
										</div>
										<div class="single-item-body">
											<p class="single-item-title">Name</p>
											<p class="single-item-price">
												<span class="flash-sale">price</span>
											</p>
										</div>
										<div class="single-item-caption">
											<a class="add-to-cart pull-left" href="#"><i class="fa fa-shopping-cart"></i></a>
											<a class="beta-btn primary" href="#">Details <i class="fa fa-chevron-right"></i></a>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
							</div>
						</div> <!-- .beta-products-list -->

						<div class="space50">&nbsp;</div>

						<div class="beta-products-list">
							<h4>Sản Phẩm Khác</h4>
							<div class="beta-products-details">
								<p class="pull-left">Tìm thấy #</p>
								<div class="clearfix"></div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="single-item">
                                    <div class="ribbon-wrapper"><div class="ribbon sale">Sale</div></div>
										<div class="single-item-header">
											<a href="product.html"><img src="" alt="" height="250px"></a>
										</div>
										<div class="single-item-body">
											<p class="single-item-title">NAme}</p>
											<p class="single-item-price">
												<span class="flash-sale">price</span>
											</p>
										</div>
										<div class="single-item-caption">
											<a class="add-to-cart pull-left" href="shopping_cart.html"><i class="fa fa-shopping-cart"></i></a>
											<a class="beta-btn primary" href="product.html">Details <i class="fa fa-chevron-right"></i></a>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">aaa</div><!--phân trang -->
							<div class="space40">&nbsp;</div>
							
						</div> <!-- .beta-products-list -->
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
            carts: {},
            totalPrice: 1,
            tax: 1,
            total_tax: 1,
            fields: [{
                rowId: '',
                qty: 1,
            }],
            categories: {
                edit_name: '',
                edit_id: 1,
                edit_email: '',
                edit_phone: '',
                edit_address: '',
                role: 1,
            },
        }
    },
    computed: {
    },
    methods: {
    },
    mounted() {
        console.log('Component mounted.')
    },
    created() {
    },
}
</script>