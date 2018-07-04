<!DOCTYPE html>
<html>
    <head>
        <title>{{Lang::get('static.application_error')}}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style type="text/css">
		* {
			margin:0;
			padding:0;
		}

		.tech-issues {
			text-align:left;
			width:715px;
			margin:40px;
		}

		.tech-issues h2 {
			font:bold 26px Arial, Helvetica, sans-serif;
			color:#1a1a1a;
			margin-bottom:20px;
		}

		.tech-issues p {
			font:normal 14px/18px Arial, Helvetica, sans-serif;
			color:#383838;
			margin-bottom:10px;
		}
		</style>
    </head>
    <body>

        <div class="tech-issues">
            <h2>{{Lang::get('static.oops_technical_issue')}}...</h2>
            <p>{{Lang::get('static.error_page_error_message')}}.</p>
            <p>	{{Lang::get('static.sorry_for_inconvenience')}}!</p>
        </div>

    </body>
</html>
