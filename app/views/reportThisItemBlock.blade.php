<!-- BEGIN: REPORT ITEMS FORM -->
<div class="modal fade" id="reportItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="margin-top-10 pull-right">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">{{trans('common.close')}}</span></button>
				</div>
				<!--<h1 id="myModalLabel" class="margin-0">{{Config::get('generalConfig.site_name')}}</h1>-->
				<div id="selSharehedding"><h1 class="margin-0">{{Lang::get('viewProduct.report_a_listing')}}</h1></div>
			</div>

			<div class="modal-body">
				<div id="report_message_div"></div>
				{{ Form::open(array('action' => array('ProductController@postReportItem'), 'class' => 'form-horizontal margin-top-10', 'method' => 'post', 'autocomplete' => 'off', 'id' => 'frmReportItem', 'name' => 'frmReportItem' )) }}
					<p class="note note-success">{{Lang::get('viewProduct.thank_you_for_help')}} {{Config::get('generalConfig.site_name')}}!</p>
                    <h3 class="title-one margin-bottom-10">{{Lang::get('viewProduct.why_reporting_listing')}} <span class="report-mand">*</span></h3>
                    <?php
                    	$DifferThanAd = $IncorrectData = $Prohibited = $InaccurateCategory = false;
						if(isset($product_report) && count($product_report) > 0) {
							$report_thread_arr = $product_report->report_thread;
							$report_thread = explode(',', $report_thread_arr);
							$report_thread_new_arr = array();
							foreach($report_thread as $key => $val) {
								$report_thread_new_arr[$val] = $val;
							}
							if(isset($report_thread_new_arr['DifferThanAd'])) {
								$DifferThanAd = true;
							}
							if(isset($report_thread_new_arr['IncorrectData'])) {
								$IncorrectData = true;
							}
							if(isset($report_thread_new_arr['Prohibited'])) {
								$Prohibited = true;
							}
							if(isset($report_thread_new_arr['InaccurateCategory'])) {
								$InaccurateCategory = true;
							}
						}
					?>
					<p class="margin-0">
						<label class="checkbox-inline">
							{{Form::checkbox('report_thread[]','DifferThanAd', $DifferThanAd, array('class'=>'js-report-checkbox checkboxes', 'id' => 'report_thread_1'))}}
							<label for="report_thread_1">{{Lang::get('viewProduct.DifferThanAd')}}</label>
						</label>
					</p>
					<p class="margin-0">
						<label class="checkbox-inline">
							{{Form::checkbox('report_thread[]','IncorrectData', $IncorrectData, array('class'=>'js-report-checkbox checkboxes', 'id' => 'report_thread_2'))}}
							<label for="report_thread_2">{{Lang::get('viewProduct.IncorrectData')}}</label>
						</label>
					</p>
					<p class="margin-0">
						<label class="checkbox-inline">
							{{Form::checkbox('report_thread[]','Prohibited', $Prohibited, array('class'=>'js-report-checkbox checkboxes', 'id' => 'report_thread_3'))}}
							<label for="report_thread_3">{{Lang::get('viewProduct.Prohibited')}}</label>
						</label>
					</p>
					<p class="margin-0">
						<label class="checkbox-inline">
							{{Form::checkbox('report_thread[]','InaccurateCategory', $InaccurateCategory, array('class'=>'js-report-checkbox checkboxes', 'id' => 'report_thread_4'))}}
							<label for="report_thread_4">{{Lang::get('viewProduct.InaccurateCategory')}}</label>
						</label>
					</p>
					<?php
						$message = '';
						if(isset($product_report) && count($product_report) > 0) {
							if($product_report->custom_message) {
							$message = $product_report->custom_message;
							}
						}
					?>
					<h3 class="title-one margin-top-20">{{Lang::get('viewProduct.your_message')}}</h3>
					<p>{{Form::textarea('custom_message', $message, array('class' => 'form-control input-sm', 'id' => 'custom_message', 'rows'=>'7', 'cols' => '50'))}}</p>
					{{Form::hidden('product_id',$p_details['id'])}}
				{{Form::close()}}
			</div>

			<div class="modal-footer">
				<button type="button" class="btn green js-submit-report"><i class="fa fa-check"></i> {{Lang::get('common.save_changes')}}</button>
				<button type="button" class="btn red" data-dismiss="modal"><i class="fa fa-times"></i> {{Lang::get('common.close')}}</button>
			</div>
		</div>
	</div>
</div>
<!-- END: REPORT ITEMS FORM -->