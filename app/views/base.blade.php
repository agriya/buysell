<!DOCTYPE html>
<html>
	<head>
    	<!-- ========================== BASIC PAGE NEEDS ======================== -->
        <meta charset="utf-8" />
		<title>{{{ $header->getMetaTitle() }}}</title>
		<meta name="keywords" content="{{{ $header->getMetaKeyword() }}}" />
		<meta name="description" content="{{{ $header->getMetaDescription() }}}" />
		<meta name="version" content="{{ Config::get('version.version') }}" />
		<meta name="svn" content="{{ Config::get('version.svn') }}" />

		<!-- ============================ MOBILE SPECIFIC METAS ====================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		@if(isset($d_arr['post_wall_title']) && $d_arr['post_wall_title'] != "")
			<meta property="og:title" content="{{ $d_arr['post_wall_title'] }}"/>
		@else
			<meta property="og:title" content="{{ $header->getMetaTitle() }}"/>
		@endif
		<meta property="og:type" content="website"/>
		<meta property='og:site_name' content="{{ Config::get('generalConfig.site_name') }}" />
		<meta property="og:url" content="{{ Request::url() }}"/>
		@if(isset($d_arr['post_wall_img']) && $d_arr['post_wall_img'] != "")
			<meta property="og:image" content="{{ $d_arr['post_wall_img'] }}"/>
		@else
			<meta property="og:image" content="{{ URL::asset('images/header/logo/logo.png') }}"/>
		@endif

		@if(isset($d_arr['post_wall_description']) && $d_arr['post_wall_description'] != "")
			<meta property="og:description" content="{{ $d_arr['post_wall_description'] }}"/>
			<meta name="description" content="{{ $d_arr['post_wall_description'] }}" />
		@endif


        <!-- ========================== FAVICONS ======================== -->
		<?php $fav_image = CUtil::getSiteLogo('favicon');
			$favicon_url = isset($fav_image['image_url'])?$fav_image['image_url']:URL::asset('/images/header/favicon/favicon.ico');
		?>
		<link rel="shortcut icon" href="{{ $favicon_url }}">

       <!-- =========================== BEGIN GLOBAL MANDATORY STYLES ======================= -->
        <?php $stylesheets[] = '/css/jQuery_plugins/ui-lightness/jquery-ui-1.10.3.custom.css'; ?>
        <?php $stylesheets[] = '/css/jQuery_plugins/jquery.fancyBox-v2.1.5-0/jquery.fancybox.css'; ?>
        <?php $stylesheets[] = '/css/admin/global/plugins/font-awesome/css/font-awesome.css'; ?>
        <?php $stylesheets[] = '/css/admin/global/plugins/bootstrap/css/bootstrap.css'; ?>

        <!-- BEGIN PAGE LEVEL STYLES -->
        @if(BasicCutil::checkIsSriptAllowedInPage('datepicker'))
        	<?php $stylesheets[] = '/css/admin/global/plugins/bootstrap-datepicker/css/datepicker3.css'; ?>
        	<?php $stylesheets[] = '/css/admin/global/plugins/bootstrap-datetimepicker/css/datetimepicker.css'; ?>
        @endif
        @if(BasicCutil::checkIsSriptAllowedInPage('select2'))
        	<?php $stylesheets[] = '/css/admin/global/plugins/select2/select2.css'; ?>
        	<?php $stylesheets[] = '/css/admin/global/plugins/jquery-multi-select/css/multi-select.css'; ?>
        @endif
    	<?php $stylesheets[] = '/css/admin/global/plugins/data-tables/DT_bootstrap.css'; ?>
    	<?php $stylesheets[] = '/css/admin/pages/css/profile.css'; ?>
        <!-- END PAGE LEVEL STYLES -->
		@if (Config::get('generalConfig.minify'))
			{{ Minify::stylesheet($stylesheets)->withFullUrl() }}
		@else
			@foreach($stylesheets as $stylesheet)
				<link href="{{ URL::asset($stylesheet) }}" rel="stylesheet" />
			@endforeach
		@endif
		<!-- ========================= BEGIN THEME STYLES ========================= -->
		<?php $stylesheets1[] = '/css/admin/global/css/components.css'; ?>
		@if (Config::get('generalConfig.minify'))
			{{ Minify::stylesheet($stylesheets1)->withFullUrl() }}
		@else
			@foreach($stylesheets1 as $stylesheet)
				<link href="{{ URL::asset($stylesheet) }}" rel="stylesheet" />
			@endforeach
		@endif
		<?php $stylesheets2[] = '/css/admin/global/css/plugins.css'; ?>
		<?php $stylesheets2[] = '/css/frontend/layout/css/style.css'; ?>
		<?php $stylesheets2[] = '/css/frontend/layout/css/style-responsive.css'; ?>
		<?php $stylesheets2[] = '/css/frontend/core/embed_fonts.css'; ?>
		<?php $stylesheets2[] = '/css/frontend/core/custom.css'; ?>
		@if(Request::is('deals*') )
			<?php $stylesheets2[] = '/../app/plugins/deals/public/css/deals.css'; ?>
        @endif
        <?php $stylesheets2[] = '/css/frontend/core/mobile.css'; ?>

        <?php $stylesheets2[] = '/js/lib/jcarousel-0.3.0/css/jcarousel.connected-carousels.css'; ?>

        @if(BasicCutil::checkIsSriptAllowedInPage('star-rating'))
        	<?php $stylesheets2[] = '/js/lib/star-rating/star-rating.css'; ?>
        @endif

		@if (Config::get('generalConfig.minify'))
	        <!-- combine all stylesheet files -->
			{{ Minify::stylesheet($stylesheets2)->withFullUrl() }}
		@else
			@foreach($stylesheets2 as $stylesheet)
				<link href="{{ URL::asset($stylesheet) }}" rel="stylesheet" />
			@endforeach
		@endif
        <!-- THEME STYLES ENDS -->

        <!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries // HTML5 Shiv Version - 3.7.0 // Respond.js Version - 1.4.2   -->
        <!--[if lt IE 9]>
          {{ Minify::javascript(array('/js/bootstrap/html5shiv.js', '/js/bootstrap/respond.min.js'))->withFullUrl() }}
          {{ Minify::stylesheet('/css/frontend/core/ie.css')->withFullUrl() }}
        <![endif]-->

        <script language="javascript">
			var mes_required = "{{ Lang::get('common.required') }}";
			var bt_site_url = "{{ Url::to('/') }}";
			var page_name = "";
			var remText = '{{ trans("common.words_remaining_1")}} %n {{ trans("common.words_remaining_2")}} %s {{ trans("common.words_remaining_3")}}';
			var limitText = '{{ trans("common.limitted_words_1")}} %n {{ trans("common.limitted_words_2")}}%s';

		</script>
	</head>
	<body class="corporate @if(Request::is('users/login')) login-page @endif">
        <div id="selLoading" class="loading" style="display:none;">
            <img src="{{URL::asset('/images/general/bg_opac.png')}}" alt="image" height="100%" width="100%" />
            <div class="loading-cont">
                <img src="{{ URL::asset('/images/general/loader.gif') }}" alt="loading" />
                <p><strong>{{ trans('common.loading') }}</strong></p>
            </div>
        </div>

        <!-- BEGIN: HEADER -->
    		@include('headerMenu')
        <!-- END: HEADER -->

        <!-- BEGIN: CATEGORY MENU -->
            @if(Request::is('browse*'))
            	@include('categoryMenu')
            @endif
        <!-- END: CATEGORY MENU -->

        <!-- EBGIN: VIEW SHOP LIST -->
        	@if(Request::is('product/view*') || Request::is('product/shop*'))
                @include('viewShopList')
            @endif
        <!-- END: VIEW SHOP LIST -->

        <!-- BEGIN: VIEW DEAL SHOP DETAILS -->
            @if(Request::is('deals/view-deal/*'))
        		@include('deals::viewDealShopDetails')
            @endif
        <!-- END: VIEW DEAL SHOP DETAILS -->

		<!-- BEGIN: FAVORITE LIST -->
       	 	@if(Request::is('favorite-list/*'))
                @include('viewFavoriteProductimage')
            @endif
        <!-- END: FAVORITE LIST -->

        <!-- BEGIN: INDEX BANNER -->
        	<?php $user_id = BasicCUtil::getLoggedUserId(); ?>
            @if(!CUtil::isMember())
                @if(Request::is('/'))
                    @include('indexBanner')
                @endif
            @endif
        <!-- END: INDEX BANNER -->

			<div class="@if(Request::is('/') || Request::is('users/login')) @if(!CUtil::isMember()) main @else main-alt @endif @else main-alt @endif  @if(Request::is('favorite-list/*')) favimgblk @endif">
        	@if(Request::is('/') )
				@if(CUtil::isMember())
                	<div class="container">
                @endif
            @elseif(!Request::is('/'))
            	<?php
				if(Request::is('users/myaccount') || Request::is('shop/users/*') || Request::is('myproducts') || Request::is('product/add') || Request::is('taxations/*') || Request::is('purchases/*') || Request::is('addresses/*') || Request::is('shipping-template*') || Request::is('mycollections/index') || Request::is('coupons/*') || Request::is('invoice*') || Request::is('wishlist/index') || Request::is('transactions') || Request::is('transactions/*')  || Request::is('walletaccount/*') || Request::is('messages*') || Request::is('feedback') || Request::is('feedback/*') || Request::is('users/my-withdrawals/index') || Request::is('users/my-withdrawals') || Request::is('variations') || Request::is('variations/*') || Request::is('importer') || Request::is('importer/*') || Request::is('cart') || Request::is('checkout/*') || Request::is('deals/my-deals') || Request::is('deals/update-deal/*') || Request::is('deals/my-featured-request') || Request::is('deals/set-featured/*') || Request::is('deals/add-deal'))
					$header_cls = 'container-fluid';
				else
					$header_cls = 'container';?>
                <div class="{{$header_cls}}">
                    <div class="margin-bottom-40">
           @endif
                   		@yield('content')
                    </div>
                </div>
        </div>

        <!-- BEGIN: FOOTER -->
        	@include('footerMenu')
        	{{ updateAdvertisementCount() }}
        <!-- END: FOOTER -->

        <!-- LOAD JAVASCRIPTS AT BOTTOM, THIS WILL REDUCE PAGE LOAD TIME -->
        <!-- BEGIN: CORE PLUGINS (REQUIRED FOR ALL PAGES) -->
        <?php $scripts[] = '/css/admin/global/plugins/jquery-1.11.0.min.js'; ?>
        <?php $scripts[] = '/css/admin/global/plugins/jquery-migrate-1.2.1.min.js'; ?>

        <!-- IMPORTANT! LOAD JQUERY-UI-1.10.3.CUSTOM.MIN.JS BEFORE BOOTSTRAP.MIN.JS TO FIX BOOTSTRAP TOOLTIP CONFLICT WITH JQUERY UI TOOLTIP -->
        <?php $scripts[] = '/css/admin/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js'; ?>
        <?php $scripts[] = '/css/admin/global/plugins/bootstrap/js/bootstrap.min.js'; ?>
        <?php $scripts[] = '/css/frontend/layout/scripts/back-to-top.js'; ?>
        <?php $scripts[] = '/css/admin/global/plugins/bootstrap/js/bootstrap-upload.js'; ?>
        <!-- END: CORE PLUGINS -->

        <!-- BEGIN: PAGE LEVEL PLUGINS -->
        @if(BasicCutil::checkIsSriptAllowedInPage('datepicker'))
	        <?php $scripts[] = '/css/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js'; ?>
	        <?php $scripts[] = '/css/admin/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'; ?>
        @endif
        <?php $scripts[] = '/css/admin/global/plugins/bootstrap-select/bootstrap-select.min.js'; ?>
        @if(BasicCutil::checkIsSriptAllowedInPage('select2'))
	        <?php $scripts[] = '/css/admin/global/plugins/select2/select2.min.js'; ?>
	        <?php $scripts[] = '/css/admin/global/plugins/jquery-multi-select/js/jquery.multi-select.js'; ?>
        @endif
        <!-- END: PAGE LEVEL PLUGINS -->

        <!-- BEGIN: PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
        <?php $scripts[] = '/css/admin/global/plugins/fancybox/source/jquery.fancybox.pack.js'; ?>
        <?php $scripts[] = '/js/admin/bootbox.min.js'; ?>
        <?php $scripts[] = '/css/frontend/layout/scripts/layout.js'; ?>
        <?php $scripts[] = '/js/jquery.validate.min.js'; ?>
        <?php $scripts[] = '/js/lib/jquery.inputlimiter.js'; ?>

        @if(BasicCutil::checkIsSriptAllowedInPage('readmore'))
	        <?php $scripts[] = '/js/lib/readmore/readmore.min.js'; ?>
        @endif
        @if(BasicCutil::checkIsSriptAllowedInPage('star-rating'))
	        <?php $scripts[] = '/js/lib/star-rating/star-rating.min.js'; ?>
        @endif

        @if (Config::get('generalConfig.minify'))
	        <!-- combine all javascript files -->
	        {{ Minify::javascript($scripts)->withFullUrl() }}
	        @if(BasicCutil::checkIsSriptAllowedInPage('tinymce'))
	        	<script src="{{ URL::asset('/js/lib/tinymce/tinymce.min.js') }}"></script>
	        @endif
            {{ Minify::javascript('/js/functions.js')->withFullUrl() }}
        @else
	        @foreach($scripts as $script)
	        	<script src="{{ URL::asset($script) }}"></script>
	        @endforeach
	        @if(BasicCutil::checkIsSriptAllowedInPage('tinymce'))
	        	<script src="{{ URL::asset('/js/lib/tinymce/tinymce.min.js') }}"></script>
	        @endif
	        <script src="{{ URL::asset('/js/functions.js') }}"></script>
        @endif

        @if(BasicCutil::checkIsSriptAllowedInPage('readmore'))
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#shop_desc_more').readmore({
                        moreLink: '<a href="#">More</a>',
                        lessLink: '<a href="#">Less</a>',
                        maxHeight: 105,
                        afterToggle: function(trigger, element, expanded) {
                          /*if(! expanded) { // The "Close" link was clicked
                            $('html, body').animate( { scrollTop: element.offset().top }, {duration: 100 } );
                          }*/
                        }
                    });
                });
            </script>
        @endif

        @if(Request::is('shop/users/shop-details') || Request::is('shop/users/shop-policy-details'))
        	@if (Config::get('generalConfig.minify'))
		    	{{ Minify::javascript(array('/js/jquery.form.js', '/js/bootstrap/bootstrap.file-input.js'))->withFullUrl() }}
		    @else
				<script src="{{ URL::asset('/js/jquery.form.js') }}"></script>
				<script src="{{ URL::asset('/js/bootstrap/bootstrap.file-input.js') }}"></script>
		    @endif
			<script>
				$(document).ready(function(){
					$('input[type=file]').bootstrapFileInput();
				});
			</script>
		@endif

		@if(Request::is('pay-checkout/*') || Request::is('walletaccount/add-amount') || Request::is('purchases/order-details/*') || Request::is('invoice/invoice-details/*'))
			<script src="{{ URL::asset('/js/card_validation.js') }}"></script>
		@endif
		@if(Request::is('product/view/*'))
			@if(!isset($d_arr['error_msg']) || $d_arr['error_msg'] == '' )
			    <script src="{{ URL::asset('/js/lib/jcarousel-0.3.0/js/jquery.jcarousel.min.js') }}"></script>
			    @if (Config::get('generalConfig.minify'))
			    	{{ Minify::javascript(array('/js/lib/jcarousel-0.3.0/js/jcarousel.connected-carousels.js'))->withFullUrl() }}
			    @else
			    	<script src="{{ URL::asset('/js/lib/jcarousel-0.3.0/js/jcarousel.connected-carousels.js') }}"></script>
			    @endif
			@endif
		@endif
		@if(Request::is('deals/view-deal/*'))
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4decc2325f9111d1"></script>
		@endif

        <script type="text/javascript">
        	$(document).ready(function(){
				// Initialize tooltip
			    $('[data-toggle="tooltip"]').tooltip({
			        placement : 'top'
			    });
			});
		</script>

        <script type="text/javascript">
        	function updateCurrency(currency_code) {
				//$('#country_list').hide();
				var path = ""+window.location+"";
				if(window.location.hash != "") {
					window.location.hash = "";
				}
				if(path.indexOf("#") >= 0) {
					path = window.location.href.slice(0, -1);
				}
				displayLoadingImage(true);
				$.post("{{ Url::to('updateCurrency')}}", {"currency_code": currency_code}, function(data) {
					window.location = path;
				})
			}

			function updateLanguage(lang_code) {
				var path = ""+window.location+"";
				if(window.location.hash != "") {
					window.location.hash = "";
				}
				if(path.indexOf("#") >= 0) {
					path = window.location.href.slice(0, -1);
				}
				displayLoadingImage(true);
				$.post("{{ Url::to('updateLanguage')}}", {"lang_code": lang_code}, function(data) {
					window.location = path;
				})
			}
        </script>

        <script type="text/javascript">
			$(document).ready(function() {
				$('.acc_dropdown').click(function() {
					$('#acc_menu').slideToggle(500);
					// toggle open/close symbol
					var span_elm = $('.acc_dropdown i');
					if(span_elm.hasClass('fa fa-chevron-circle-up')) {
						$('.acc_dropdown').html('<i class="fa fa-chevron-circle-down pull-right margin-top-3"></i> {{ trans("Show Account Menu") }}');
					} else {
						$('.acc_dropdown').html('<i class="fa fa-chevron-circle-up pull-right margin-top-3"></i> {{ trans("Hide Account Menu") }}');
					}
					return false;
				});

				$('.shop-accmenu').click(function() {
					$('#shop_menu').slideToggle(500);
					// toggle open/close symbol
					var span_elm = $('.shop-accmenu i');
					if(span_elm.hasClass('fa fa-chevron-up')) {
						$('.shop-accmenu').html('<i class="fa fa-truck"></i><i class="fa fa-chevron-down pull-right margin-top-3"></i> {{ Lang::get('common.manage_shop') }}');
					} else {
						$('.shop-accmenu').html('<i class="fa fa-truck"></i><i class="fa fa-chevron-up pull-right margin-top-3"></i> {{ Lang::get('common.manage_shop') }}');
					}
					return false;
				});

				$('.btn-mobmenu').click(function(){
					$('.custom-mobnav').slideToggle(500);
					var span_elm = $('.btn-mobmenu i');
					if(span_elm.hasClass('fa fa-chevron-circle-up')) {
						$('.btn-mobmenu').html('<i class="fa fa-chevron-circle-down pull-right margin-top-3"></i> {{ trans("Show Product Menu") }}');
					} else {
						$('.btn-mobmenu').html('<i class="fa fa-chevron-circle-up pull-right margin-top-3"></i> {{ trans("Hide Product Menu") }}');
					}
					return false;
				});

				$('.mobviewmenu-480 button').click(function(){
					$('.mobviewmenu-480 ul').slideToggle(1000);
				});

				$('#js-search-header,#js-search-header-resp').click(function(){
					$('#searchHeaderFrm').submit();
				});

				$(".all-option li a").click(function(){
					$(this).parents(".input-group").find('.option-txt').val($(this).text()).text($(this).text());
				});

				$('.js-action-selection').click(function(){
					var action = $(this).data('action');

					var url = '{{Url::to('product')}}';
					var search_text_name = 'tag_search';
					var text_to_disp = $(this).text();
					switch(action)
					{
						case 'shop':
							url = '{{Url::to('shop')}}';
							search_text_name = 'shop_name';
							break;
						case 'user':
							url = '{{Url::to('users')}}';
							search_text_name = 'uname';
							break;
						default:
							url = '{{Url::to('product')}}';
							search_text_name = 'tag_search';
							break;
					}
					$('#header_srch_text_to_disp').text(text_to_disp);
					$('#searchHeaderFrm').attr('action',url);
					$('#header_tag_search').attr('name',search_text_name);
				});

				$(".tooltip-viewtop span").tooltip({
					placement : 'top'
				});
			});
		</script>
		<script type="text/javascript">
			var saveclass = null;
			function UpdateCountry(cookieValue){
				var id = $('#Update_Country_Cookie').attr('id');
				//alert(cookieValue);return;
				saveclass = saveclass ? saveclass : document.body.className;
				document.body.className = saveclass + ' ' + id.value;
				setCookie('stock_country', cookieValue, 365);
			}
			function setCookie(cookieName, cookieValue, nDays) {
				//alert(cookieName);
				var today = new Date();
				var expire = new Date();
				if (nDays==null || nDays==0)
					nDays=1;
				expire.setTime(today.getTime() + 3600000*24*nDays);
				document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString()+"; path=/";
				window.location.reload();
			}
		</script>
		<script type="text/javascript">
			function gotoSocialInPage(url, social_type) {
				if(social_type == 'facebook')
				{
					window.open(url,'mywindow', 'width=635,height=320,left=100,top=100,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no');
				} else if(social_type == 'linkedin')
				{
					window.open(url,'mywindow', 'width=635,height=220,left=100,top=100,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no');
				}
				else if(social_type == 'gplus')
				{
					top.location.href = url;
				}
		};
		</script>

        <script type="text/javascript">
            jQuery(document).ready(function() {
			   Layout.init(); // init layout
            });
        </script>

        <!--[if lte IE 9]>
            <script type="text/javascript">
                $(document).ready(function(){
                    function add() {
                        if($(this).val() === ''){
                            $(this).val($(this).attr('placeholder')).addClass('placeholder');
                        }
                    }

                    function remove() {
                        if($(this).val() === $(this).attr('placeholder')){
                            $(this).val('').removeClass('placeholder');
                        }
                    }

                    // Create a dummy element for feature detection
                    if (!('placeholder' in $('<input>')[0])) {

                    // Select the elements that have a placeholder attribute
                        $('input[placeholder], textarea[placeholder]').blur(add).focus(remove).each(add);

                        // Remove the placeholder text before the form is submitted
                        $('form').submit(function(){
                            $(this).find('input[placeholder], textarea[placeholder]').each(remove);
                        });
                    }
                });
            </script>
        <![endif]-->
        <!-- END: PAGE LEVEL JAVASCRIPTS -->
        @yield("script_content")
	</body>
</html>