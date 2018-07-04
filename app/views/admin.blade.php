<!DOCTYPE html>
<html>
	<head>
    	<!-- Basic Page Needs
		================================================== -->
        <meta charset="utf-8" />
		<title>{{ $header->getMetaTitle() }}</title>
		<meta name="description" content="{{ $header->getMetaKeyword() }}" />
		<meta name="keywords" content="{{ $header->getMetaDescription() }}" />

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Favicons
		================================================== -->
		<?php $fav_image = CUtil::getSiteLogo('favicon');
			$favicon_url = isset($fav_image['image_url'])?$fav_image['image_url']:URL::asset('/images/header/favicon/favicon.ico');
		?>
		<link rel="shortcut icon" href="{{ $favicon_url }}">

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="{{ URL::asset('/css/admin/global/plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet"/> <!--- version 4.1.0 --->
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/fancybox/source/jquery.fancybox.css') }}" rel="stylesheet"/>
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap-datepicker/css/datepicker3.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap-datetimepicker/css/datetimepicker.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/select2/select2.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/plugins/data-tables/DT_bootstrap.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/pages/css/profile.css') }}" rel="stylesheet"/>
        <!-- END PAGE LEVEL STYLES -->

        <!-- BEGIN THEME STYLES -->
        <link href="{{ URL::asset('/css/admin/global/css/components.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/global/css/plugins.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/layout/css/layout.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/layout/css/themes/darkblue.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/admin/layout/css/custom.css') }}" rel="stylesheet"/>
        <link href="{{ URL::asset('/css/frontend/core/embed_fonts.css') }}" rel="stylesheet"/>
        <!-- END THEME STYLES -->

        <link rel="stylesheet" href="{{ URL::asset('/js/lib/star-rating/star-rating.css') }}" />

        <!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries // HTML5 Shiv Version - 3.7.0 // Respond.js Version - 1.4.2   -->
        <!--[if lt IE 9]>
          <script src="{{ URL::asset('/js/bootstrap/html5shiv.js') }}"></script>
          <script src="{{ URL::asset('/js/bootstrap/respond.min.js') }}"></script>
          <link href="{{ URL::asset('/css/admin/layout/css/ie.css') }}" rel="stylesheet"/>
        <![endif]-->

        <script language="javascript">
			var mes_required = "{{ Lang::get('common.required') }}";
			var page_name = "";
		</script>
	</head>
	<body class="page-header-fixed">
		<div id="selLoading" class="loading" style="display:none;">
            <img src="{{URL::asset('/images/general/bg_opac.png')}}" alt="image" height="100%" width="100%" />
            <div class="loading-cont">
                <img src="{{ URL::asset('/images/general/loader.gif') }}" alt="loading" />
                <p><strong>{{ trans('common.loading') }}</strong></p>
            </div>
        </div>

        <!-- HEADER STARTS -->
    	<header class="page-header navbar navbar-fixed-top">
            <!-- INNER HEADER STARTS -->
            <div class="page-header-inner">
                <!-- LOGO STARTS -->
                <div class="page-logo">
                    <a href="{{ URL::to('/') }}">
                        {{ Config::get('generalConfig.site_name') }}
                    </a>
                    <div class="menu-toggler sidebar-toggler hide">
                        <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                    </div>
                </div>
                <!-- LOGO END -->

                <!-- RESPONSIVE MENU STARTS -->
                <div class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"></div>
                <!-- RESPONSIVE MENU END -->

                <!-- BEGIN: TOP MENU -->
                @include('admin.topMenu')
                <!-- END: TOP MENU -->
            </div>
        </header>

        <div class="clearfix"></div>

        <!-- CONTAINER STARTS -->
        <div class="page-container">

            <!-- SIDEBAR STARTS -->
            @include('admin.sideBarMenu')
            <!-- SIDEBAR END -->

            <!-- MAIN PAGE RIGHT SIDE STARTS -->
            <div class="page-content-wrapper">
                <div class="page-content">
                	<div class="row">
                    	<div class="col-md-12">
                    		<!-- BREADCRUMB STARTS -->
						    <div class="breadcrumbs">
						        <i class="icon-home home-icon bigger-130"></i> {{ Breadcrumbs::render(CUtil::getAdminBreadCrumb()) }}
						    </div>
						    <!-- BREADCRUMB END -->
                        	@yield('content')
                        </div>
                    </div>
                </div>
            </div>

            <!--- FOOTER STARTS --->
            <div id="footer" class="page-footer">
                <div class="page-footer-tools"><span class="go-top"><a href="#top"><i class="fa fa-chevron-up"></i></a></span></div>
                <div class="page-footer-inner">&copy; {{ Config::get('generalConfig.copyright') }} <a href="{{ Request::root() }}"><strong>{{ Config::get('generalConfig.site_name') }}</strong></a> {{ Config::get('version.version') }}. {{ trans("common.all_rights_reserved") }}. {{ Lang::get('common.powered_by')}} <a href="http://www.agriya.com" target="_blank"><strong>Agriya</strong></a></div>
            </div>
            <!--- FOOTER END --->
        </div>

        <script src="{{ URL::asset('/css/admin/global/plugins/jquery-1.11.0.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/jquery-migrate-1.2.1.min.js') }}"></script>

        <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
        <script src="{{ URL::asset('/css/admin/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/jquery.blockui.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/jquery.cokie.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/uniform/jquery.uniform.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap/js/bootstrap-upload.js') }}"></script>
        <!-- END CORE PLUGINS -->

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/select2/select2.min.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}"></script>
        <!-- END PAGE LEVEL PLUGINS -->

        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{ URL::asset('/css/admin/global/scripts/metronic.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/layout/scripts/layout.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/pages/scripts/components-pickers.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/pages/scripts/components-dropdowns.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/pages/scripts/table-advanced.js') }}"></script>
        <script src="{{ URL::asset('/css/admin/pages/scripts/search.js') }}"></script>

        <!-- BEGIN CUSTOM SCRIPTS -->
        <script src="{{ URL::asset('js/admin/bootbox.min.js') }}"></script>
        <script src="{{ URL::asset('js/jquery.validate.min.js') }}"></script>
		<script>
			var invalid_price = '{{ trans("js.invalid_price") }}';
		</script>
		<script src="{{ URL::asset('/css/admin/global/plugins/fancybox/source/jquery.fancybox.pack.js') }}"></script><!-- pop up -->
		<script src="{{ URL::asset('/js/lib/readmore/readmore.min.js') }}"></script>
        <!--<script src="{{ URL::asset('js/jquery.fancybox.pack.js') }}"></script>-->
        <script src="{{ URL::asset('/js/lib/star-rating/star-rating.min.js') }}"></script>
        <script src="{{ URL::asset('js/functions.js') }}"></script>
        <script src="{{ URL::asset('/js/lib/tinymce/tinymce.min.js') }}"></script>
        <!-- END CUSTOM SCRIPTS -->

        <script>
			jQuery(document).ready(function() {
			   Metronic.init(); // init metronic core componets
			   Layout.init(); // init layout
			   //ComponentsPickers.init();
			   ComponentsDropdowns.init();
			});

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
            $(document).ready(function(){
                // Initialize tooltip
                $('[data-toggle="tooltip"]').tooltip({
                    placement : 'top'
                });
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

        @yield("script_content")
	</body>
</html>