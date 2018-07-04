<?php

class MessageAddService extends MessagingService
{
	public static function getValidatorRule($field)
	{
		$rules = array(
				'subject' => 'required',
				'message_text' => 'required'
		);
		return isset($rules[$field])? $rules[$field] : 'Required';
	}

	public function addMessage($data_arr)
	{
		$message = new Message;
		$valid_user = true;
		$message_id = 0;
		$user_id = 0;

		$logged_user_id = BasicCUtil::getLoggedUserId();

		if(isset($data_arr['user_code']))
		{
			$user_id = CUtil::getUserId($data_arr['user_code']);
		}
		if($user_id == 0 OR $user_id == $logged_user_id)
		{
			$valid_user = false;
		}
		if($valid_user)
		{
			$subject = $data_arr['subject'];
			$data_arr['date_added'] = date('Y-m-d H:i:s');
			$data_arr['subject'] = $subject;
			$data_arr['last_replied_date'] = date('Y-m-d H:i:s');
			$data_arr['from_user_id'] = $logged_user_id;
			$data_arr['to_user_id'] = $user_id;
			$message_id = $message->addNew($data_arr);
			$this->sendMessageAddedNotification($message_id);
		}
		return $message_id;
	}

	public static function populateShopProducts($user_code)
	{
		$product_arr = array();
		$messageAddService = new MessageAddService;
		//Get User id from user code
		$user_id = CUtil::getUserId($user_code);
		if($user_id > 0)
		{
			$product_details = Product::whereRaw('product_user_id =  ?', array($user_id))->get(array('product_name', 'id'));
			$product_arr[""] = trans("common.select_option");
			if(count($product_details) > 0)
			{
				foreach($product_details AS $product)
				{
					$product_arr[$product['id']] = $product['product_name'];
				}
			}
			return $product_arr;
		}
	}
}