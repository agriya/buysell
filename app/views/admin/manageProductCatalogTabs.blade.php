@if(!$ajax_page)
    <div id="category_details"></div>

    <!--- TABS STARTS --->
	<div class="mt30 tabbable-custom tabbable-customnew">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#category_info_block" data-toggle="tab">{{ trans('admin/manageCategory.category_info_title') }}</a></li>
			<li><a href="#attributes_block" data-toggle="tab">{{ trans('admin/manageCategory.attributes_title') }}</a></li>
		</ul>
		<div class="tab-content">
			<div id="category_info_block" class="tab-pane active"></div>
			<div id="attributes_block" class="tab-pane"></div>
		</div>
	</div>
    <!--- TABS END --->
@else
	<!--- TITLE AND INFO STARTS --->
	<div class="note note-info">
        @if($category_id == $root_category_id)
            <h4 class="mar0">{{ trans('admin/manageCategory.new_category_title') }} </h4>
        @elseif($display_block == 'add_sub_category')
            <h4 class="mar0">{{ trans('admin/manageCategory.sub_category_title') }} {{ trans('common.for') }} {{ $category_details['full_parent_category_name']}}</h4>
        @else
            <a href="javascript:void(0);" onclick="addSubCategory({{$category_details['id']}});"; title="{{ trans('admin/manageCategory.sub_category_title') }}" class="btn blue btn-xs pull-right"><i class="fa fa-plus"></i> {{ trans('admin/manageCategory.sub_category_title') }}</a>
            <h4 class="mar0">{{ $category_details['category_name'] }}</h4>
        @endif
    </div>
    <!--- TITLE AND INFO END --->
@endif