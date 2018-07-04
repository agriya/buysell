<table border="0" cellspacing="0" cellpadding="0" style="background:#f0f0f0; border:1px solid #d3d2d2; width:684px; padding:8px; margin:33px;">
	<tr>
		<td>
        	<!-- BEGIN: EMAIL HEADER -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="background:#f9f9f9; padding:0 10px; border-radius:2px 2px 0 0; border-bottom:1px solid #ddd;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" height="69">
                            <tr>
                                <td style="padding:14px 14px 14px 3px; width:140px; height:54px;" align="left" valign="middle">
                                    <a href="{{ url('/') }}" style="border:0; color:#fff; font-size:18px; text-decoration:none;">
                                        <img style="border:0; max-width:140px; max-height:54px;" src="{{ URL::asset('/images/mails/logo.png') }}" alt="logo"/>
                                    </a>
                                </td>

                                <td style="padding:8px 0 8px 8px;" valign="top" align="right">
                                    <table border="0" cellpadding="0" cellspacing="0" style="margin:20px 3px 0 8px;">
                                        <tr>
                                            <td align="left" valign="middle" height="26">
                                                <a href="{{ url('/users/login') }}" style="font:bold 12px 'Arial'; color:#fff; text-decoration:none; padding:6px 12px; background-color:#35aa47;">{{Lang::get('mail.login')}}</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- END: EMAIL HEADER -->

            <!-- BEGIN: EMAIL CONTENT -->
            <table width="100%" cellspacing="0" cellpadding="0" style="background:#fff; padding:30px 30px 20px;">
                <tr>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="font:14px Arial, Helvetica, sans-serif; color:#383838;">
                                	@yield('email_content')
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- END: EMAIL CONTENT -->
		</td>
	</tr>

    <tr>
    	<!-- BEGIN: EMAIL REGARDS -->
    	<td style="background:#fff; padding:15px 30px 30px;">
        	<p style="padding-bottom:5px; margin:0; font:normal 12px Arial, Helvetica, sans-serif; color:#333;">{{Lang::get('mail.regards')}}</p>
            <span style="text-transform:capitalize; margin:0; font:bold 13px Arial, Helvetica, sans-serif; color:#353535;">{{Lang::get('mail.the_site_team', array('site_name' => Config::get('generalConfig.site_name'))) }}</span>
        </td>
        <!-- END: EMAIL REGARDS -->
    </tr>

	<tr>
		<td>
        	<!-- BEGIN: EMAIL FOOTER -->
            <table width="680" border="0" cellspacing="0" cellpadding="0" height="63" style="background:#f9f9f9 ;border-top:1px solid #dad9d9;">
				<tr>
					<td style="color:#383838; text-align:center; font:normal 11px 'Arial'; padding:0 10px;">
						@if (Config::get('generalConfig.support_email') != '')
                            <p style="margin:0; padding:17px 2px 4px 2px;">
                                {{Lang::get('mail.contact_our_customer_care')}}
                                <a href="mailto:{{ Config::get('generalConfig.support_email') }}" style="color:#327cb7; font-weight:bold; text-decoration:none;">
                                {{ Config::get('generalConfig.support_email') }}</a>
                            </p>
						@endif

						@if (Config::get('mail.from_email') != '')
                            <p style="margin:0; padding:2px 2px 10px;">
                                {{Lang::get('mail.be_sure_to_add')}}
                                <a href="mailto:{{ Config::get('mail.from_email') }}" style="color:#327cb7; font-weight:bold; text-decoration:none;">{{ Config::get('mail.from_email') }}</a>
                                {{Lang::get('mail.to_your_addr_book')}}
                            </p>
						@endif

						<p style="margin:0; padding:2px 2px 10px;">
							&copy;{{date('Y')}}
							<a href="{{ URL::to('/') }}" style="color:#383838; font-weight:bold; text-decoration:none;">{{ Config::get('generalConfig.site_name') }}.</a>
							{{ Lang::get('common.all_rights_reserved')}}.
						</p>

						@if (Config::get('generalConfig.facebook_link') != '' || Config::get('generalConfig.twitter_link') != '')
                            <p>
                                <span>{{ Lang::get('common.follow_us_on')}}</span>
                                @if (Config::get('generalConfig.twitter_link') != '')
                                    <a style="margin:0 7px; text-decoration:none; width:16px; height:16px;" href="{{ Config::get('generalConfig.twitter_link') }}" target="_blank">
                                        <img style="border:0" width="16" height="16" src="{{ URL::asset('/images/mails/twitter.png') }}" alt="twitter" />
                                    </a>
                                @endif
                                @if (Config::get('generalConfig.facebook_link') != '')
                                    <a style="text-decoration:none; width:16px; height:16px; text-decoration:none;" href="{{ Config::get('generalConfig.facebook_link') }}" target="_blank">
                                        <img style="border:0" width="16" height="16" src="{{ URL::asset('/images/mails/facebook.png') }}" alt="Facebook" />
                                    </a>
                                @endif
                            </p>
						@endif
					</td>
				</tr>
			</table>
            <!-- END: EMAIL FOOTER -->
		</td>
	</tr>
</table>