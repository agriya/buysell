<!-- BEGIN: INFO BLOCK -->
@if(Session::has('error_message') && Session::get('error_message') != '')
	<div class="note note-danger">{{ Session::get('error_message') }}</div>
	<?php Session::forget('error_message'); ?>
@endif

@if(Session::has('success_message') && Session::get('success_message') != '')
	<div class="note note-success">{{ Session::get('success_message') }}</div>
	<?php Session::forget('success_message'); ?>
@endif
<!-- END: INFO BLOCK -->

<h1>{{ Lang::get('favorite.user_favorites', array('user_name' => $user_details['display_name'])) }}</h1>
<div class="tabbable-custom portlet">
	<div class="customview-navtab mobviewmenu-480">
		<button class="btn bg-blue-steel btn-sm"><i class="fa fa-chevron-down"></i> View Menu</button>
		<ul role="tablist" class="nav nav-tabs margin-bottom-30" id="myTab">
            <?php
                $sort_by_arr = CUtil::populateFavoritesHeaderArray();
                $orderby_field = 'id';
                if (Input::has('favorites'))
                    $favorites = Input::get('favorites');
            ?>
            @foreach($sort_by_arr as $sortKey => $sort)
            	<li @if($favorites == $sort['innervalue']) class="active" @endif><a href="{{ $sort['href'] }}">{{ $sort['innertext'] }}</a></li>
            @endforeach
        </ul>
    </div>

	<!-- BEGIN: PRODUCTS LISTS -->
    @if($favorites == 'product')
    	@include('viewFavoriteProductsList')
    @endif
    <!-- END: PRODUCTS LISTS -->

   	<!-- BEGIN: SHOP LISTS -->
    @if($favorites == 'shop')
    	@include('viewFavoriteShops')
    @endif
    <!-- END: SHOP LISTS -->

    <!-- BEGIN: COLLECTION LISTS -->
    @if($favorites == 'collection')
    	@include('viewFavoriteCollections')
    @endif
    <!-- END: COLLECTION LISTS -->
</div>
