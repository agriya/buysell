<?php
class AdminNewsletterSubscriberService
{
	public function buildNewsletterSubscriberQuery() {
		$this->qry = NewsletterSubscriber::Select('id', 'email', 'ip', 'date_added', 'date_unsubscribed', 'status', 'first_name', 'last_name');
		$this->qry->orderBy('id','DESC');
		return $this->qry;
	}

	public function addNewsletterSubscriberCopyPasteList($input) {
		$subscribers_list = explode("\n", $input['subscribers_list']);
		$total_subscriber_count = count($subscribers_list);
		$imported_count = $failed_subscribers_count = $duplicate_count = 0;
		$failed_emails = array();
		$duplicate_emails = array();

		$subscriber_arr = array();
		if(!empty($subscribers_list)) {
			foreach($subscribers_list AS $subscribers) {
				$subscriber_arr = explode(",", $subscribers);
				if(!empty($subscriber_arr)) {
					$email = trim($subscriber_arr[0]);
					$validator = Validator::make(
					    array('email' => $email),
					    array('email' => 'Required|Email')
					);
					if($validator->passes()) {
						$check = $this->checkEmail($email);
						if($check) {
							$user_id = 0;
							$user_info = User::select('id')->where('email', $email)->first();
							if(count($user_info) > 0) {
								$user_id = $user_info['id'];
							}
							$insert_arr = array();
							$insert_arr['email'] = $email;
							$insert_arr['ip'] = Request::getClientIp();
							$insert_arr['date_added'] = DB::Raw('Now()');
							$insert_arr['unsubscribe_code'] = str_random(10);
							$insert_arr['status'] = 'active';
							if(isset($subscriber_arr[1]) && trim($subscriber_arr[1]) != "") {
								$first_name = trim($subscriber_arr[1]);
								$first_name = str_replace(array('"', "'"), "", $first_name);
								$insert_arr['first_name'] = trim($first_name);
							}
							if(isset($subscriber_arr[2]) && trim($subscriber_arr[2]) != "") {
								$last_name = trim($subscriber_arr[2]);
								$last_name = str_replace(array('"', "'"), "", $last_name);
								$insert_arr['last_name'] = trim($last_name);
							}
							$insert_arr['user_id'] = $user_id;
							$newsletter = new NewsletterSubscriber();
							$newsletterid = $newsletter->addNew($insert_arr);
							$imported_count++;
						}
						else {
							$duplicate_emails[] = $email;
							$duplicate_count++;
						}
					}
					else {
						$failed_emails[] = $email;
						$failed_subscribers_count++;
					}
				}
			}
		}
		return array("total_subscribers" => $total_subscriber_count, "imported_subscribers" => $imported_count, "failed_subscribers" => $failed_subscribers_count, "duplicate_subscribers" => $duplicate_count, "failed_emails" => $failed_emails, "duplicate_emails" => $duplicate_emails);
	}

	public function addNewsletterSubscriberImportFile($input) {
		$gentime = substr(time(), -5);
		$uniqgentime = substr(uniqid().$gentime, 0, 18);
        $newsletterfile = $input['subscribers_importlist'];
        $data_arr['ext'] = $newsletterfile->getClientOriginalExtension();
        $data_arr['filename'] = $uniqgentime;
		$fname = $data_arr['filename'] . '.' . $data_arr['ext'];
	    $destinationPath = Config::get("generalConfig.import_folder");
	    $uploadSuccess = Input::file('subscribers_importlist')->move($destinationPath, $fname);

		$file_path = Request::root().'/'.$destinationPath.$fname;
	    $handle = fopen($file_path, 'r');
	    if(!$handle) return;
	    $subscribers_list = array();

	    while (($data = fgetcsv($handle, 0, ",")) !== FALSE)
	    {
	        foreach($data as $key => $value) {
	            $data[$key] = $value;
	        }
	        $subscribers_list[] = implode(",", $data);
		}
		$total_subscriber_count = count($subscribers_list);
		$imported_count = $failed_subscribers_count = $duplicate_count = 0;
		$failed_emails = array();
		$duplicate_emails = array();

		if(!empty($subscribers_list)) {
			foreach($subscribers_list as $subscriber_val) {
				$firstname = $lastname = '';
				$subscriber_str =  explode(",", $subscriber_val);
				if(count($subscriber_str) > 0) {
					$email = trim($subscriber_str[0]);
					$validator = Validator::make(
					    array('email' => $email),
					    array('email' => 'Required|Email')
					);
					if($validator->passes()) {
						$check = $this->checkEmail($email);
						if($check) {
							$user_id = 0;
							$user_info = User::select('id')->where('email', $email)->first();
							if(count($user_info) > 0) {
								$user_id = $user_info['id'];
							}
							$insert_arr = array();
							$insert_arr['email'] = $email;
							$insert_arr['ip'] = Request::getClientIp();
							$insert_arr['date_added'] = DB::Raw('Now()');
							$insert_arr['unsubscribe_code'] = str_random(10);
							$insert_arr['status'] = 'active';
							if(isset($subscriber_str[1]) && trim($subscriber_str[1]) != "") {
								$insert_arr['first_name'] = trim($subscriber_str[1]);
							}
							if(isset($subscriber_str[2]) && trim($subscriber_str[2]) != "") {
								$insert_arr['last_name'] = trim($subscriber_str[2]);
							}
							$insert_arr['user_id'] = $user_id;
							$newsletter = new NewsletterSubscriber();
							$newsletterid = $newsletter->addNew($insert_arr);
							$imported_count++;
						}
						else {
							$duplicate_emails[] = $email;
							$duplicate_count++;
						}
					}
					else {
						$failed_emails[] = $email;
						$failed_subscribers_count++;
					}
				}
			}
		}
		return array("total_subscribers" => $total_subscriber_count, "imported_subscribers" => $imported_count, "failed_subscribers" => $failed_subscribers_count, "duplicate_subscribers" => $duplicate_count, "failed_emails" => $failed_emails, "duplicate_emails" => $duplicate_emails);
	}

	public function updateSubscriberStatus($subscriber_id, $action) {
		$success_msg = '';
		$details = NewsletterSubscriber::select('user_id')->where('id', $subscriber_id)->first();
		if(count($details) > 0)
		{
			if(strtolower($action) == 'active')
			{
				$data_arr['status'] = 'active';
				$data_arr['unsubscribe_code'] = str_random(10);
				NewsletterSubscriber::where('id', $subscriber_id)->update($data_arr);
				$success_msg = trans('admin/newsletterSubscriber.activated_suc_msg');

				if($details['user_id'] > 0)
				{
					$update_arr['subscribe_newsletter'] = 1;
					User::where('id', $details['user_id'])->update($update_arr);
					$array_multi_key = array('featured_seller_banner_key');
					HomeCUtil::forgotMultiCacheKey($array_multi_key);
				}
			} elseif (strtolower($action) == 'delete') {

				$data_arr['status'] = 'delete';
				$data_arr['date_unsubscribed'] = DB::Raw('Now()');
				$data_arr['unsubscribe_code'] = '';
				NewsletterSubscriber::where('id', $subscriber_id)->update($data_arr);
				$success_msg = trans('admin/newsletterSubscriber.delete_suc_msg');

			} else {
				$data_arr['status'] = 'inactive';
				$data_arr['date_unsubscribed'] = DB::Raw('Now()');
				$data_arr['unsubscribe_code'] = '';
				NewsletterSubscriber::where('id', $subscriber_id)->update($data_arr);
				$success_msg = trans('admin/newsletterSubscriber.deactivated_suc_msg');

				if($details['user_id'] > 0)
				{
					$update_arr['subscribe_newsletter'] = 0;
					User::where('id', $details['user_id'])->update($update_arr);
					$array_multi_key = array('featured_seller_banner_key');
					HomeCUtil::forgotMultiCacheKey($array_multi_key);
				}
			}
		}
		return $success_msg;

	}

	public function checkEmail($email)
	{
		$count = NewsletterSubscriber::select('email')->where('email', $email)->count();
		if($count)
		{
			$update = array();
			$update['ip'] = Request::getClientIp();
			$update['date_added'] = DB::Raw('Now()');
			$update['status'] = 'active';
			$active = NewsletterSubscriber::where('email', $email)->update($update);
			return false;
		}
		else
		{
		return true;
		}
	}
}