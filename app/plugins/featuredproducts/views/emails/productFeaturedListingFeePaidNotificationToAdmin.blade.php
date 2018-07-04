@extends('mail')
@section('email_content')
    <div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('deals::deals.hi') }} Admin,</div>

    <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.listing_fee_has_been_paid_for_set_as_featured_product') }}. </p>

    <!-- BEGIN: ADMIN CANCELLATION DETAILS -->
    <div style="line-height:18px;">
        <div style="background:#fafafa; border:1px solid #e6e6e6; border-radius:4px; line-height:18px; padding:10px 20px;">
            <p style="margin:5px 0 18px 0; padding:0; font:bold 15px Arial, Helvetica, sans-serif; color:#333;">{{ Lang::get('deals::deals.details_of_the_product') }} :</p>

            <table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.product_code') }} :</p>
                    </td>
                    <td valign="top" align="left"><a href="{{ $product_view_url }}" style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $product_code }}</a></td>
                </tr>

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.by') }} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p >
                            <a href="{{ $product_added_by['profile_url'] }}" title="{{ $product_added_by['display_name'] }}" style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">{{ $product_added_by['display_name'] }}</a>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td width="100" valign="top" align="left">
                        <p style="padding:0; margin:0 0 8px 0; color:#707070; font:normal 13px Arial, Helvetica, sans-serif;">{{ Lang::get('deals::deals.added_on') }} :</p>
                    </td>
                    <td valign="top" align="left">
                        <p style="padding:0;margin:0 0 8px 0;color:#1a1a1a; font:normal 13px Arial, Helvetica, sans-serif;">
                        	{{ $transaction_date }}
                        </p>
                    </td>
                </tr>
            </table>

            <p style="margin:5px 0 18px 0; padding:0; font:bold 15px Arial, Helvetica, sans-serif; color:#333;">For more details about this product&nbsp;<a style="font:bold 12px 'trebuchet MS'; color:#15aadb; text-decoration:none;" href="{{ $product_view_url }}" title="view product">View Product</a></p>
        </div>
    </div>
    <!-- END: ADMIN CANCELLATION DETAILS -->
@stop
