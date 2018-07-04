<!DOCTYPE html>
<html>
	<head>
    	<!-- BASIC PAGE NEEDS
		================================================== -->
        <meta charset="utf-8" />
		<title>{{{ $header->getMetaTitle() }}}</title>
		<meta name="keywords" content="{{{ $header->getMetaKeyword() }}}" />
		<meta name="description" content="{{{ $header->getMetaDescription() }}}" />
		<meta name="version" content="{{ Config::get('version.version') }}" />
		<meta name="svn" content="{{ Config::get('version.svn') }}" />

		<!-- MOBILE SPECIFIC METAS
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- FAVICONS
		================================================== -->
		<?php $fav_image = CUtil::getSiteLogo('favicon');
			$favicon_url = isset($fav_image['image_url'])?$fav_image['image_url']:URL::asset('/images/header/favicon/favicon.ico');
		?>
		<link rel="shortcut icon" href="{{ $favicon_url }}">

       <!-- BEGIN GLOBAL MANDATORY STYLES
		================================================== -->
        <link href="{{ URL::asset('/css/admin/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"/>
        <style type="text/css">
			html, body {min-height:100%;height:100%;position:relative;}
			.license-header {background-color:#f7f7f7;border-bottom:1px solid #ededed;min-height:40px;padding:15px 0;margin-bottom:20px;text-align:center;}
			.license-header h1 {padding:0;margin:0;}
			.invalid-license {max-width:800px;margin:0 auto;padding:0 10px;}
			.invalid-license .alert {border-radius:0;padding:10px;}
			.footer {position:absolute;bottom:0; text-align:center; background:#f9f9f9;border-top:1px solid #ededed;padding:12px 0;font-size:12px;width:100%;}
		</style>
        <script language="javascript">
			var mes_required = "{{ Lang::get('common.required') }}";
			var bt_site_url = "{{ Url::to('/') }}";
			var page_name = "";
		</script>
	</head>
	<body>
        <div class="clearfix license-header">
            <h1>
                <?php $logo_image = CUtil::getSiteLogo();
                    $image_url = isset($logo_image['image_url'])?$logo_image['image_url']:URL::asset('/images/header/logo/logo.png');
                 ?>
                 <img class="logo-default" src="{{ $image_url }}" alt="{{ Config::get('generalConfig.site_name') }}" />
            </h1>
        </div>
    	<div class="invalid-license">
            @if(isset($err_msg))
                <div class="alert alert-danger">
                    <strong>{{ $err_msg }}</strong>
                </div>
            @endif
            @if(isset($success_msg))
                <div class="alert alert-success">
                    <strong>{{ $success_msg }}</strong>
                </div>
            @endif
        </div>
        <footer class="footer">&copy; {{ Config::get('generalConfig.copyright') }} {{Config::get('generalConfig.site_name')}}. {{ Lang::get('common.all_rights_reserved')}}. Powered By <strong>Agriya</strong></footer>
    </body>
</html>