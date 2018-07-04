<?php
/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
class SudoPay_Canvas
{
    private $sa;
    private $btn_ids = array();
    private $_tab_id = 0;
    public function __construct(SudoPay_API $sudopay_api_obj)
    {
        $this->sa = $sudopay_api_obj;
        $this->sudopay_service = new \SudopayService();
        $this->logged_user_id = \BasicCUtil::getLoggedUserId();
    }
    public function displayForm($label, $fields_arr, $is_add_signature = true)
    {
        if ($is_add_signature) { // sign the fields?
            $fields_arr['signature'] = SudoPay_Utils::getSignature($this->sa->secret, $fields_arr);
        }
        echo '<form action="' . $this->sa->payment_url . '" method="post">' . "\n";
        foreach($fields_arr as $key => $val) {
            echo '<input type="hidden" name="' . $key . '" value="' . $val . '"/>' . "\n";
        }
        echo '<input name="submit" class="btn btn-large" type="submit" value="' . $label . '">';
        echo '</form>' . "\n";
    }
    public function displayJSCode()
    {
        $btn_ids_str = '\'' . implode('\',\'', $this->btn_ids) . '\'';
        $btn_url = '\'' . $this->sa->button_url . '\'';
        $script = <<<EOT
<script>
	(function(s,u,d,o,p,a,y,b,t,n) {
	s['sudopay_btn_ids'] = p;
	a = u.createElement(d), y = u.getElementsByTagName(d)[0];
	a.async = 1;
	a.src = o;
	y.parentNode.insertBefore(a, y);
	})(window, document, 'script', $btn_url, [$btn_ids_str]);
</script>
EOT;
        echo $script;
    }
    public function displayJSBtn($fields_arr, $is_add_signature = true, $id = "sudopaybtn", $is_include_script = true)
    {
        if ($is_add_signature) { // sign the fields?
            $fields_arr['signature'] = SudoPay_Utils::getSignature($this->sa->secret, $fields_arr);
        }
        $data_param = '';
        // values have to be properly urlencoded...
        foreach($fields_arr as $key => $val) {
            $data_param.= $key . '=' . urlencode($val) . '&';
        }
        $data_param = substr($data_param, 0, -1); // remove final &
        echo '<button id="' . $id . '" class="sudopaybtn" type="button" data-param="' . $data_param . '"> Pay $' . $fields_arr['amount'] . '</button>' . "\n";
        $this->btn_ids[] = $id; // buffer the id to be used in displayJSCode()
        if ($is_include_script) {
            $this->displayJSCode();
        }
    }
    // TODO: fix non-existing error code case
    private function _showError($error_arr)
    {
        echo '<div class="clearfix alert alert-danger text-center">
        			<p>Code: ' . $error_arr['code'] . '</p>
                    <p><strong>Message: ' . $error_arr['message'] . '</strong></p>
        	  </div>';
    }

    public function checkGateways($action = '', $get_buyer_details = false, $is_credit = 'No')
    {
    	$wallet_id = Input::get('wallet_id');
        $gateways_arr = $this->sa->callGetGateways(array(
            'supported_actions' => $action
        ));
        \Log::info('Gate way start =======>');
        //\Log::info(print_r($gateways_arr));
        \Log::info('Gate way end =======>');
        if (!empty($gateways_arr) && empty($gateways_arr['error']) && !empty($gateways_arr['gateways'])) {
			return $gateways_arr;
        } else {
            return false;
        }
	}

    public function displayGatewaysNew($action = '', $get_buyer_details = false, $is_credit = 'No', $gateways_arr)
    {
    	$wallet_id = Input::get('wallet_id');
        if (!empty($gateways_arr) && empty($gateways_arr['error']) && !empty($gateways_arr['gateways'])) {
            $this->_tab_id++;
            $active_tab = 0;
            echo '<form method="post" class="js-payment wallet-tab"><div class="fonts14 top-space wallet-tab"><div class="bs-example bs-example-tabs"><ul class="nav nav-tabs col-md-2">';
            //Display parent gateways in tab
            foreach($gateways_arr['gateways'] as $par_gateway) {
            	if(count($par_gateway['gateways']) > 0) {
	                $active_tab++;
	                $li_tab = '<li title="' . $par_gateway['display_name'] . '" class="text-center';
	                if ($active_tab == 1) {
	                    $li_tab.= ' active';
	                }
	                $li_tab.= '" onclick="hideCreditCard('.$par_gateway['id'].')" >';
	                echo $li_tab . '<a data-toggle="tab" href="#gateways-' . $par_gateway['id'] . '-' . $this->_tab_id . '">
						<!-- <span class="show">
							<img data-toggle="tooltip" src="' . $par_gateway['thumb_url'] . '" alt="' . $par_gateway['display_name'] . '">
						</span> -->
						<span>' . $par_gateway['display_name'] . '</span>
						</a>
					</li>';
				}
            }
            //Display tab content
            echo '</ul>
           <div id="myTabContent' . $this->_tab_id . '" class="tab-content margin-bottom-20 col-md-10 wid-per-70">';
            $i = 0;
			$gateway_form_tpls_arr = array();
            foreach($gateways_arr['gateways'] as $parent_gateway) {
            	if(count($parent_gateway['gateways']) > 0) {
                $i++;
                echo '<div class="tab-pane fade fnGatwayFinder ';
                if ($i == 1) {
                    echo 'active ';
                }
                echo 'in" id="gateways-' . $parent_gateway['id'] . '-' . $this->_tab_id . '">';
                //Display radio button and child gateways

					foreach($parent_gateway['gateways'] as $gateway) {
	                	$buyer_fees_formula = (isset($gateway['buyer_fees_formula'])) ? $gateway['buyer_fees_formula'] : '';
	                	$buyer_fee_conf_tkn = (isset($gateway['buyer_fees_payer_confirmation_token'])) ? $gateway['buyer_fees_payer_confirmation_token']:'';
						if ($i == 1) {
	                        $gateway_form_tpls_arr = $gateway['_form_fields']['_extends_tpl'];
	                    }
	                    $thumb_url = ($parent_gateway['id'] == 4922)? $parent_gateway['thumb_url']:$gateway['thumb_url'];
	                    echo '<div id="gateway_' . $gateway['id'] . '" class="span2">';
	                    $input = '<input type="radio" class="js-gateway margin-right-5" data-gateway-name="'.$gateway['display_name'].'" data-form-tpl="' . implode(',', $gateway['_form_fields']['_extends_tpl']) . '" name="gateway_id_' .$parent_gateway['id']. '" id="sudopay_gateway_' . $parent_gateway['id'] . '_' . $gateway['id'] . '" value="' . $gateway['id'] . '" onclick="calcBuyerFeesFormula()"';
	                    $input.= ' checked="checked"';
	                    $input.= '/>';
	                    echo $input;
	                    $buyer_fourmula_hdn = '<input type="hidden" name="buyer_fees_formula" id="buyer_fees_formula_'.$parent_gateway['id'].'_'.$gateway['id'].'" value="'.$buyer_fees_formula.'">';
	                    $buyer_fee_tkn_hdn 	= '<input type="hidden" name="buyer_fees_payer_confirmation_token" id="buyer_fees_payer_confirmation_token_'.$parent_gateway['id'].'_'.$gateway['id'].'" value="'.$buyer_fee_conf_tkn.'">';
	                    echo $buyer_fourmula_hdn;
	                    echo $buyer_fee_tkn_hdn;
	                    echo '<label for="sudopay_gateway_' . $gateway['id'] . '_' . $this->_tab_id . '">
	                            <span class="text-center">
	                                <span class="show">
	                                    <img alt="' . $gateway['display_name'] . '" data-toggle="tooltip" title="' . $gateway['display_name'] . '" src="' .$thumb_url  . '"/>
	                                </span>
	                                <span>
	                                    <small>' . $gateway['display_name'] . '</small>
	                                </span>
	                            </span>
						     </label>
						</div>';
	                }
                echo '</div>';
                }
            }
            //echo '</div>';
            // display manual instructions. hide all that are not from first gateway
            $i = 0;
            foreach($gateways_arr['gateways'] as $parent_gateway) {
                foreach($parent_gateway['gateways'] as $gateway) {
                    ++$i;
                    if (!empty($gateway['instruction_for_manual'])) {
                        //$hide_class = (in_array('manual', $gateway_form_tpls_arr)) ? '' : ' hide';
                        $hide_class = ($i == 1) ? '' : ' hide';
                        echo '<div id="manual_instruction_' . $gateway['id'] . '_' . $this->_tab_id . '" class="js-manual-instruction' . $hide_class . '">' . '<div class="alert alert-info">
                            <strong>Manual Instruction</strong> ' . $gateway['instruction_for_manual'] . '</div>
                          </div>';
                    }
                }
            }
            // for first gateway, show/hide relevant forms...
            $buyer_form_class = (is_array($gateway_form_tpls_arr) && in_array('buyer', $gateway_form_tpls_arr)) ? '' : ' hide';
            $creditcard_form_class = (is_array($gateway_form_tpls_arr) && in_array('credit_card', $gateway_form_tpls_arr)) ? '' : ' hide';
            $manual_form_class = (is_array($gateway_form_tpls_arr) && in_array('manual', $gateway_form_tpls_arr)) ? '' : ' hide';
            // Note:
            //   To avoid "An invalid form control with name='' is not focusable" error in Chrome & error in submission...
            //   had to wrap in disabled fieldset as per https://code.google.com/p/chromium/issues/detail?id=45640#c13
            // http://www.whatwg.org/specs/web-apps/current-work/multipage/common-microsyntaxes.html#boolean-attribute
            $buyer_fieldset_disabled = (is_array($gateway_form_tpls_arr) && in_array('buyer', $gateway_form_tpls_arr)) ? '' : ' disabled';
            $creditcard_fieldset_disabled = (is_array($gateway_form_tpls_arr) && in_array('credit_card', $gateway_form_tpls_arr)) ? '' : ' disabled';
            $manual_fieldset_disabled = (is_array($gateway_form_tpls_arr) && in_array('manual', $gateway_form_tpls_arr)) ? '' : ' disabled';
            $pay_via_paypal = 'Pay Now';
            //display all forms with show/hide...
            $show_hide = 'hide';
            if($wallet_id == 'wallet'){
				$show_hide = 'hide';
            }

            echo <<<EOT
            <div class="js-form-tpl-buyer js-form-tpl clearfix {$show_hide} {$buyer_form_class}">
                <fieldset {$buyer_fieldset_disabled}>
                    <h5 class="ver-space">Payer Details</h5>
                    {$gateways_arr['_form_fields_tpls']['buyer']['_html5']}
                </fieldset>
            </div>

            <div class="js-form-tpl-credit_card js-form-tpl clearfix cc-section {$creditcard_form_class}">
                <fieldset {$creditcard_fieldset_disabled} class="sudopay-crdtcard">
                    <h5 class="title-one">Credit Card Details</h5>
                    {$gateways_arr['_form_fields_tpls']['credit_card']['_html5']}
                    <div id="card_error" class="text-danger"></div>
                    <div class="cc-type"></div>
                    <div class="cc-default"></div>
                </fieldset>
            </div>

            <div class="js-form-tpl-manual js-form-tpl clearfix {$manual_form_class}">
                <fieldset {$manual_fieldset_disabled}>
                    <h5 class="ver-space">Payment Note (Manual)</h5>
                    {$gateways_arr['_form_fields_tpls']['manual']['_html5']}
                </fieldset>
            </div>

            </div>
            <div class="clearfix">
                <input class="btn" type="hidden" name="action" value="{$action}">
                <button type="button" name="" value="{$pay_via_paypal}" class="btn green" onclick="proceedPayment('USD', 'sudopay', '{$is_credit}');">
                    {$pay_via_paypal}
                </button>
            </div>
            </div>
			</form>
EOT;

        }
	}

    public function displayGateways($action = '', $get_buyer_details = false, $is_credit = 'No')
    {
    	$wallet_id = Input::get('wallet_id');
        $gateways_arr = $this->sa->callGetGateways(array(
            'supported_actions' => $action
        ));
        \Log::info('Gate way start =======>');
        //\Log::info(print_r($gateways_arr));
        \Log::info('Gate way end =======>');
        if (!empty($gateways_arr) && empty($gateways_arr['error']) && !empty($gateways_arr['gateways'])) {
            $this->_tab_id++;
            $active_tab = 0;
            echo '<form method="post" class="js-payment wallet-tab"><div class="fonts14 top-space wallet-tab"><div class="bs-example bs-example-tabs"><ul class="nav nav-tabs col-md-2">';
            //Display parent gateways in tab
            foreach($gateways_arr['gateways'] as $par_gateway) {
                $active_tab++;
                $li_tab = '<li title="' . $par_gateway['display_name'] . '" class="text-center';
                if ($active_tab == 1) {
                    $li_tab.= ' active';
                }
                $li_tab.= '" onclick="hideCreditCard('.$par_gateway['id'].')" >';
                echo $li_tab . '<a data-toggle="tab" href="#gateways-' . $par_gateway['id'] . '-' . $this->_tab_id . '">
					<!--<span class="show">
						<img data-toggle="tooltip" src="' . $par_gateway['thumb_url'] . '" alt="' . $par_gateway['display_name'] . '">
					</span>-->
                    	<span>' . $par_gateway['display_name'] . '</span>
					</a>
				</li>';
            }
            //Display tab content
            echo '</ul>
            <div id="myTabContent' . $this->_tab_id . '" class="tab-content margin-bottom-20 col-md-10 wid-per-70">';
                $i = 0;

                foreach($gateways_arr['gateways'] as $parent_gateway) {
                    $i++;
                    echo '<div class="tab-pane fade ';
                    if ($i == 1) {
                        echo 'active ';
                    }
                    echo 'in" id="gateways-' . $parent_gateway['id'] . '-' . $this->_tab_id . '">';
                    //Display radio button and child gateways
                    foreach($parent_gateway['gateways'] as $gateway) {
                        $buyer_fees_formula = (isset($gateway['buyer_fees_formula'])) ? $gateway['buyer_fees_formula'] : '';
                        if ($i == 1) {
                            $gateway_form_tpls_arr = $gateway['_form_fields']['_extends_tpl'];
                        }
                        $thumb_url = ($parent_gateway['id'] == 4922)? $parent_gateway['thumb_url']:$gateway['thumb_url'];
                        echo '<div id="gateway_' . $gateway['id'] . '" class="span2">';
                        $input = '<input type="radio" class="js-gateway margin-right-5" data-form-tpl="' . implode(',', $gateway['_form_fields']['_extends_tpl']) . '" name="gateway_id_' .$parent_gateway['id']. '" id="sudopay_gateway_' . $gateway['id'] . '_' . $this->_tab_id . '" value="' . $gateway['id'] . '" onclick="calcBuyerFeesFormula()"';
                        $input.= ' checked="checked"';
                        $input.= '/>';
                        echo $input;
                        $buyer_fourmula_hdn = '<input type="hidden" name="buyer_fees_formula" id="buyer_fees_formula_'.$parent_gateway['id'].'_'.$gateway['id'].'" value="'.$buyer_fees_formula.'">';
                        echo $buyer_fourmula_hdn;
                        echo '<label for="sudopay_gateway_' . $gateway['id'] . '_' . $this->_tab_id . '">
                                  <span class="text-center">
                                     <span class="show">
                                        <img alt="' . $gateway['display_name'] . '" data-toggle="tooltip" title="' . $gateway['display_name'] . '" src="' .$thumb_url  . '"/>
                                     </span>
                                     <span><small>' . $gateway['display_name'] . '</small></span>
                                  </span>
                              </label>';

                            echo '<div class="js-form-tpl-credit_card js-form-tpl clearfix cc-section {$creditcard_form_class}">';
                            echo '<fieldset{$creditcard_fieldset_disabled} class="sudopay-crdtcard">';
                            echo $gateways_arr['_form_fields_tpls']['credit_card']['_html5'];
                            echo '<div id="card_error" class="text-danger fonts12"></div>';
                            echo '<div class="cc-type"></div>';
                            echo '<div class="cc-default"></div>';
                            echo '</fieldset>';
                            echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                echo '</div>';

            // display manual instructions. hide all that are not from first gateway
            $i = 0;
            foreach($gateways_arr['gateways'] as $parent_gateway) {
                foreach($parent_gateway['gateways'] as $gateway) {
                    ++$i;
                    if (!empty($gateway['instruction_for_manual'])) {
                        //$hide_class = (in_array('manual', $gateway_form_tpls_arr)) ? '' : ' hide';
                        $hide_class = ($i == 1) ? '' : ' hide';
                        echo '<div id="manual_instruction_' . $gateway['id'] . '_' . $this->_tab_id . '" class="js-manual-instruction' . $hide_class . '">' . '<div class="alert alert-info">
                            <strong>Manual Instruction</strong> ' . $gateway['instruction_for_manual'] . '</div>
                          </div>';
                    }
                }
            }
            // for first gateway, show/hide relevant forms...
            $buyer_form_class = (is_array($gateway_form_tpls_arr) && in_array('buyer', $gateway_form_tpls_arr)) ? '' : ' hide';
            $creditcard_form_class = (is_array($gateway_form_tpls_arr) && in_array('credit_card', $gateway_form_tpls_arr)) ? '' : ' hide';
            $manual_form_class = (is_array($gateway_form_tpls_arr) && in_array('manual', $gateway_form_tpls_arr)) ? '' : ' hide';
            // Note:
            //   To avoid "An invalid form control with name='' is not focusable" error in Chrome & error in submission...
            //   had to wrap in disabled fieldset as per https://code.google.com/p/chromium/issues/detail?id=45640#c13
            // http://www.whatwg.org/specs/web-apps/current-work/multipage/common-microsyntaxes.html#boolean-attribute
            $buyer_fieldset_disabled = (is_array($gateway_form_tpls_arr) && in_array('buyer', $gateway_form_tpls_arr)) ? '' : ' disabled';
            $creditcard_fieldset_disabled = (is_array($gateway_form_tpls_arr) && in_array('credit_card', $gateway_form_tpls_arr)) ? '' : ' disabled';
            $manual_fieldset_disabled = (is_array($gateway_form_tpls_arr) && in_array('manual', $gateway_form_tpls_arr)) ? '' : ' disabled';
            $pay_via_paypal = 'Pay Now';
            //display all forms with show/hide...
            $show_hide = 'hide';
            if($wallet_id == 'wallet'){
				$show_hide = 'hide';
            }

            echo <<<EOT
            <div class="js-form-tpl-buyer js-form-tpl clearfix {$show_hide} {$buyer_form_class}">
                <fieldset{$buyer_fieldset_disabled}>
                    <h5 class="ver-space">Payer Details</h5>
                    {$gateways_arr['_form_fields_tpls']['buyer']['_html5']}
                </fieldset>
            </div>

            <div class="js-form-tpl-manual js-form-tpl clearfix {$manual_form_class}">
                <fieldset{$manual_fieldset_disabled}>
                    <h5 class="ver-space">Payment Note (Manual)</h5>
                    {$gateways_arr['_form_fields_tpls']['manual']['_html5']}
                </fieldset>
            </div>
            </div>
            <div class="col-md-offset-2 col-md-6 padlft35">
                <input class="btn" type="hidden" name="action" value="{$action}">
                <button type="button" name="" value="{$pay_via_paypal}" class="btn green pull-left" onclick="proceedPayment('USD', 'sudopay', '{$is_credit}');">
                	<i class="fa fa-check"></i> {$pay_via_paypal}
                </button>
            </div>
            </div>
            </form>
EOT;

        } else {
            $this->_showError($gateways_arr['error']);
        }
    }
    public function displayReceivers($receiver_account)
    {
        $gateways_arr = $this->sa->callGetGateways(array(
            'supported_actions' => 'Marketplace-Auth,Marketplace-Capture'
        ));
        //echo '<pre>';print_r($gateways_arr);echo '</pre>';die;
        if(!empty($gateways_arr)) {
			if(isset($gateways_arr['error'])) {
				$this->_showError($gateways_arr['error']);
			}
			else {
				$i = 0;
	            foreach($gateways_arr['gateways'] as $parent_gateway) {
	                foreach($parent_gateway['gateways'] as $gateway) {
	                	if ($i == 1) {
			            	$gateway_form_tpls_arr = $gateway['_form_fields']['_extends_tpl'];
			            }
	                    //$connected_gateway_response = $this->sa->callGetReceiverAccounts('19890', $gateway['id']);
	                    //echo '<pre>';print_r($connected_gateway_response);echo '</pre>';die;
	                    echo '<div class="col-md-12 fonts14 top-space detail-payment"><div class="row clearfix"><div class="col-md-2 margin-bottom-10"><img alt="' . $gateway['display_name'] . '" data-toggle="tooltip" title="' . $gateway['display_name'] . '" src="' . $gateway['thumb_url'] . '"></div>';
	                    //if (!empty($connected_gateway_response['gateways']) && in_array($gateway['id'], $connected_gateway_response['gateways'])) {
	                    if ($this->sudopay_service->isGatewayConnected($this->logged_user_id, $gateway['id'])) {
	                        echo '<span> Connected </span> <span><a href="'.URL::to('sudopay/disconnect-reciver-account').'?gid='.$gateway["id"].'&action=disconnect">disconnect</a></span>';
	                    } else {
	                    $form = '';
	                    	if($gateway['id'] == 2504){
	                        	$form = <<<EOT

         <fieldset>
           <h5 class="ver-space">Bank Details</h5>
           {$gateways_arr['_form_fields_tpls']['bank']['_html5']}
         </fieldset>

EOT;
                    }
                    if($gateway['id'] == 2504){
                    	echo '<form action="'.URL::to('sudopay/connect-reciver-account').'?gid='.$gateway["id"].'&action=connect" method="post">';
                    }
                    echo '<div class="col-md-6"><div class="clearfix htruncate well"><p><strong>' . $gateway['name'] . '</strong></p>' . $gateway['connect_instruction'] . $form.'</div></div><div class="col-md-3 margin-bottom-20">';
                    if($gateway['id'] == 2504){
                    	echo '<input class="btn green-meadow" type="submit" name="submit" value="Connect my '.$gateway['name'].' account">';
                    }else{
                    	//echo '<a class = "btn green-meadow" href="index.php?gid=' . $gateway['id'] . '&action=connect"><i class="fa fa-chevron-right"></i> Connect my ' . $gateway['name'] . ' account</a></div>';
                    	echo '<a class = "btn green-meadow" href="'.URL::to('sudopay/connect-reciver-account').'?gid=' . $gateway['id'] . '&action=connect"><i class="fa fa-chevron-right"></i> Connect my ' . $gateway['name'] . ' account</a></div>';
                    }
                    if($gateway['id'] == 2504){
                    	echo '</form>';
                    }
                }
                echo '</div></div>';
            }
            $i++;
	            }

			}
		}
    }
    public function displayTransactions()
    {
        $arr = $this->sa->callGetAllPayments();
        if (!empty($arr) && empty($arr['error'])) {
            $list = '<table class="table table-bordered">
					<thead>
						<tr>
							<th>Id</th>
							<th>Amount</th>
							<th>Currency Code</th>
							<th>Status</th>
							<th>Gateway</th>
							<th>Buyer</th>
						</tr>
					</thead>
					<tbody>';
            if (!empty($arr['payments'])) {
                foreach($arr['payments'] as $payment) {
                    $list.= '<tr><td>' . $payment['id'] . '</td>';
                    $list.= '<td>' . $payment['amount'] . '</td>';
                    $list.= '<td>' . $payment['currency_code'] . '</td>';
                    $list.= '<td>' . $payment['status'] . '</td>';
                    $list.= '<td>' . $payment['gateway']['name'] . '</td>';
                    $list.= '<td>&lt;Email Hidden&gt;</td></tr>'; // $list.= '<td>' . $payment['buyer']['email'] . '</td>';

                }
            } else {
                $list.= '<tr><td colspan="6">No transaction histroies available</td></tr>';
            }
            $list.= '</tbody>
			</table>';
            echo $list;
        } else {
            $this->_showError($arr['error']);
        }
    }
    public function createReceiver($fields_arr)
    {
        $create_account = $this->sa->callCreateReceiverAccount($fields_arr);
        //echo '<pre>';print_r($create_account);echo '</pre>';die;
        \Log::info('createReceiver start ========================================>');
        \Log::info(print_r($create_account, 1));
        if ($create_account['error']['code'] == 0) {
            header('location: ' . $create_account['gateways']['gateway_callback_url']);
            //return Redirect::to(htmlspecialchars_decode($create_account['gateways']['gateway_callback_url']));
            exit;
        } else {
            $this->_showError($create_account['error']);
        }
        \Log::info('createReceiver end ========================================>');
    }
    private function _handlePaymentResponse($response)
    {
        if ($response['error']['code'] == 0) {
            echo '<div class="clearfix alert-block text-center alert alert-success">
        			<p>Great, your SudoPay demo payment has been succeeded.</p>
        		</div>';
        } else if ($response['error']['code'] < 0) {
            // confirmation token found?
            if (!empty($response['confirmation'])) {
                echo '<div class="clearfix alert-block text-center alert alert-success">
        			<p>Confirmation token: ' . $response['confirmation']['token'] . '</p>
                    <p>Confirmation note: ' . $response['confirmation']['note'] . '</p>
                    <p>Revised amount: ' . $response['revised_amount'] . '</p>
                    <p>You\'ll have to post back confirmation token for confirming the amount change.</p>
        		</div>';
                // TODO: Note, you'll have to post back the confirmation code

            } else if (!empty($response['gateway_callback_url'])) { // redirect to callback URL...
                header('location: ' . $response['gateway_callback_url']);
                exit;
            } else if ($response['error']['code'] == -8) {
                echo '<div class="clearfix alert-block text-center alert alert-success">
        			<p>Great, your SudoPay demo payment has been succeeded. Your transaction status is pending.</p>
        		</div>';
            }
        } else if ($response['error']['code'] > 0) {
            $this->_showError($response['error']);
        }
    }
    public function makeAuthPayment($fields_arr)
    {
        $fields_arr['signature'] = SudoPay_Utils::getSignature($this->sa->secret, $fields_arr);
        $response = $this->sa->callAuth($fields_arr);
        $this->_handlePaymentResponse($response);
    }
    public function makeCapturePayment($fields_arr)
    {
        $fields_arr['signature'] = SudoPay_Utils::getSignature($this->sa->secret, $fields_arr);
        $response = $this->sa->callCapture($fields_arr);
        $this->_handlePaymentResponse($response);
    }
    public function makeMarketplaceAuthPayment($fields_arr)
    {
    	Log::info('fields_arr ============>');
    	Log::info(print_r($fields_arr, 1));
        $fields_arr['signature'] = SudoPay_Utils::getSignature($this->sa->secret, $fields_arr);
        $response = $this->sa->callMarketplaceAuth($fields_arr);
        Log::info('makeMarketplaceAuthPayment response start ===========================>');
        Log::info(print_r($response, 1));
        Log::info('makeMarketplaceAuthPayment response end ===========================>');
        $this->_handlePaymentResponse($response);
    }
    public function makeMarketplaceCapturePayment($fields_arr)
    {
    	/*Log::info('makeMarketplaceCapturePayment ============>');
    	Log::info('fields_arr ============>');
    	Log::info(print_r($fields_arr, 1));
    	Log::info('secret kay ===>'.$this->sa->secret);*/
        //$fields_arr['signature'] = SudoPay_Utils::getSignature($this->sa->secret, $fields_arr);
        $response = $this->sa->callMarketplaceCapture($fields_arr);
        Log::info('makeMarketplaceCapturePayment response start ===========================>');
        Log::info(print_r($response, 1));
        Log::info('makeMarketplaceCapturePayment response end ===========================>');
        $this->_handlePaymentResponse($response);
    }
}
?>