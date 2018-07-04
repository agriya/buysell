<?php

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;

class GeneralTableSeeder extends Seeder {

	/*
	- site_logo
	- message
	- countries
	- coupons
	- banner_images
	- collections
	- collection_comments
	- collection_favorites
	- password_reminders
	- credits_log
	- newsletter
	- withdrawal_request
	*/

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users_circle')->truncate();
		DB::table('advertisement')->truncate();
		DB::table('collections')->truncate();
		DB::table('product_favorites_list')->truncate();
		DB::table('collection_favorites')->truncate();
		DB::table('collection_products')->truncate();
		DB::table('coupons')->truncate();
		DB::table('user_product_section')->truncate();

		$now = date('Y-m-d H:i:s');

		$collection_name = array('Kitty lights v3', 'ChristmasRecycled Haitian Metal', 'Handmade Soap - Frosty - Light', 'Hand knit toddler earflap hat', 'T-Shirt - Sweet dreams - Little', 'Owl - Print - 8 x 10', 'Set of 2 Hanging Mason Jar Glass', 'Vegetable Tan Leather Key', 'HUGS & KISSES stamp with border', 'Unique Marimo Moss Ball Light', 'Bright Spark Pom Pom Studs', 'A4-Original fine art ink drawing', 'Metal TCG Deck Box for Mtg', 'Vintage granny squares afghan', 'The Welder did it Maternity Shirt', 'Vintage Letter Cubes FOUND', 'Good Day, Bad Day, Jack Daniel', 'A Single Wish Dandelion Seed', 'Kitty butt baby skinny pants', 'Migrations - 2015 calendar', 'ROSE GOLD over Sterling Silver', 'Makeup bag - Floral clutch', 'Real Dandelion Necklace');

		$list_name = array('My Favorite', 'ChristmasRecycled Favorited', 'Handmade Soap', 'Frosty', 'Light', 'Hand knit toddler earflap hat', 'T-Shirt', 'Sweet dreams', 'Little', 'Owl', 'Print - 8 x 10', 'Set of 2 Hanging Mason', 'Jar Glass', 'Vegetable Tan', 'Leather Key', 'HUGS & KISSES', 'stamp with border', 'Unique Marimo', 'Moss', 'Ball Light', 'Bright Spark', 'Pom Pom Studs', 'A4-Original', 'fine art ink drawing', 'Metal TCG', 'Deck Box for Mtg', 'Vintage granny', 'squares afghan', 'The Welder', 'Maternity Shirt', 'Vintage', 'Letter Cubes FOUND', 'Good Day', 'Bad Day', 'Jack Daniel', 'A Single Wish Dandelion Seed', 'Kitty butt', 'baby skinny pants', 'Migrations', '2015 calendar', 'ROSE', 'GOLD over Sterling Silver', 'Makeup bag', 'Floral clutch', 'Real Dandelion Necklace');


		$advertisement_block = array('side-banner', 'bottom-banner', 'top-banner');
		$access = array('Public', 'Private');
		$collection_status = array('Active', 'InActive');
		$featured_status = array('Yes', 'No');
		$price_restriction = array('less_than','greater_than','equal_to','between','none');
		$offer_type = array('Flat', 'Percentage');
		$price[] = 50;
		$price[] = 75;
		$price[] = 100;
		$price[] = 110;
		$price[] = 125;
		$price[] = 150;
		$price[] = 520;
		$price[] = 250;
		$price[] = 350;
		$price[] = 420;
		$price[] = 499;
		$price[] = 502;
		$price[] = 527;
		$price[] = 600;
		$price[] = 1000;
		$price[] = 1125;

		$source[] = '<div>
<div class="new_imgmb20">
<h2 class="products-h2" style="margin-bottom: 0; border-bottom: none;">Client Speaks</h2>
<li style="text-align: center;">
<iframe width="272" height="195" src="http://www.youtube.com/embed/6RoprHXaZGE?enablejsapi=1" frameborder="0" allowfullscreen="" id="widget2"></iframe>
</li>
</div>
<div class="new_content-side1"><h2>Quick Contact</h2>
<p>Write your queries to us and we will address them in a short span of time</p>

<form method="post" action="" id="EmailForm" enctype="multipart/form-data" name="EmailForm" class="js-contact-form">
<input type="hidden" name="geobyte_info" id="geobyte_info" value="{"country_code":"IN","region_name":"Tamil Nadu","city":"Chennai","certainty":"42","latitude":"13.0634","longitude":"80.2207","timezone":"+05:30","proxy":"false","gn_countryCode":"IN","gn_countryName":"India","gn_lat":13.0634,"gn_lng":80.2207,"gn_timezoneId":"Asia/Kolkata","gn_dstOffset":5.5,"gn_gmtOffset":5.5,"gn_rawOffset":5.5}">
<input type="hidden" name="maxmind_info" id="maxmind_info" value="{"country_code":"IN","city":"Chennai","region_name":"Tamil Nadu","latitude":"13.0833","longitude":"80.2833","postal_code":"","mx_countryCode":"IN","mx_countryName":"India","mx_lat":13.0833,"mx_lng":80.2833,"mx_timezoneId":"Asia/Kolkata","mx_dstOffset":5.5,"mx_gmtOffset":5.5,"mx_rawOffset":5.5}">
<input type="hidden" name="browser_info" id="browser_info" value="{"browser_lang":"en-GB","browser_timezone":5.5,"resolution":"1440x900x24","browser":"Chrome","os":"Windows","useragent":"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36","gmt_offset":5.5,"dst_offset":5.5,"dst":"0"}">
<input type="hidden" name="visited_sites" id="visited_sites" value="">
<input type="hidden" name="source" id="source" value="Newsletter">
<input type="hidden" name="medium" id="medium" value="View-Button">
<input type="hidden" name="term" id="term" value="-">
<input type="hidden" name="content" id="content" value="-">
<input type="hidden" name="campaign" id="campaign" value="18.12.2014-Christmas">
<input type="hidden" name="visitor_id" id="visitor_id" value="4bb2027cec76a9ee">
<input type="hidden" name="segment" id="segment" value="(not set)">
<input name="formtype" type="hidden" value="contact">

	<ul class="quick-contact">
		<input name="formid" type="hidden" value="HomeQuickContactForm">
	    <li class="addon">
        	<span><img src="http://www.agriya.com/assets/templates/agriya/images/images_new/icon_user.png" alt="user"></span>
        	<input name="lname" id="cfFname" placeholder="Name" type="text" value="">
        </li>

        <li class="addon">
        	<span><img src="http://www.agriya.com/assets/templates/agriya/images/images_new/icon_msg.png" alt="user"></span>
        	<input name="email" id="cfEmail" placeholder="Email" type="text" value="">
        </li>

	    <li><textarea name="goal" id="cfGoal" placeholder="Comments"></textarea></li>

       	<li>
            <label>reCaptcha*</label>
     		<span for="cfvericode"><img src="/manager/includes/veriword.php" border="1" height="36" width="115" alt="verification code"></span>
            <input name="vericode" id="cfvericode" class="new_enqVerf" size="20" type="text" value="">
	    </li>

        <li>
			<input type="submit" name="contact" id="cfContact" value="Submit">
        </li>
	</ul>
</form>
</div>
</div>';
		$source[] = '<div class="new_content-side1"><h2>Quick Contact - Banner</h2>
<p>Write your queries to us and we will address them in a short span of time</p>

<form method="post" action="" id="EmailForm" enctype="multipart/form-data" name="EmailForm" class="js-contact-form">
<input type="hidden" name="geobyte_info" id="geobyte_info" value="{"country_code":"IN","region_name":"Tamil Nadu","city":"Chennai","certainty":"42","latitude":"13.0634","longitude":"80.2207","timezone":"+05:30","proxy":"false","gn_countryCode":"IN","gn_countryName":"India","gn_lat":13.0634,"gn_lng":80.2207,"gn_timezoneId":"Asia/Kolkata","gn_dstOffset":5.5,"gn_gmtOffset":5.5,"gn_rawOffset":5.5}">
<input type="hidden" name="maxmind_info" id="maxmind_info" value="{"country_code":"IN","city":"Chennai","region_name":"Tamil Nadu","latitude":"13.0833","longitude":"80.2833","postal_code":"","mx_countryCode":"IN","mx_countryName":"India","mx_lat":13.0833,"mx_lng":80.2833,"mx_timezoneId":"Asia/Kolkata","mx_dstOffset":5.5,"mx_gmtOffset":5.5,"mx_rawOffset":5.5}">
<input type="hidden" name="browser_info" id="browser_info" value="{"browser_lang":"en-GB","browser_timezone":5.5,"resolution":"1440x900x24","browser":"Chrome","os":"Windows","useragent":"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36","gmt_offset":5.5,"dst_offset":5.5,"dst":"0"}">
<input type="hidden" name="visited_sites" id="visited_sites" value="">
<input type="hidden" name="source" id="source" value="Newsletter">
<input type="hidden" name="medium" id="medium" value="View-Button">
<input type="hidden" name="term" id="term" value="-">
<input type="hidden" name="content" id="content" value="-">
<input type="hidden" name="campaign" id="campaign" value="18.12.2014-Christmas">
<input type="hidden" name="visitor_id" id="visitor_id" value="4bb2027cec76a9ee">
<input type="hidden" name="segment" id="segment" value="(not set)">
<input name="formtype" type="hidden" value="contact">

	<ul class="quick-contact">
		<input name="formid" type="hidden" value="HomeQuickContactForm">
	    <li class="addon">
        	<span><img src="http://www.agriya.com/assets/templates/agriya/images/images_new/icon_user.png" alt="user"></span>
        	<input name="lname" id="cfFname" placeholder="Name" type="text" value="">
        </li>

        <li class="addon">
        	<span><img src="http://www.agriya.com/assets/templates/agriya/images/images_new/icon_msg.png" alt="user"></span>
        	<input name="email" id="cfEmail" placeholder="Email" type="text" value="">
        </li>

	    <li><textarea name="goal" id="cfGoal" placeholder="Comments"></textarea></li>

       	<li>
            <label>reCaptcha*</label>
     		<span for="cfvericode"><img src="/manager/includes/veriword.php" border="1" height="36" width="115" alt="verification code"></span>
            <input name="vericode" id="cfvericode" class="new_enqVerf" size="20" type="text" value="">
	    </li>

        <li>
			<input type="submit" name="contact" id="cfContact" value="Submit">
        </li>
	</ul>
</form>
</div>';
		$source[] = '<div class="new_content-side1"><h2>Test Banner</h2>
<p>Write your queries to us and we will address them in a short span of time</p>

<form method="post" action="" id="EmailForm" enctype="multipart/form-data" name="EmailForm" class="js-contact-form">
<input type="hidden" name="geobyte_info" id="geobyte_info" value="{"country_code":"IN","region_name":"Tamil Nadu","city":"Chennai","certainty":"42","latitude":"13.0634","longitude":"80.2207","timezone":"+05:30","proxy":"false","gn_countryCode":"IN","gn_countryName":"India';

		for ($inc = 1; $inc <= 50; $inc++)
		{
			$users_circle = $advertisement = $collections = $product_favorites_list = $collection_favorites = $coupons = $collection_products = array();
			$user_product_section = array();
			// User Circle entry
			$users_circle['user_id'] = rand(1, 100);
			$users_circle['circle_user_id'] = (rand(1, 100) * 10);
			DB::table('users_circle')->insert($users_circle);


			if($inc <= 5)
			{
				$banner_block = Config::get("generalConfig.banner_position_arr");

				// Advertisement entry
				$advertisement['add_id']	= $inc;
				$advertisement['user_id']	= 1;
				$advertisement['post_from']	= 'Admin';	// 'User', 'Admin')
				$advertisement['block']		= $advertisement_block[array_rand($advertisement_block, 1)];	// 'User', 'Admin')
				$advertisement['about']		= 'About of '.$advertisement['block'];
				$advertisement['source'] 	= $source[array_rand($source, 1)];
				$advertisement['start_date'] = $now;
				$advertisement['end_date'] 	= date('Y-m-d H:i:s', strtotime('90 days'));
				$advertisement['allowed_impressions']	= rand(1, 100);	// 'User', 'Admin')
				$advertisement['completed_impressions']	= 0;	// 'User', 'Admin')
				$advertisement['status'] 	= 'Active';		// Active', 'Inactive'
				$advertisement['date_added'] = $now;		// Active', 'Inactive'
				DB::table('advertisement')->insert($advertisement);
			}

			$collections['user_id'] = (rand(1, 100) * 10);
			$collections['collection_name'] = ucwords($collection_name[array_rand($collection_name, 1)]);
			$collections['collection_slug'] = $this->slugify($collections['collection_name'].'-'.$inc);
			$collections['collection_description'] = 'Description of '.$collections['collection_name'];
			$collections['collection_access']	= $access[array_rand($access, 1)];
			$collections['collection_status']	= $collection_status[array_rand($collection_status, 1)];
			$collections['featured_collection']	= $featured_status[array_rand($featured_status, 1)];
			$collections['total_views']	= 0;
			$collections['total_comments']	= 0;
			$collections['total_clicks']	= 0;
			$collection_id = DB::table('collections')->insertGetId($collections);

			// Add entry for collection_comments
			for($i=1; $i<=rand(1, 50); $i++)
			{
				$collection_comments['collection_id'] = $collection_id;
				$collection_comments['user_id'] = rand(1, 100);
				$collection_comments['comment'] = 'Comment for collection '.$collections['collection_name'].'_'.rand(1, 100);
				$collection_comments['status'] = $collection_status[array_rand($collection_status, 1)];
				DB::table('collection_comments')->insert($collection_comments);
			}

			// Add entry for collection_products
			for($i=1; $i<=rand(1, 65); $i++)
			{
				$collection_products['collection_id'] = $collection_id;
				$collection_products['product_id'] = rand(1, 1000);
				$collection_products['date_added'] = $now;
				DB::table('collection_products')->insert($collection_products);
			}

			// Add entry for product_favorites_list
			$product_favorites_list['list_name'] = $list_name[array_rand($list_name, 1)].''.rand(1, 57);
			$product_favorites_list['user_id'] = rand(1, 100);
			$product_favorites_list['status'] = rand(0, 1);
			DB::table('product_favorites_list')->insert($product_favorites_list);

			// Add entry for collection_favorites
			for($i=1; $i<=rand(1, 20); $i++)
			{
				$collection_favorites['user_id'] = rand(1, 100);
				$collection_favorites['collection_id'] = $collection_id;
				$collection_favorites['collection_owner_id'] = $collections['user_id'];
				DB::table('collection_favorites')->insert($collection_favorites);
			}
			// Add entry for coupon
			$coupons['user_id'] = rand(1, 100);
			$coupons['coupon_code'] = rand(1, 100).'COUPON'.$inc;
			$coupons['from_date'] = date('Y-m-d H:i:s', strtotime(rand(1, 20).' days'));
			$coupons['to_date'] = date('Y-m-d H:i:s', strtotime(rand(25, 50).' days'));
			$coupons['price_restriction'] = $price_restriction[array_rand($price_restriction, 1)];
			$coupons['price_from'] = rand(1, 200);
			$coupons['price_to'] = rand(500, 1000);
			$coupons['offer_type'] = 'Flat';
			$coupons['offer_amount'] = $price[array_rand($price, 1)];
			$coupons['status'] = $collection_status[array_rand($collection_status, 1)];
			DB::table('coupons')->insert($coupons);


			// Add entry for user_product_section
			$user_product_section['user_id'] = rand(1, 100);
			$user_product_section['section_name'] = rand(1, 100).'section'.$inc;
			$user_product_section['date_added'] = $now;
			$user_product_section['status'] = $featured_status[array_rand($featured_status, 1)];
			DB::table('user_product_section')->insert($user_product_section);
		}

	}

	public  function slugify($text) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $text);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		return $clean;
	}




}