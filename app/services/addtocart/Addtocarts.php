<?php

class AddtocartException extends Exception {}
class CartInvalidDataException extends AddtocartException {}
class CartRequiredIndexException extends AddtocartException {}
class CartItemNotFoundException extends AddtocartException {}
class CartInvalidItemIdException extends AddtocartException {}
class CartInvalidUserIdException extends AddtocartException {}
class CartInvalidItemQuantityException extends AddtocartException {}

class Addtocarts {

	protected $cart_id;

	protected $fields_arr = array();

	protected $filter_cookie_id = '';

	protected $filter_item_id = '';

	protected $filter_item_owner_id = '';

	protected $cart_per_page = '';

	public function __construct()
	{

	}

	public function setCartId($val)
	{
		$this->fields_arr['id'] = $val;
	}

	public function setCartUserId($val)
	{
		$this->fields_arr['user_id'] = $val;
	}

	public function setCartItemId($val)
	{
		$this->fields_arr['item_id'] = $val;
	}

	public function setCartItemOwnerId($val)
	{
		$this->fields_arr['item_owner_id'] = $val;
	}

	public function setCartItemQuantity($val)
	{
		$this->fields_arr['qty'] = $val;
	}

	public function setCartItemMatrixId($val)
	{
		$this->fields_arr['matrix_id'] = $val;
	}

	public function setCartItemGiftwrap($val)
	{
		$this->fields_arr['use_giftwrap'] = $val;
	}

	public function setCartItemDateModified($val)
	{
		$this->fields_arr['date_modified'] = $val;
	}

	public function setCartCookieId($val)
	{
		$this->fields_arr['cookie_id'] = $val;
	}

	public function setCartPagination($val)
	{
		$this->cart_per_page = $val;
	}

	public function setFilterCookieId($val)
	{
		$this->filter_cookie_id = $val;
	}

	public function setFilterItemId($val)
	{
		$this->filter_item_id = $val;
	}

	public function setFilterItemOwnerId($val)
	{
		$this->filter_item_owner_id = $val;
	}

	/**
	 * Inserts items into the cart.
	 *
	 * @access   public
	 * @param    item
	 * @return   json
	 */
	public function add()
	{
		$rules = $message = array();
		$rules += array(
			'item_id' => 'Required', 'qty' => 'Required|numeric|min:1', 'cookie_id' => 'Required',
		);
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$added_cart_id = 0;
			$matrix_id = isset($this->fields_arr['matrix_id']) ? $this->fields_arr['matrix_id'] : 0;
			$use_giftwrap = isset($this->fields_arr['use_giftwrap']) ? $this->fields_arr['use_giftwrap'] : 0;
			$cart_details = UserCart::Select('id')
										->whereRaw('item_id = ? AND cookie_id = ? ', array($this->fields_arr['item_id'], $this->fields_arr['cookie_id']))
										->first();
			if(count($cart_details) > 0) {
				$added_cart_id = $cart_details['id'];
			}
			$cache_key = 'cart_count_cache_key_'.$this->fields_arr['cookie_id'];
			if($added_cart_id > 0) {
				UserCart::whereRaw('id = ?', array($added_cart_id))->update($this->fields_arr);
				$total_cart_items = UserCart::whereRaw('cookie_id = ?', array($this->fields_arr['cookie_id']))->count();
				HomeCUtil::cachePut($cache_key, $total_cart_items, Config::get('generalConfig.cache_expiry_minutes'));
				return json_encode(array('status' => 'success', 'cart_id' => $added_cart_id));
			}
			else {
				$this->fields_arr['date_added'] = date('Y-m-d H:i:s');
				$added_cart_id = UserCart::insertGetId($this->fields_arr);
				$total_cart_items = UserCart::whereRaw('cookie_id = ?', array($this->fields_arr['cookie_id']))->count();
				HomeCUtil::cachePut($cache_key, $total_cart_items, Config::get('generalConfig.cache_expiry_minutes'));
				return json_encode(array('status' => 'success', 'cart_id' => $added_cart_id));
			}
		}
	}

	/**
	 * Remove an item from the cart.
	 *
	 * @access   public
	 * @param    item_id
	 * @return   json
	 */
	public function remove($cart_id = 0)
	{
		// Try to remove the item.
		$cart = UserCart::whereRaw('id != ?', array(''));
		if($cart_id)
			$cart = $cart->whereRaw('id = ?', array($cart_id));
		if($this->filter_item_owner_id != '')
			$cart = $cart->whereRaw('item_owner_id = ?', array($this->filter_item_owner_id));
		if($this->filter_cookie_id != '')
			$cart = $cart->whereRaw('cookie_id = ?', array($this->filter_cookie_id));
		$cart = $cart->delete();
		if ($cart) {
			return json_encode(array('status' => 'success'));
		}
		return json_encode(array('status' => 'error', 'error_messages' => trans('cart.item_not_exists')));
	}

	/**
	 * Destroy cart contents.
	 *
	 * @param 		user_id
	 * @return 		boolean
	 * @access 		public
	 * @throws   	Exception
	 */
	public function destroy($cart_cookie_id = 0, $seller_id = 0)
	{
		if($cart_cookie_id > 0) {
			$res = UserCart::whereRaw('cookie_id = ?', array($cart_cookie_id));
			if($seller_id > 0)
				$res = $res->whereRaw('item_owner_id = ?', array($seller_id));
			$res = $res->delete();
			if ($res) {
				$cache_key = 'cart_count_cache_key_'.$cart_cookie_id;
				$total_cart_items = UserCart::whereRaw('cookie_id = ?', array($cart_cookie_id))->count();
				HomeCUtil::cachePut($cache_key, $total_cart_items, Config::get('generalConfig.cache_expiry_minutes'));
				return json_encode(array('status' => 'success'));
			}
		}
		return json_encode(array('status' => 'success'));
	}

	/**
	 * Returns the cart contents.
	 *
	 * @param 		user_id
	 * @return 		array
	 * @access 		public
	 * @throws   	Exception
	 */
	public function contents()
	{
		$cart_arr = array();
		$cart = UserCart::Select('id', 'user_id', 'item_id', 'item_owner_id', 'qty', 'cookie_id', 'date_added', 'matrix_id', 'use_giftwrap')
									->orderBy('id', 'ASC');
		if($this->filter_cookie_id != '')
			$cart = $cart->whereRaw('cookie_id = ?', array($this->filter_cookie_id));
		else
			$cart = $cart->whereRaw('id = ?', array(''));

		if($this->filter_item_id != '')
			$cart = $cart->whereRaw('item_id = ?', array($this->filter_item_id));

		if($this->filter_item_owner_id != '')
			$cart = $cart->whereRaw('item_owner_id = ?', array($this->filter_item_owner_id));

		if($this->cart_per_page != '' && $this->cart_per_page > 0)
			$cart = $cart->paginate($this->cart_per_page);
		else
			$cart = $cart->get();
		return $cart;
	}

	/**
	 * Update cart contents.
	 *
	 * @param 		items
	 * @param 		user_id
	 * @return 		boolean
	 * @access 		public
	 */
	public function update($items, $user_id)
	{
		if ( ! is_array($items) or count($items) == 0) {
			throw new CartInvalidDataException;
		}

		foreach($items as $item) {
			$data_update['qty'] = $item['qty'];
			UserCart::whereRaw('id = ? AND user_id = ?', array($item['id'], $user_id))->update($data_update);
		}
		return true;
	}

	public function isCartItemAlreadyAdded($cart_cookie_id, $item_id)
	{
		$added_cart_id = 0;

		$cart_details = UserCart::Select('id')->whereRaw('item_id = ? AND cookie_id = ?', array($item_id, $cart_cookie_id))->first();
		if(count($cart_details) > 0)
		{
			$added_cart_id = $cart_details['id'];
		}
		return $added_cart_id;
	}

	public function getCartItemCount($cart_cookie_id)
	{
		$cache_key = 'cart_count_cache_key_'.$cart_cookie_id;
		if (($total_cart_items = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$total_cart_items = UserCart::whereRaw('cookie_id = ?', array($cart_cookie_id))->count();
			HomeCUtil::cachePut($cache_key, $total_cart_items, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $total_cart_items;
	}
}
?>