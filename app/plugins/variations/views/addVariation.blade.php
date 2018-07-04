@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<a href="{{ URL::to('variations') }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ Lang::get('variations::variations.manage_variations') }}
				</a>

				@if($action=='add')
				   <h1>{{ Lang::get('variations::variations.add_variation') }}</h1>
				@else
					<h1>{{ Lang::get('variations::variations.update_variation') }}</h1>
				@endif
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: INCLUDE NOTIFICATIONS -->
			@include('notifications')
			<!-- END: INCLUDE NOTIFICATIONS -->

			<!-- BEGIN: ADD VARIATIONS -->
			<div class="well">
				{{ Form::model($variation_details, ['method' => 'post','class' => 'form-horizontal', 'id' => 'add_variations_form']) }}
					<div id="selSrchProducts">
						<fieldset>
							<div class="form-group">
								{{ Form::label('name', Lang::get('variations::variations.name'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('name', null, array('class' => 'form-control valid', 'id' => 'name')) }}
									<label class="error">{{{ $errors->first('name') }}}</label>
								</div>
							</div>
                            
							<div class="form-group">
								{{ Form::label('help_text', Lang::get('variations::variations.help_tip'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-5">
									{{ Form::textarea('help_text', null, array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('help_text') }}}</label>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12 margin-top-10 margin-bottom-10">
									<strong>{{ Lang::get('variations::variations.options_for_the_variation') }}</strong>
								</div>
							</div>
                            
							<p class="note note-info">{{ Lang::get('variations::variations.variation_option_key_note_msg') }}</p>

							<div class="form-group">
								<label class="col-md-2 control-label required-icon">{{ Lang::get('variations::variations.options') }}</label>
								<div id="variation_options_group" class="col-md-8 option-var">
						            @include('variations::variationOptionsBlock', array('attributes_arr' => $attributes_arr))
						        </div>
							</div>

							<div class="form-group">
								<div class="col-md-offset-2 col-md-10">
									{{ Form::hidden('logged_user_id', $logged_user_id) }}
									{{ Form::hidden('action', $action) }}
									{{ Form::hidden('variation_id', $variation_id) }}
                                    @if($action=='add')
                                        <button type="submit" class="btn green">
                                            <i class="fa fa-plus"></i> {{Lang::get('variations::variations.add')}}
                                        </button>
                                    @else
                                        <button type="submit" class="btn blue-madison">
                                            <i class="fa fa-undo"></i> {{Lang::get('variations::variations.update')}}
                                        </button>
                                    @endif
                                    
									<button type="reset" name="addvariation_reset" value="addvariation_reset" class="btn default" onclick="javascript:location.href='{{ URL::to('variations/add-variation') }}'"><i class="fa fa-times"></i> {{ Lang::get('variations::variations.reset') }}</button>
								</div>
							</div>
						</fieldset>
					</div>
				{{ Form::close() }}
			</div>
			<!-- END: ADD VARIATIONS -->
		</div>
	</div>

	<script type="text/javascript">
		var mes_required = '{{ Config::get('common.required') }}';
		var page_name 	= "add_variation";
		var action		= '{{ $action }}';
	</script>
@stop