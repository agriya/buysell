<?php

class CancellationPolicyService
{
	public function updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath)
	{
		$return_arr = array();
		$config_path = Config::get('webshoppack.shop_cancellation_policy_folder');
		CUtil::chkAndCreateFolder($config_path);
		$file->move(Config::get("webshoppack.shop_cancellation_policy_folder"),$file_name.'.'.$file_ext);
		// open file a image resource
		//Image::make($file->getRealPath())->save(Config::get("webshoppack.shop_cancellation_policy_folder").$file_name.'_O.'.$file_ext);

		$this->deleteShopCancellationPolicyFile();

		$return_arr = array('file_ext' => $file_ext, 'file_name' => $file_name, 'file_server_url' => $destinationpath);
		return $return_arr;
	}
	public function deleteShopCancellationPolicyFile($id='', $folder_name = '')
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		//$cancellation_policy = Products::initializeShops();

		$cancellation_policy = Products::initializeCancellationPolicy();

		$existing_file = $cancellation_policy->getCancellationPolicyDetails($this->logged_user_id);
		if(count($existing_file) > 0 && $existing_file['cancellation_policy_filename'] != '')
		{
			$filename = $existing_file['cancellation_policy_filename'];
			$ext = $existing_file['cancellation_policy_filetype'];

			$cancellation_policy->setCancellationPolicyId($existing_file['id']);
			$cancellation_policy->setCancellationPolicyFilename('');
			$cancellation_policy->setCancellationPolicyFiletype('');
			$cancellation_policy->setCancellationPolicyServerUrl('');

			$resp = $cancellation_policy->save();
			$respd = json_decode($resp, true);

			if ($respd['status'] == 'error') {
				return false;
			}
			if($folder_name == '')
				$folder_name = Config::get('webshoppack.shop_cancellation_policy_folder');

			$this->deleteCancellationPolicyFiles($filename, $ext, $folder_name);
			return true;
		}
		return false;
	}
	public function deleteShopCancellationPolicyFileByAdmin($id='', $folder_name = '', $user_id)
	{

		$cancellation_policy = Products::initializeCancellationPolicy();

		$existing_file = $cancellation_policy->getCancellationPolicyDetails($user_id);
		if(count($existing_file) > 0 && $existing_file['cancellation_policy_filename'] != '')
		{
			$filename = $existing_file['cancellation_policy_filename'];
			$ext = $existing_file['cancellation_policy_filetype'];

			$cancellation_policy->setCancellationPolicyId($existing_file['id']);
			$cancellation_policy->setCancellationPolicyFilename('');
			$cancellation_policy->setCancellationPolicyFiletype('');
			$cancellation_policy->setCancellationPolicyServerUrl('');

			$resp = $cancellation_policy->save();
			$respd = json_decode($resp, true);

			if ($respd['status'] == 'error') {
				return false;
			}
			if($folder_name == '')
				$folder_name = Config::get('webshoppack.shop_cancellation_policy_folder');

			$this->deleteCancellationPolicyFiles($filename, $ext, $folder_name);
			return true;
		}
		return false;
	}
	public function deleteCancellationPolicyFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename.".".$ext))
		{
			unlink($folder_name.$filename.".".$ext);
		}
	}
}