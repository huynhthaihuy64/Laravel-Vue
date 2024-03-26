<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bubble Cafe</title>
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="{{ mix('/css/app.css') }}" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" type="text/css"
		rel="stylesheet" />
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js">
	</script>
	<script src="https://apis.google.com/js/client:platform.js?onload=start" async defer>
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="{{ asset('js/app.js') }}" defer></script>
	<meta name="csrf-token" value="{{ csrf_token() }}" />
	<link rel="icon" type="image/png" href="/template/images/icons/icon-heart-01.png" />
	<!--=====================================/==========================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/fonts/iconic/css/material-design-iconic-font.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/fonts/linearicons-v1.0.0/icon-font.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/animate/animate.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/animsition/css/animsition.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/select2/select2.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/daterangepicker/daterangepicker.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/slick/slick.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/MagnificPopup/magnific-popup.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/vendor/perfect-scrollbar/perfect-scrollbar.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/css/util.css">
	<link rel="stylesheet" type="text/css" href="/template/css/main.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/template/css/style.css">
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body style="background-color: #F5F5F5" class="mt-0 pt-0">
	<div id="app">
		<router-view>
		</router-view>
	</div>
	<script src="{{ mix('/js/app.js') }}"></script>

	<!--===============================================================================================-->
	<script src="/template/vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
	<script src="/template/vendor/bootstrap/js/popper.js"></script>
	<script src="/template/vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="/template/vendor/select2/select2.min.js"></script>
	<script>
		$(".js-select2").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
	<!--===============================================================================================-->
	<script src="/template/vendor/daterangepicker/moment.min.js"></script>
	<script src="/template/vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
	<script src="/template/vendor/parallax100/parallax100.js"></script>
	<script>
		$('.parallax100').parallax100();
	</script>
	<!--===============================================================================================-->
	<script src="/template/vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
	<script>
		$('.gallery-lb').each(function() { // the containers for all your galleries
			$(this).magnificPopup({
		        delegate: 'a', // the selector for gallery item
		        type: 'image',
		        gallery: {
		        	enabled:true
		        },
		        mainClass: 'mfp-fade'
		    });
		});
	</script>
	<!--===============================================================================================-->
	<script src="/template/vendor/isotope/isotope.pkgd.min.js"></script>
	<!--===============================================================================================-->
	<script src="/template/vendor/sweetalert/sweetalert.min.js"></script>
	<script>
		$('.js-addwish-b2').on('click', function(e){
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function(){
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addwish-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-detail');
				$(this).off('click');
			});
		});

		/*---------------------------------------------*/

		$('.js-addcart-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to cart !", "success");
			});
		});
	
	</script>
	<!--===============================================================================================-->
	<script src="/template/vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script>
		$('.js-pscroll').each(function(){
			$(this).css('position','relative');
			$(this).css('overflow','hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function(){
				ps.update();
			})
		});
	</script>
	<!--===============================================================================================-->
	<script src="/template/js/main.js"></script>
	<script src="/template/js/public.js"></script>
	<script>
		/* When the user clicks on the button,
	toggle between hiding and showing the dropdown content */
		function myFunction() {
			document.getElementById("myDropdown").classList.toggle("show");
		}
	</script>
	<script type="text/javascript">
		function googleTranslateElementInit() {
		new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
	}
	</script>

	<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
	</script>
	<!-- Upload File !-->
	<script src="/template/admin/js/main.js"></script>
	<script>
		var botmanWidget = {
	        aboutText: 'Start the conversation with Hi',
	        introMessage: 'WELCOME TO ALL ABOUT LARAVEL',
	        chatServer: '{{ url("/api/botman") }}'
	    };
	</script>
	<script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
</body>

</html>