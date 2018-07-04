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
class SudoPay_API
{
    // API URL
    private $live_api_url = 'https://sudopay.com/api/v1';
    private $sandbox_api_url = 'http://sandbox.sudopay.com/api/v1';
    // Payment URL
    private $live_payment_url = 'https://sudopay.com/pay';
    private $sandbox_payment_url = 'http://sandbox.sudopay.com/pay';
    // Button Script URL
    private $live_button_url = '//d1fhd8b1ym2gwa.cloudfront.net/btn/sudopay_btn.js';
    private $sandbox_button_url = '//d1fhd8b1ym2gwa.cloudfront.net/btn/sandbox/sudopay_btn.js';
    // Set mode sandbox(false)/live(true)
    private $is_live = false;
    //URL set based on payment mode($is_live)
    public $api_url;
    public $payment_url;
    public $button_url;
    private $api_key = '';
    private $merchant_id = '';
    private $website_id = '';
    private $format = 'json';
    private $debug = true;
    // Cache setting for GET requests as we'll rate limit API calls.
    private $cache_duration = '+48 hours';
    private $cache_path = 'cache/';
    private $url_replace_filename_arr = array(
        ' ',
        '/',
        ':',
        '?',
        '&',
        '$',
    );
    public $secret = '';
    public function __construct($settings = array())
    {
        $this->api_url = ($this->is_live) ? $this->live_api_url : $this->sandbox_api_url;
        $this->payment_url = ($this->is_live) ? $this->live_payment_url : $this->sandbox_payment_url;
        $this->button_url = ($this->is_live) ? $this->live_button_url : $this->sandbox_button_url;
        foreach($settings as $key => $val) {
            if (!empty($val)) {
                $this->{$key} = $val;
            }
        }
    }
    private function _getCurrentIP()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP') , 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR') , 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR') , 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }
        return $ip;
    }
    private function _safe_json_decode($json)
    {
        $return = json_decode($json, true);
        if ($return === null) {
            $error['error']['code'] = 1;
            $error['error']['message'] = 'Syntax error, malformed JSON';
            return $error;
        }
        return $return;
    }
    private function _safe_xml_decode($xml)
    {
        libxml_use_internal_errors(true);
        $return = simplexml_load_string($xml);
        if ($return === false) {
            $error['error']['code'] = 1;
            $error['error']['message'] = 'Syntax error, malformed XML';
            return $error;
        }
        $return = json_decode(json_encode((array)$return) , true);
        return $return;
    }
    private function _doGet($url)
    {
        $filename = $this->cache_path . str_replace($this->url_replace_filename_arr, '_', $url);
        if ($fh = @fopen($filename, 'r')) {
            $content = unserialize(fread($fh, filesize($filename)));
            fclose($fh);
            if (strtotime('now') < $content['expires']) {
                return $content['response'];
            } else {
                @unlink($filename);
            }
        }
        $return = $this->_execute($url);
        if (empty($return->error->code)) {
            if ($fh = @fopen($filename, 'w+')) {
                $content['expires'] = strtotime('now ' . $this->cache_duration);
                $content['response'] = $return;
                fwrite($fh, serialize($content));
                fclose($fh);
            }
        }
        return $return;
    }
    private function _doPost($url, $post = array())
    {
        return $this->_execute($url, 'post', $post);
    }
    private function _doPut($url, $post = array())
    {
        return $this->_execute($url, 'put', $post);
    }
    private function _doDelete($url)
    {
        return $this->_execute($url, 'delete');
    }
    private function _execute($url, $method = 'get', $post = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->merchant_id . ':' . $this->api_key);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 300 seconds (5min)
        if ($method == 'get') {
            curl_setopt($ch, CURLOPT_POST, false);
        } elseif ($method == 'post') {
            $post['buyer_ip'] = $this->_getCurrentIP();
            $post_string = http_build_query($post, '', '&');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        } elseif ($method == 'put') {
            $post_string = http_build_query($post, '', '&');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        } elseif ($method == 'delete') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Changes handled for gateway payU & EBS
        if($http_code == '200') {
			if($method == 'post' && !empty($post) && ($post['gateway_id'] == '6034' || $post['gateway_id'] == '6005' )) {
				$data = $this->_safe_json_decode($response);
				if(!empty($data['gateway_callback_url'])) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $data['gateway_callback_url']);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					$output = curl_exec($ch);
					echo $output;
					exit;
				}
			}
		}

        if ($this->debug) {
            // relative to this app...
            $log_filename = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'debug.log';
            $log_content = date('Y-m-d H:i:s') . __FILE__ . '#' . __LINE__ . "\n";
            $log_content.= 'URL: ' . $url . "\n";
            $log_content.= 'HTTP code: ' . $http_code . "\n";
            $log_content.= 'Response: ' . $response . "\n";
            //error_log($log_content, 3, $log_filename);
            \Log::info('Debug start =========================>');
            \Log::info($log_content);
            \Log::info('Debug end =========================>');
        }
        // Note: timeout also falls here...
        if (curl_errno($ch)) {
            $return['error']['code'] = 1;
            $return['error']['message'] = curl_error($ch);
            curl_close($ch);
            return $return;	//return $error;
        }
        switch ($http_code) {
            case 200:
                if ($this->format == 'json') {
                    $return = $this->_safe_json_decode($response);
                } else if ($this->format == 'xml') {
                    $return = $this->_safe_xml_decode($response);
                }
                if (!empty($return['error']['code']) && is_array($return['error']['message'])) {
                    $return['error']['message'] = implode(', ', $return['error']['message']);
                }
                break;

            case 401:
                $return['error']['code'] = 1;
                $return['error']['message'] = 'Unauthorized';
                break;

            case 400:
            case 500:
            case 504: /* Here we're not sure if anything got already triggered/saved in SudoPay */
                $return['error']['code'] = 1;
                $return['error']['message'] = 'Problem in gateway. Recheck later.';
                break;

            default:
                $return['error']['code'] = 1;
                $return['error']['message'] = 'Not Found';
            }
            curl_close($ch);
            return $return;
    }
    public function callCapture($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/capture' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callCaptureConfirm($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/capture/confirm' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callAuth($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/auth' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callAuthConfirm($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/auth/confirm' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callAuthCapture($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/' . $post['payment_id'] . '/auth-capture' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    public function callVoid($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/' . $post['payment_id'] . '/void' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    public function callRefund($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/' . $post['payment_id'] . '/refund' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    public function callMarketplaceCapture($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/marketplace-capture' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callMarketplaceCaptureConfirm($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/marketplace-capture/confirm' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callMarketplaceAuth($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/marketplace-auth' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callMarketplaceAuthConfirm($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/marketplace-auth/confirm' . '.' . $this->format;
        $post['website_id'] = $this->website_id;
        return $this->_doPost($url, $post);
    }
    public function callMarketplaceAuthCapture($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/' . $post['payment_id'] . '/marketplace-auth-capture' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    public function callMarketplaceVoid($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/' . $post['payment_id'] . '/marketplace-void' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    public function callMarketplaceRefund($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/gateways/' . $post['gateway_id'] . '/payments/' . $post['payment_id'] . '/marketplace-refund' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    // Get detail about single payment_id...
    public function callGetPayment($payment_id = '')
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/payment/' . $payment_id . '.' . $this->format;
        return $this->_doGet($url);
    }
    // All payments for the merchant...
    public function callGetAllPayments($page = '')
    {
        $page = !empty($page) ? '?page=' . $page : '';
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/payments' . '.' . $this->format . $page;
        return $this->_doGet($url);
    }
    // All but for website payments...
    public function callGetWebsitePayments($page = '')
    {
        $page = !empty($page) ? '&page=' . $page : '';
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/payments?website_id=' . $this->website_id . '.' . $this->format . $page;
        return $this->_doGet($url);
    }
    // Get plan detail of merchant...
    public function callGetPlan()
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/plan' . '.' . $this->format;
        return $this->_doGet($url);
    }
    // Get enabled gateways of merchant...
    public function callGetGateways($supported_query = '')
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/gateways' . '.' . $this->format;
        if (!empty($supported_query)) {
            $url.= '?' . http_build_query($supported_query);
        }
        return $this->_doGet($url);
    }
    public function callCreateReceiverAccount($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/gateways/' . $post['gateway_id'] . '/receiver_accounts' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    // Get receiver accounts
    public function callGetReceiverAccounts($receiver_id = '', $gateway_id = '', $page = '')
    {
        if (!empty($gateway_id)) {
            $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/gateways/' . $gateway_id . '/receiver_accounts/' . $receiver_id . '.' . $this->format;
        } else {
            $page = !empty($page) ? '?page=' . $page : '';
            $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/receiver_accounts/' . $receiver_id . '.' . $this->format . $page;
        }
        return $this->_doGet($url);
    }
    public function callDeleteReceiverAccounts($receiver_id = '', $gateway_id = '')
    {
        if (!empty($gateway_id)) {
            $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/gateways/' . $gateway_id . '/receiver_accounts/' . $receiver_id . '.' . $this->format;
        } else {
            $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/receiver_accounts/' . $receiver_id . '.' . $this->format;
        }
        return $this->_doDelete($url);
    }
    //get vault listing
    public function callGetVaults($user_handle = '', $page = '')
    {
        if (!empty($user_handle)) {
            $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/users/' . $user_handle . '/vaults/type/cc.' . $this->format;
        } else {
            $page = !empty($page) ? '?page=' . $page : '';
            $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/vaults/type/cc.' . $this->format . $page;
        }
        return $this->_doGet($url);
    }
    public function callAddVault($post)
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/vaults/type/cc' . '.' . $this->format;
        return $this->_doPost($url, $post);
    }
    public function callEditVault($vault_id = '')
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/vaults/' . $vault_id . '.' . $this->format;
        return $this->_doPut($url, $post);
    }
    public function callDeleteVault($vault_id = '')
    {
        $url = $this->api_url . '/merchants/' . $this->merchant_id . '/websites/' . $this->website_id . '/vaults/' . $vault_id . '.' . $this->format;
        return $this->_doDelete($url);
    }
}
?>