<!DOCTYPE html>
<html>
	<head>
		<title>{{ $header->getMetaTitle() }}</title>
		<meta name="keywords" content="{{ $header->getMetaKeyword() }}" />
		<meta name="description" content="{{ $header->getMetaDescription() }}" />

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Favicons
		================================================== -->
		<?php $fav_image = CUtil::getSiteLogo('favicon');
			$favicon_url = isset($fav_image['image_url'])?$fav_image['image_url']:URL::asset('/images/header/favicon/favicon.ico');
		?>
		<link rel="shortcut icon" href="{{ $favicon_url }}">

       <!-- BEGIN GLOBAL MANDATORY STYLES
		================================================== -->
        <link href="{{ URL::asset('/css/jQuery_plugins/ui-lightness/jquery-ui-1.10.3.custom.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/jQuery_plugins/jquery.fancyBox-v2.1.5-0/jquery.fancybox.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"/>

        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap-datepicker/css/datepicker3.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap-datetimepicker/css/datetimepicker.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/select2/select2.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/data-tables/DT_bootstrap.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/pages/css/profile.css') }}" rel="stylesheet"/>
        <!-- END PAGE LEVEL STYLES -->

		<!-- BEGIN THEME STYLES
		================================================== -->
        <link href="{{ URL::asset('/css/admin/global/css/components.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/layout/css/style.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/layout/css/style-responsive.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/core/embed_fonts.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/core/custom.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/core/mobile.css') }}" rel="stylesheet"/>
        <!-- Theme Styles Ends -->

        <link rel="stylesheet" href="{{ URL::asset('/js/lib/jcarousel-0.3.0/css/jcarousel.connected-carousels.css') }}" />

        <!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries // HTML5 Shiv Version - 3.7.0 // Respond.js Version - 1.4.2   -->
        <!--[if lt IE 9]>
          <script src="{{ URL::asset('/js/bootstrap/html5shiv.js') }}"></script>
          <script src="{{ URL::asset('/js/bootstrap/respond.min.js') }}"></script>
          <link rel="stylesheet" href="{{ URL::asset('/css/core/ie.css') }}">
        <![endif]-->


        <style type="text/css">
			body {
				overflow:hidden;
				text-align:left;
			}
			h1.logo {
				margin:13px 40px 0 10px;
			}
			h1.logo a {
				padding:0;
			}
			.iframe-class {
				background:#fff;
				height:100%;
				width:100%;
				float:left;
				overflow-x:hidden;
				overflow-y:auto;
				position:absolute;
			}
			header.navbar {
				background-color:#fff;
				border-bottom:2px solid #ccc;
				min-height:45px;
				margin:0;
				border-radius:0;
			}
			.mt15 {
				margin-top:15px;
			}
			.navbar li {
				margin:0 5px;
			}
			.fa-times {
				font-size:22px;
			}
			.fa-times:hover {
				color:#9E7835;
			}

			.demourl-dropdown {
				margin-top:-8px;
			}

			.demourl-dropdown .dropdown-menu {
				padding:10px;
				width:250px;
			}
		</style>

        <script language="javascript">
			var mes_required = "{{ Lang::get('common.required') }}";
			var bt_site_url = "{{ Url::to('/') }}";
			var page_name = "";
		</script>
	</head>
	<body class="corporate">
        <div id="selLoading" class="loading" style="display:none;">
            <img src="{{URL::asset('/images/general/bg_opac.png')}}" alt="image" height="100%" width="100%" />
            <div class="loading-cont">
                <img src="{{ URL::asset('/images/general/loader.gif') }}" alt="loading" />
                <p><strong>{{ trans('common.loading') }}</strong></p>
            </div>
        </div>

        <div class="header">
		    <div class="container">
		        <h1 class="navbar-brand">
		        	<?php $logo_image = CUtil::getSiteLogo();
						$image_url = isset($logo_image['image_url'])?$logo_image['image_url']:URL::asset('/images/header/logo/logo.png');
					 ?>
		        	<a href="{{ URL::to('product') }}" title="{{ Config::get('generalConfig.site_name') }}">
		            	<img class="logo-default" src="{{ $image_url }}" alt="{{ Config::get('generalConfig.site_name') }}" />
		        	</a>
		        </h1>
		         <ul class="list-inline pull-right">
	            	<li><a href="{{ Url::to($d_arr['iframe_url']) }}" title="Close"><i class="fa fa-times text-warning"></i></a></li>
	            </ul>

	            <ul class="list-inline pull-right">
	                <li>{{ HTML::link(Url::to('item/'.$p_details['product_code'].'-'.$p_details['url_slug']), $p_details['product_name'], array()) }}</li>
	            </ul>
		        <a href="javascript:void(0);" class="mobi-toggler"><i class="fa fa-bars"></i></a>
		        <div class="header-navigation pull-right font-transform-inherit">
		            @if(trim($d_arr['demo_details']) != "")
		                <div class="dropdown pull-right demourl-dropdown">
		                    <a data-toggle="dropdown" href="#" class="selBtn btn btn-info">Demo Details</a>
		                    <ul id="selDetails" class="dropdown-menu" style="display:none" role="menu" aria-labelledby="dLabel">
		                        <li>{{ nl2br(e($d_arr['demo_details'])) }} </li>
		                    </ul>
		                </div>
					@endif
		        </div>
		    </div>
		</div>
		<iframe id="myframe" class="iframe-class" src="{{ $d_arr['iframe_url'] }}" name="myframe" scrolling="yes" allowtransparency="true" marginheight="0" marginwidth="0" width="100%" frameborder="0"></iframe>

		<script src="{{ URL::asset('/css/admin/global/plugins/jquery-1.11.0.min.js') }}"></script>
    	<script type="text/javascript">
			$(".selBtn").click(function(){
				$("#selDetails").toggle();
			});
		</script>
</body>
</html>