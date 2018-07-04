<!DOCTYPE html>
<html>
    <head>
    	<!-- Basic Page Needs
		================================================== -->
        <meta charset="utf-8" />
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

		<!-- BEGIN THEME STYLES
		================================================== -->
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap-datepicker/css/datepicker3.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/css/components.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/layout/css/style.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/layout/css/style-responsive.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/core/embed_fonts.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/core/custom.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/core/mobile.css') }}" rel="stylesheet"/>
        <!-- Theme Styles Ends -->

        <!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries // HTML5 Shiv Version - 3.7.0 // Respond.js Version - 1.4.2   -->
        <!--[if lt IE 9]>
          <script src="{{ URL::asset('/js/bootstrap/html5shiv.js') }}"></script>
          <script src="{{ URL::asset('/js/bootstrap/respond.min.js') }}"></script>
          <link rel="stylesheet" href="{{ URL::asset('/css/frontend/core/ie.css') }}">
        <![endif]-->

        <!-- JS
		================================================== -->
    	<script src="{{ URL::asset('/css/admin/global/plugins/jquery-1.11.0.min.js') }}"></script>
    	<script src="{{ URL::asset('/css/admin/global/plugins/jquery-migrate-1.2.1.min.js') }}"></script>

        <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
        <script src="{{ URL::asset('/css/admin/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        @if(!Request::is('admin/deals/approve-featured-request/*'))
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
        @endif
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
        <!-- END CORE PLUGINS -->

        <script src="{{ URL::asset('/js/jquery.validate.min.js') }}"></script>
        <script src="{{ URL::asset('/js/lib/readmore/readmore.min.js') }}"></script>
        <script src="{{ URL::asset('/js/functions.js') }}"></script>
        <script src="{{ URL::asset('/js/jquery.fancybox.pack.js') }}"></script>

        <script language="javascript">
			var mes_required = "{{ Lang::get('common.required') }}";
			var bt_site_url = "{{ Url::to('/') }}";
			var page_name = "";

			function setCookie(cookieName, cookieValue, nDays) {
			    var today = new Date();
			    var expire = new Date();
				if (nDays==null || nDays==0)
			        nDays=1;
			    expire.setTime(today.getTime() + 3600000*24*nDays);
			    document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString()+"; path=/";
			}
		</script>

        @yield('includescripts')

        <script>
			var invalid_price = '{{ trans("js.invalid_price") }}';
			var currency_code = 'INR';
		</script>
        <!-- END PAGE LEVEL JAVASCRIPTS -->
    </head>
    <body class="popup-container">
        <section>@yield('content')</section>
    </body>
</html>