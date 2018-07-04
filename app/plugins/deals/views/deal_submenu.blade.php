<!-- BEGIN: DEALS SUB MNEU -->
<div class="clearfix tabbable-custom deals-menu">
	<div class="customview-navtab mobviewmenu-480 margin-top-10">
		<button class="btn bg-blue-steel btn-sm"><i class="fa fa-chevron-down"></i>View Deals Menu</button>
		<ul class="nav nav-tabs margin-bottom-20">
			<li {{ ((Request::is('deals/list/new')) ? 'class="active"' : '') }}>
				<a href="{{ URL::to('deals/list', array('list_type' => 'new')) }}">{{ Lang::get('deals::deals.new_deals_menu') }}</a>
			</li>            
			<li {{ ((Request::is('deals/list/expiring')) ? 'class="active"' : '') }}>
				<a href="{{ URL::to('deals/list', array('list_type' => 'expiring')) }}">{{ Lang::get('deals::deals.expiring_deals_menu') }}</a>
			</li>
			<li {{ ((Request::is('deals/list/expired')) ? 'class="active"' : '') }}>
				<a href="{{ URL::to('deals/list', array('list_type' => 'expired')) }}">{{ Lang::get('deals::deals.past_deals_menu') }}</a>
			</li>
			@if(CUtil::isMember())
					<li role="presentation" {{ ((Request::is('deals/my-deals') || Request::is('deals/my-featured-request') || Request::is('deals/set-featured/*') || Request::is('deals/view-deal/*') || Request::is('deals/add-deal') || Request::is('deals/update-deal/*')) ? 'class="active"' : 'dropdown') }}>
					<a class="dropdown-toggle" data-toggle="dropdown" href="{{ URL::to('deals/my-deals') }}" role="button" aria-expanded="false">
						{{ Lang::get('deals::deals.my_deals_menu') }} <span class="caret"></span>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{ URL::to('deals/my-deals') }}">{{ Lang::get('deals::deals.my_deals_menu') }}</a></li>
						<li><a href="{{ URL::to('deals/my-featured-request') }}">{{ Lang::get('deals::deals.my_featured_request_menu') }}</a></li>
						<li><a href="{{ URL::to('deals/add-deal') }}">{{ Lang::get('deals::deals.add_deal_link_lbl') }}</a></li>
					</ul>
				</li>
			@else
				<li><a href="{{ URL::to('deals/add-deal') }}">{{ Lang::get('deals::deals.add_deal_link_lbl') }}</a></li>
			@endif
		</ul>            
	</div>
</div>
<!-- END: DEALS SUB MNEU -->