<?php

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;


class UserTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users')->truncate();
		DB::table('users_groups')->truncate();
		DB::table('newsletter_subscriber')->truncate();
		DB::table('addresses')->truncate();
		DB::table('users_top_picks')->truncate();
		DB::table('user_account_balance')->truncate();
		DB::table('seller_request')->truncate();
		DB::table('users_favorites_products')->truncate();
		DB::table('users_featured')->truncate();
		DB::table('product_favorites')->truncate();

		DB::table('shop_details')->truncate();
		DB::table('cancellation_policy')->truncate();
		DB::table('shipping_templates')->truncate();
		DB::table('shipping_template_companies')->truncate();
		DB::table('shipping_template_fee_custom')->truncate();
		DB::table('shipping_template_fee_custom_countries')->truncate();
		DB::table('password_reminders')->truncate();

		$now = date('Y-m-d H:i:s');

		$names = array('Doe', 'Smith', 'Jones', 'vasan', 'brit', 'dani', 'ani', 'hanna', 'Jhon', 'adam', 'christ', 'williams', 'peter', 'sally', 'sam', 'dhenn', 'James', 'Senthil', 'Manikandan', 'Aboobakkar', 'Ronaldo', 'Ali');
		$shop_names = array('Vasanth & Co', 'Vivek', 'Saravana Stores', 'Rathna Fan House', 'RSV Groups', 'Venkateshvara Stores', 'Murugan Stores', 'Ali Expres');
		$shop_names_prefix = array('One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'New', 'Exclusive', 'Premium', 'Gold', 'Local');
		$paypal_id = array('l.aboobakkarseller1@agriya.in', 'admin@agriya.in');
		$shipping_address[] = array('address_line1' => 'First street, xxxxx nagar', 'address_line2' => 'Nungambakkam', 'city' => 'Chennai', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '600034', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Second street, xxxxx nagar', 'address_line2' => 'Adayar', 'city' => 'Chennai', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '600020', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Third street, xxxxx nagar', 'address_line2' => 'Parrys', 'city' => 'Chennai', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '600001', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Fourth street, xxxxx nagar', 'address_line2' => 'Kottivakkam', 'city' => 'Chennai', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '600041', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Fifth street, xxxxx nagar', 'address_line2' => 'Thiruvanmiyur', 'city' => 'Chennai', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '600041', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Sixth street, xxxxx nagar', 'address_line2' => 'T.Nagar', 'city' => 'Chennai', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '600017', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Seventh street, xxxxx nagar', 'address_line2' => 'N.K Road', 'city' => 'Thanjavur', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '613001', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Eighth street, chettimandapam', 'address_line2' => '', 'city' => 'Kumbakonam', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '612501', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Nienth street, Dheeran Chinnamalai Nagar', 'address_line2' => 'Dindukal Road', 'city' => 'Trichy', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '620001', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Tenth street, xxxxx nagar', 'address_line2' => '', 'city' => 'Coiambatore', 'state' => 'Tamilnadu', 'country' => 'India', 'zip_code' => '641001', 'country_id' => '98');
		$shipping_address[] = array('address_line1' => 'Eleventh street, xxxxx nagar', 'address_line2' => '', 'city' => 'Akarawita', 'state' => 'Colombo', 'country' => 'Sri Lanka', 'zip_code' => '10732', 'country_id' => '115');
		$shipping_address[] = array('address_line1' => 'Twelth street, xxxxx nagar', 'address_line2' => '', 'city' => 'Ganemulla', 'state' => 'Gampaha', 'country' => 'Sri Lanka', 'zip_code' => '11020', 'country_id' => '115');
		$shipping_address[] = array('address_line1' => 'Thirteenth street, xxxxx nagar', 'address_line2' => 'S.H Road', 'city' => 'Alhydri North Nazimabad', 'state' => 'Karachi', 'country' => 'Pakistan', 'zip_code' => '74700', 'country_id' => '153');
		$shipping_address[] = array('address_line1' => 'Fourteenth street, xxxxx nagar', 'address_line2' => 'Lahore Model Town', 'city' => 'Lahore', 'state' => 'Lahore', 'country' => 'Pakistan', 'zip_code' => '54000', 'country_id' => '153');
		$country_code = array('India' => 'IND', 'Sri Lanka' => 'PAK', 'Pakistan' => 'LKA');
		$cancell_policy[] = 'All Vintage items are sold As-IS with no returns. The only exception is if a glaring mistake has been made in the description. Every attempt has been made to photograph and describe the item clearly and concisely. Please review all photos to further see condition. We will also happily answer any questions you may have and will provide additional pictures if requested.

Damages on Insured Items: Please make sure to open and inspect your order when it arrives. If an order arrives damaged, please contact us within 48 hours with photographs and details of the damage. We may need additional photos and information to proceed with a claim. Please keep the original packaging materials as damaged parcels may also need to be picked up by the carrier to proceed with a damage claim. Damage claims must be made within 48 hours of package arrival. No claims can be made after 48 hours of package arrival. Refunds will be made when claim has been settled by the carrier.

Damages on Non-Insured Items: Sadly, breakage in transit does occur occasionally, no matter how diligently it was wrapped. In this case we will issue a full store credit and reserve the right to have you return the damaged item to us (return postage will be sent to you). EXCEPTION: We will not offer a store credit if we have recommended insurance but you have requested to not pay for insurance by convo.';
		$cancell_policy[] = 'The return of the product is only accepted if the product is under perfect conditions (new and without being used). The accepted reasons are: manufacture defect, the color and sizing for an error committed from our behalf. The clients should send a message, indicating their name, within 5 days after receiving the product and solicitate switching. We will send instructions of how to return the product.';

		$template_id = $addresses_id = 1;
		for ($user_id = 1; $user_id <= 1000; $user_id++) {
			$users = $users_groups = $newsletter_subscriber = $addresses = $users_top_picks = $user_account_balance = $seller_request = $shop_details = $users_featured = array();
			$shipping_templates = $shipping_template_companies = $shipping_template_fee_custom = $shipping_template_fee_custom_countries = $cancellation_policy = array();
			$users_favorites_products = $product_favorites = $password_reminders = array();

			//User details
			$users['id'] = $user_id;
			$users['user_name'] = ($user_id == 1)? 'admin' : 'username'.str_pad($user_id, 4, '0', STR_PAD_LEFT);
			$users['email'] = ($user_id == 1)? 'r.senthilvasan@agriya.in': 'username.'.str_pad($user_id, 4, '0', STR_PAD_LEFT).'@agriya.in';
			$users['password'] = '123123';
			if ($users['password']) {
				$strength = 8;
				$saltLength = 22;
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$salt = substr(str_shuffle(str_repeat($pool, 5)), 0, $saltLength);
				$strength1 = str_pad($strength, 2, '0', STR_PAD_LEFT);
				$prefix = PHP_VERSION_ID < 50307 ? '$2a$' : '$2y$';
				$users['password'] = crypt($users['password'], $prefix.$strength1.'$'.$salt.'$');
			}
			$users['permissions'] = '';
			$users['activated'] = 1;
			$users['activation_code'] = '';
			$users['activated_at'] = date('Y-m-d H:i:s');
			$users['last_login'] = date('Y-m-d H:i:s');
			$users['persist_code'] = $this->getRandomString();
			$users['reset_password_code'] = $this->getRandomString();
			$users['first_name'] = ucwords($names[array_rand($names, 1)]);
			$users['last_name'] = ucwords($names[array_rand($names, 1)]);
			$users['about_me'] = '';
			$users['created_at'] = date('Y-m-d H:i:s');
			$users['updated_at'] = date('Y-m-d H:i:s');
			$users['is_shop_owner'] = 'No';
			$users['shop_status'] = '1';
			$users['paypal_id'] = '';
			$users['total_products'] = 0;
			$users['subscribe_newsletter'] = '1';

			//users_groups details
			$users_groups['user_id'] = $user_id;
			$users_groups['group_id'] = ($user_id == 1)? 1 : 2;

			//newsletter_subscriber details
			$newsletter_subscriber['id'] = $user_id;
			$newsletter_subscriber['email'] = $users['email'];
			$newsletter_subscriber['date_added'] = date('Y-m-d H:i:s');
			$newsletter_subscriber['user_id'] = $user_id;

			//seller_request details
			if (!($user_id%25)) {
				$seller_request['id'] = NULL;
				$seller_request['user_id'] = $user_id;
				$seller_request['request_message'] = 'I would like to become seller, please accept my request create shop';
				$seller_request['created_at'] = date('Y-m-d H:i:s');
			}

			if (!($user_id%150)) {
				//users_top_picks details
				$users_top_picks['top_pick_id'] = NULL;
				$users_top_picks['user_id'] = $user_id;
				$users_top_picks['date_added'] = date('Y-m-d H:i:s');
			}

			//user_account_balance details
			$user_account_balance['id'] = NULL;
			$user_account_balance['user_id'] = $user_id;
			$user_account_balance['currency'] = 'USD';
			$user_account_balance['amount'] = $user_id%450;

			if (!($user_id%75)) {
				//users_favorites_products details
				$users_favorites_products['favorite_id'] = NULL;
				$users_favorites_products['product_id'] = rand(1, 1000);
				$users_favorites_products['date_added'] = $now;
			}

			//product_favorites details
			$product_favorites['id'] = NULL;
			$product_favorites['product_id'] = rand(1, 1000);
			$product_favorites['user_id'] = $user_id;
			$product_favorites['list_id'] = 0;
			$product_favorites['created_at'] = $now;
			$product_favorites['updated_at'] = $now;

			if (!($user_id%90)) {
				//users_featured details
				$users_featured['featured_id'] = NULL;
				$users_featured['user_id'] = $user_id;
				$users_featured['date_from'] = '0000-00-00 00:00:00';
				$users_featured['date_to'] = '0000-00-00 00:00:00';
				$users_featured['date_added'] = $now;
			}

			if (!($user_id%10)) {
				//shop settings
				$users['is_shop_owner'] = 'Yes';
				$users['paypal_id'] = $paypal_id[array_rand($paypal_id, 1)];
				$users['is_requested_for_seller'] = 'Yes';
				$users['is_allowed_to_add_product'] = 'Yes';

				//shop_details details
				$shop_details['id'] = NULL;
				$shop_details['user_id'] = $user_id;
				$shop_details['shop_name'] = $shop_names[array_rand($shop_names, 1)].' '.$shop_names_prefix[array_rand($shop_names_prefix, 1)];
				$shop_details['url_slug'] = $this->slugify($shop_details['shop_name']);
				$shop_details['shop_slogan'] = 'Shop Slogan of '.$shop_details['shop_name'];
				$shop_details['shop_desc'] = 'Shop description of '.$shop_details['shop_name'];
				$shop_details['shop_message'] = 'Shop message of '.$shop_details['shop_name'];
				$addresses2 = $shipping_address[array_rand($shipping_address, 1)];
				$shop_details['shop_address1'] = $addresses2['address_line1'];
				$shop_details['shop_address2'] = $addresses2['address_line2'];
				$shop_details['shop_city'] = $addresses2['city'];
				$shop_details['shop_state'] = $addresses2['state'];
				$shop_details['shop_zipcode'] = $addresses2['zip_code'];
				$shop_details['shop_country'] = $country_code[$addresses2['country']];
				$shop_details['policy_welcome'] = 'Shop welcome policy<br />Shop welcome policy<br />Shop welcome policy<br />Shop welcome policy<br />Shop welcome policy';
				$shop_details['policy_payment'] = 'Shop payment policy<br />Shop payment policy<br />Shop payment policy<br />Shop payment policy<br />Shop payment policy';
				$shop_details['policy_shipping'] = 'Shop shipping policy<br />Shop shipping policy<br />Shop shipping policy<br />Shop shipping policy<br />Shop shipping policy<br />Shop shipping policy';
				$shop_details['policy_refund_exchange'] = 'Shop refund policy<br />Shop refund policy<br />Shop refund policy<br />Shop refund policy<br />Shop refund policy<br />Shop refund policy<br />Shop refund policy';
				$shop_details['policy_faq'] = 'Shop FAQ policy<br />Shop FAQ policy<br />Shop FAQ policy<br />Shop FAQ policy<br />Shop FAQ policy<br />Shop FAQ policy<br />Shop FAQ policy<br />';
				$shop_details['created_at'] = date('Y-m-d H:i:s');
				$shop_details['updated_at'] = date('Y-m-d H:i:s');

				//cancellation_policy details
				$cancellation_policy['id'] = NULL;
				$cancellation_policy['user_id'] = $user_id;
				$cancellation_policy['cancellation_policy_text'] = $cancell_policy[array_rand($cancell_policy, 1)];

				//shipping_templates details
				$shipping_templates['id'] = $template_id;
				$shipping_templates['user_id'] = $user_id;
				$shipping_templates['template_name'] = 'Standard';
				$shipping_templates['status'] = 'Active';

				//shipping_template_companies details
				$shipping_template_companies['id'] = $template_id;
				$shipping_template_companies['template_id'] = $template_id;
				$shipping_template_companies['company_id'] = 27;
				$shipping_template_companies['fee_type'] = '1';
				$shipping_template_companies['fee_discount'] = 0;
				$shipping_template_companies['delivery_type'] = '2';
				$shipping_template_companies['days'] = 23;

				//shipping_template_fee_custom details
				$shipping_template_fee_custom_data = array();
				$shipping_template_fee_custom['id'] = NULL;
				$shipping_template_fee_custom['template_id'] = $template_id;
				$shipping_template_fee_custom['company_id'] = 27;
				$shipping_template_fee_custom['template_company_id'] = $template_id;
				$shipping_template_fee_custom['country_selected_type'] = 'geo_location';
				$shipping_template_fee_custom['shipping_setting'] = 'ship_to';
				$shipping_template_fee_custom['fee_type'] = '1';
				$shipping_template_fee_custom['discount'] = 0;
				$shipping_template_fee_custom['custom_fee_type'] = '1';
				$shipping_template_fee_custom['min_order'] = 1;
				$shipping_template_fee_custom['max_order'] = 10;
				$shipping_template_fee_custom['cost_base_weight'] = 10;
				$shipping_template_fee_custom['extra_units'] = 5;
				$shipping_template_fee_custom['extra_costs'] = 3;
				$shipping_template_fee_custom['initial_weight'] = 0;
				$shipping_template_fee_custom['initial_weight_price'] = 0;
				$shipping_template_fee_custom_data[] = $shipping_template_fee_custom;

				$shipping_template_fee_custom['id'] = NULL;
				$shipping_template_fee_custom['template_id'] = $template_id;
				$shipping_template_fee_custom['company_id'] = 27;
				$shipping_template_fee_custom['template_company_id'] = $template_id;
				$shipping_template_fee_custom['country_selected_type'] = 'other_countries';
				$shipping_template_fee_custom['shipping_setting'] = 'other_countries';
				$shipping_template_fee_custom['fee_type'] = '';
				$shipping_template_fee_custom['discount'] = 0;
				$shipping_template_fee_custom['custom_fee_type'] = '';
				$shipping_template_fee_custom['min_order'] = 0;
				$shipping_template_fee_custom['max_order'] = 0;
				$shipping_template_fee_custom['cost_base_weight'] = 0;
				$shipping_template_fee_custom['extra_units'] = 0;
				$shipping_template_fee_custom['extra_costs'] = 0;
				$shipping_template_fee_custom['initial_weight'] = 0;
				$shipping_template_fee_custom['initial_weight_price'] = 0;
				$shipping_template_fee_custom_data[] = $shipping_template_fee_custom;

				//shipping_template_fee_custom_countries details
				$shipping_template_fee_custom_countries['id'] = NULL;
				$shipping_template_fee_custom_countries['template_id'] = $template_id;
				$shipping_template_fee_custom_countries['company_id'] = 27;
				$shipping_template_fee_custom_countries['template_company_id'] = $template_id;
				$shipping_template_fee_custom_countries['shipping_template_fee_custom_id'] = 3;
				$shipping_template_fee_custom_countries['country_id'] = 20;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$shipping_template_fee_custom_countries['country_id'] = 29;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$shipping_template_fee_custom_countries['country_id'] = 98;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$shipping_template_fee_custom_countries['country_id'] = 153;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$shipping_template_fee_custom_countries['country_id'] = 166;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$shipping_template_fee_custom_countries['country_id'] = 115;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$shipping_template_fee_custom_countries['country_id'] = 175;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$shipping_template_fee_custom_countries['country_id'] = 202;
				$shipping_template_fee_custom_countries_data[] = $shipping_template_fee_custom_countries;

				$template_id++;

			} else {
				//addresses details
				$addresses1 = $shipping_address[array_rand($shipping_address, 1)];
				$addresses['id'] = $addresses_id;
				$addresses['user_id'] = $user_id;
				$addresses['address_line1'] = $addresses1['address_line1'];
				$addresses['address_line2'] = $addresses1['address_line2'];
				$addresses['street'] = '2nd street';
				$addresses['city'] = $addresses1['city'];
				$addresses['state'] = $addresses1['state'];
				$addresses['country'] = $addresses1['country'];
				$addresses['zip_code'] = $addresses1['zip_code'];
				$addresses['country_id'] = $addresses1['country_id'];
				$addresses['address_type'] = 'shipping';
				$addresses['is_primary'] = 'Yes';
				$addresses_id++;
			}


			DB::table('users')->insert($users);
			DB::table('users_groups')->insert($users_groups);
			DB::table('newsletter_subscriber')->insert($newsletter_subscriber);
			DB::table('product_favorites')->insert($product_favorites);
			if ($users_favorites_products)
				DB::table('users_favorites_products')->insert($users_favorites_products);
			if ($users_featured) {
				DB::table('users_featured')->insert($users_featured);
			}
			if ($addresses) {
				DB::table('addresses')->insert($addresses);
			}
			if ($users_top_picks) {
				DB::table('users_top_picks')->insert($users_top_picks);
			}
			DB::table('user_account_balance')->insert($user_account_balance);
			if ($seller_request) {
				DB::table('seller_request')->insert($seller_request);
			}
			if ($shop_details) {
				DB::table('shop_details')->insert($shop_details);
				DB::table('cancellation_policy')->insert($cancellation_policy);
				DB::table('shipping_templates')->insert($shipping_templates);
				DB::table('shipping_template_companies')->insert($shipping_template_companies);
				DB::table('shipping_template_fee_custom')->insert($shipping_template_fee_custom_data);
				DB::table('shipping_template_fee_custom_countries')->insert($shipping_template_fee_custom_countries);
			}

			// Add password remainder entry
			$password_reminders['email'] = $users['email'];
			$password_reminders['token'] = $this->getRandomString();
			DB::table('password_reminders')->insert($password_reminders);
		}
		//DB::table('users_groups')->insert($users_groups_data);
		//DB::table('newsletter_subscriber')->insert($newsletter_subscriber_data);
		//if (isset($addresses_data))
		//	DB::table('addresses')->insert($addresses_data);
		//if (isset($users_top_picks_data))
		//	DB::table('users_top_picks')->insert($users_top_picks_data);
		//DB::table('user_account_balance')->insert($user_account_balance_data);
		//if (isset($seller_request_data))
		//	DB::table('seller_request')->insert($seller_request_data);
		//if (isset($shop_details_data)) {
		//	DB::table('shop_details')->insert($shop_details_data);
		//	DB::table('shipping_templates')->insert($shipping_templates_data);
		//	DB::table('shipping_template_companies')->insert($shipping_template_companies_data);
		//	DB::table('shipping_template_fee_custom')->insert($shipping_template_fee_custom_data);
		//	DB::table('shipping_template_fee_custom_countries')->insert($shipping_template_fee_custom_countries_data);
		//}
	}

	public  function slugify($text) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $text);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		return $clean;
	}


	public function getRandomString($length = 42)
	{
		// We'll check if the user has OpenSSL installed with PHP. If they do
		// we'll use a better method of getting a random string. Otherwise, we'll
		// fallback to a reasonably reliable method.
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			// We generate twice as many bytes here because we want to ensure we have
			// enough after we base64 encode it to get the length we need because we
			// take out the "/", "+", and "=" characters.
			$bytes = openssl_random_pseudo_bytes($length * 2);

			// We want to stop execution if the key fails because, well, that is bad.
			if ($bytes === false)
			{
				throw new \RuntimeException('Unable to generate random string.');
			}

			return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
		}

		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	}

}