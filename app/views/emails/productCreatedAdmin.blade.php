@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} Admin,</div>

    <div style="line-height:18px;">
    	<!-- BEGIN: ALERT BLOCK -->
        @if($product_details['product_status'] == 'Ok')
            <p style="background:#dff0d8; border:1px solid #d6e9c6; border-radius:4px; color:#3c763d; font:bold 14px Arial, Helvetica, sans-serif; margin:0 0 20px; padding:10px 15px;">
                {{trans('mail.new_product_posted')}}
            </p>
            <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:bold 14px Arial, Helvetica, sans-serif; margin:0 0 20px; padding:10px 15px;">
                {{trans('mail.user_posting_product')}}
                <a href="{{ $product_details['view_url'] }}" style="color:#31708f; font:bold 13px Arial; text-decoration:none;">"{{{ nl2br($product_details['product_name']) }}}"</a>.
            </p>
        @elseif($product_details['product_status'] == 'ToActivate')
            <p style="background:#dff0d8; border:1px solid #d6e9c6; border-radius:4px; color:#3c763d; font:bold 14px Arial, Helvetica, sans-serif; margin:0 0 20px; padding:10px 15px;">
                {{trans('mail.product_submitted_for_approval')}}
            </p>
            <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:bold 14px Arial, Helvetica, sans-serif; margin:0 0 20px; padding:10px 15px;">
                {{trans('mail.review_product_and_publish')}}
            </p>
        @elseif($product_details['product_status'] == 'NotApproved')
            <p style="background:#f2dede; border:1px solid #ebccd1; border-radius:4px; color:#a94442; font:bold 14px Arial, Helvetica, sans-serif; margin:0 0 20px; padding:10px 15px;">
                {{trans('mail.product_disapproved')}}
            </p>
        @endif
        <!-- END: ALERT BLOCK -->

        <!-- BEGIN: ADMIN SELLER DETAILS -->
        <div style="margin-bottom:35px; line-height:18px; padding:10px 20px; background:#f9f9f9;">
            <p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.seller_details')}}</p>
            <table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.name')}}:</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#1a1a1a;">
						<a style="color:#327cb7; font:normal 13px Arial; text-decoration:none;" href="#">{{ $user_details['display_name'] }}</a></p>
                    </td>
                </tr>

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.email')}}:</p>
                    </td>
                    <td valign="top" align="left"><p style="padding:0;margin:0 0 8px 0; color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $user_details['email'] }}</p></td>
                </tr>
            </table>
        </div>
        <!-- END: ADMIN SELLER DETAILS -->

        <!-- BEGIN: ADMIN PRODUCT DETAILS -->
        <div style="line-height:18px; padding:10px 20px; background:#f9f9f9;">
            <p style="margin:10px 0 20px 0; padding:0 0 5px 0; font:bold 16px Arial, Helvetica, sans-serif; color:#333; border-bottom:1px solid #eee;">{{trans('mail.product_details')}}</p>
            <table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.product_code')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#1a1a1a;">
                            <a style="color:#327cb7; font:normal 13px Arial, Helvetica, sans-serif; text-decoration:none;" href="{{ $product_details['view_url'] }}">
                            {{ $product_details['product_code'] }}</a>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.status')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $product_details['product_status_lang'] }}</p>
                    </td>
                </tr>

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.product_name')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0; color:#1a1a1a;">
                            <a style="color:#327cb7; font:normal 13px Arial; text-decoration:none;" href="{{ $product_details['view_url'] }}">
							{{{ nl2br($product_details['product_name']) }}}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        <!-- END: ADMIN PRODUCT DETAILS -->
    </div>
@stop


