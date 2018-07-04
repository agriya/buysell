<!DOCTYPE html>
<html>
    <head>
        <title>{{Lang::get('static.not_found')}}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <?php $fav_image = CUtil::getSiteLogo('favicon');
			$favicon_url= isset($fav_image['image_url'])?$fav_image['image_url']:URL::asset('/images/header/favicon/favicon.ico');
		?>
		<link rel="shortcut icon" href="{{ $favicon_url }}">
		<link href="{{ URL::asset('/css/frontend/core/custom.css') }}" rel="stylesheet"/>
    </head>
    <body class="issues-404">
        <p><img src="{{URL::asset('/images/general/404page.png')}}"  alt="{{ Config::get('site.site_name') }} 404"></p>
		<h2><span>{{Lang::get('common.error')}}:</span> {{Lang::get('static.sorry_page_could_not_found')}}.</h2>
        <p>{{Lang::get('static.page_not_found_error_message')}}</p>
        <p><strong>{{Lang::get('static.you_may_try_one')}}</strong> {{Lang::get('static.return_to_our')}} <a href="{{ url('/') }}">{{Lang::get('static.home_page')}}</a> {{Lang::get('static.to_get_right_direction')}}. </p>
    </body>
</html>
