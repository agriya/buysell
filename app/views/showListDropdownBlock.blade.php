<!-- BEGIN: LIST DROPDOWN  -->
<div class="add-list">
    <p><a href="{{ Url::to('favorite/'.$user_code.'?favorites=product') }}">{{ Lang::get('common.your_lists')}}</a></p>
    @if(count($d_arr['list_details']) > 0)
    <div class="added-favlist">
    	@foreach($d_arr['list_details'] as $key => $list)
        <label class="checkbox">
        	<?php
				$fav_details = $prod_fav_service->getSingleProductFavoriteDetailsByList($product_id, $user_id, $list['list_id']);
				$checked = (count($fav_details) > 0) ? 'checked' : '';
			?>

            <input name="list_name" data-listid="{{$list['list_id']}}" {{ $checked }} type="checkbox" />
            {{$list['list_name']}}
        </label>
        @endforeach
    </div>
    @endif
    <div class="addlist-form clearfix">
        <input type="text" name="list_name" id="list_name_{{$product_id}}" data-productid="{{$product_id}}" maxlength="{{ config::get('generalConfig.favorite_list_name_char_limit')}}"  class="form-control list_name"  />
        <button type="button" class="btn blue clsAddList">{{ Lang::get('common.add')}}</button>
    </div>
</div>
<!-- END: LIST DROPDOWN -->
<script language="javascript" type="text/javascript">
	var add_product_to_list_url = "{{URL::action('FavoritesController@postAddFavoriteProductToList')}}";
	var add_list_url = "{{URL::action('FavoritesController@postAddFavoriteListAndFavProduct')}}";
	var user_id = "{{ $user_id }}";
	var product_id = "{{ $product_id }}";
	var block = "{{ $block }}";

	$(document).ready(function() {

		$('.clsAddList').click(function(){
			if(product_id > 0) {
				var list_name = $.trim($('#list_name_'+product_id).val());
				if(list_name == ''){
					bootbox.alert("{{ Lang::get('common.please_enter_fav_name') }}");
					return false;
				}
				postData = 'user_id=' + user_id + '&product_id=' + product_id + '&list_name=' + urlencode(list_name),
				displayLoadingImage(true);
				$.post(add_list_url, postData,  function(response)
				{
					hideLoadingImage (false);

					data = eval( '(' +  response + ')');
					if(data.result == 'success')
					{
						if ($('#'+block+'_'+product_id).length > 0) {
                        	$('#'+block+'_'+product_id).click();
                        }
						else {
							$("#fav_list_"+product_id).click();
						}
						//showSuccessDialog({status: 'success', success_message: data.success_msg});
					}
					else
					{
						showErrorDialog({status: 'error', error_message: data.error_msg});
					}
				}).error(function() {
					hideLoadingImage (false);
					showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
				});
			}
		});

		$('input[name=list_name]').click(function(){
			$this = $(this);
			var list_id = $this.data('listid');
			if($(this).data('listid') > 0)
			{

				postData = 'user_id=' + user_id + '&product_id=' + product_id + '&list_id=' + list_id,
				displayLoadingImage(true);
				$.post(add_product_to_list_url, postData,  function(response)
				{
					hideLoadingImage (false);

					data = eval( '(' +  response + ')');
					if(data.result == 'success')
					{
						/*removeErrorDialog();
		                var act = data.action_to_show;
		                var text_to_disp = '';
		                var favorite_text_msg = '';
		                if(act == "remove")
		                {
		                    text_to_disp = '<i class="fa fa-heart text-pink"></i>';
		                }else
		                {
		                    text_to_disp = '<i class="fa fa-heart text-muted"></i>';
		                }
		                $this.html(text_to_disp);*/
						showSuccessDialog({status: 'success', success_message: data.success_msg});
					}
					else
					{
						showErrorDialog({status: 'error', error_message: data.error_msg});
					}
				}).error(function() {
					hideLoadingImage (false);
					showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
				});
			}
		});

	});

	function urlencode( str ) {
	    /* // http://kevin.vanzonneveld.net
	    // +   original by: Philip Peterson
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +      input by: AJ
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // %          note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
	    // *     example 1: urlencode('Kevin van Zonneveld!');
	    // *     returns 1: 'Kevin+van+Zonneveld%21'
	    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
	    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
	    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
	    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'*/

	    var histogram = {}, histogram_r = {}, code = 0, tmp_arr = [];
	    var ret = str.toString();

	    var replacer = function(search, replace, str) {
	        var tmp_arr = [];
	        tmp_arr = str.split(search);
	        return tmp_arr.join(replace);
	    };

	    /* The histogram is identical to the one in urldecode.*/
	    histogram['!']   = '%21';
	    histogram['%20'] = '+';

	    /* Begin with encodeURIComponent, which most resembles PHP's encoding functions*/
	    ret = encodeURIComponent(ret);

	    for (search in histogram) {
	        replace = histogram[search];
	        ret = replacer(search, replace, ret); /* Custom replace. No regexing */
	    }

	    /* Uppercase for full PHP compatibility */
	    return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
	        return "%"+m2.toUpperCase();
	    });

	    return ret;
	};
</script>