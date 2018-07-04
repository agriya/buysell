@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.hi')}} {{ $display_name }},</div>
    
    <!-- BEGIN: ALERT BLOCK -->
    <div style="line-height:18px;">
        @if($product_status == 'Ok')
            <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; color:#31708f; font:normal 14px Arial, Helvetica, sans-serif; margin:0 0 20px 0; padding:10px 15px;">
                {{trans('mail.thank_for_post_product')}}
                <a href="{{ $view_url }}" style="color:#31708f; font:bold 13px Arial; text-decoration:none;">"{{{ nl2br($product_name) }}}"</a>
            </p>
        @elseif($product_status == 'ToActivate')
            <p style="background:#dff0d8; border:1px solid #d6e9c6; border-radius:4px; font:normal 14px Arial, Helvetica, sans-serif; color:#3c763d; margin:0 0 20px 0; padding:10px 15px;">
                {{trans('mail.thank_for_post_product')}}
                <a href="{{ $view_url }}" style="color:#3c763d; font:bold 13px Arial; text-decoration:none;">"{{{ nl2br($product_name) }}}"</a>.
            </p>
            
            <p style="background:#d9edf7; border:1px solid #bce8f1; border-radius:4px; font:normal 14px Arial, Helvetica, sans-serif; color:#31708f; margin:0 0 20px 0; padding:10px 15px;">
                {{trans('mail.we_will_review_notify')}}
            </p>
        @elseif($product_status == 'NotApproved')
            <p style="background:#f2dede; border:1px solid #ebccd1; border-radius:4px; font:normal 14px Arial, Helvetica, sans-serif; color:#a94442; margin:0 0 20px 0; padding:10px 15px;">
                {{trans('mail.your_product_disapproved')}}
            </p>
        @endif
        <!-- END: ALERT BLOCK -->
        
        <!-- BEGIN: PRODUCT DETAILS -->
        <div style="line-height:18px; padding:10px 20px; background:#f9f9f9; border:1px solid #eaeaea; border-radius:4px;">
            <p style="margin:10px 0 18px 0; padding:0; font:bold 15px Arial, Helvetica, sans-serif; color:#333;">{{trans('mail.your_product_disapproved')}}</p>
            <table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.product_name')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0; color:#1a1a1a;"><a style="color:#327cb7; font:normal 13px Arial, Helvetica, sans-serif; text-decoration:none;" href="{{ $view_url }}">{{{ $product_name }}}</a></p>
                    </td>
                </tr>
                
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.product_code')}} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0; color:#1a1a1a;"><a style="color:#327cb7; font:normal 13px Arial, Helvetica, sans-serif; text-decoration:none;" href="{{ $view_url }}">{{ $product_code }}</a></p></td>
                </tr>
                
                @if($user_notes != '')
                    <tr>
                        <td width="100" valign="top" align="left">
                            <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{trans('mail.notes')}} :</p>
                        </td>
                        <td valign="top" align="left">
                            <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{  nl2br(htmlspecialchars($user_notes)) }}</p>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
        <!-- END: PRODUCT DETAILS -->
    </div>
@stop
