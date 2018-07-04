<?php

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;


class ProductCategoryTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('product_category')->where('id', '!=', 1)->delete();

		$now = date('Y-m-d H:i:s');

		$category['main'] = 'Art';
			$sub_category = array();
			$sub_category[] = 'Art Zines';
			$sub_category[] = 'Collage & Mixed Media';
			$sub_category[] = 'Custom Portraits';
			$sub_category[] = 'Decorative Arts';
			$sub_category[] = 'Drawing & Illustration';
			$sub_category[] = 'Figurines & Art Objects';
			$sub_category[] = 'Painting';
			$sub_category[] = 'Photography';
			$sub_category[] = 'Printmaking';
			$sub_category[] = 'Prints & Posters';
			$sub_category[] = 'Sculpture';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Home & Living';
			$sub_category = array();
			$sub_category[] = 'Bath & Beauty';
			$sub_category[] = 'Books, Music & Media';
			$sub_category[] = 'Collectables';
			$sub_category[] = 'Decor & Housewares';
			$sub_category[] = 'Dining & Entertaining';
			$sub_category[] = 'Electronics & Gadgets';
			$sub_category[] = 'Food Market';
			$sub_category[] = 'Furniture';
			$sub_category[] = 'Kitchen';
			$sub_category[] = 'Lighting';
			$sub_category[] = 'Musical Instruments';
			$sub_category[] = 'Outdoors & Garden';
			$sub_category[] = 'Pets';
			$sub_category[] = 'Stationery & Party';
			$sub_category[] = 'Storage & Organisation';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Mobile Accessories';
			$sub_category = array();
			$sub_category[] = 'Decals';
			$sub_category[] = 'Docking Stations & Chargers';
			$sub_category[] = 'Flash Drives';
			$sub_category[] = 'Gear for Android';
			$sub_category[] = 'Gear for Kindle';
			$sub_category[] = 'Gear for iPad Mini';
			$sub_category[] = 'Gear for iPad';
			$sub_category[] = 'Gear for iPhone';
			$sub_category[] = 'Headphones';
			$sub_category[] = 'Keyboard Accessories';
			$sub_category[] = 'Laptop Bags';
			$sub_category[] = 'Laptop Cases';
			$sub_category[] = 'Phone Cases & Wallets';
			$sub_category[] = 'Phone Covers';
			$sub_category[] = 'Phone Plugs & Charms';
			$sub_category[] = 'Speakers';
			$sub_category[] = 'Stands';
			$sub_category[] = 'Styluses';
			$sub_category[] = 'Tablet Accessories';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Jewellery';
			$sub_category = array();
			$sub_category[] = 'Body';
			$sub_category[] = 'Bracelets';
			$sub_category[] = 'Brooches';
			$sub_category[] = 'Earrings';
			$sub_category[] = 'Eco-Friendly';
			$sub_category[] = 'Fine Jewellery';
			$sub_category[] = 'Kids';
			$sub_category[] = 'Men';
			$sub_category[] = 'Necklaces';
			$sub_category[] = 'Personalised';
			$sub_category[] = 'Rings';
			$sub_category[] = 'Storage & Organisation';
			$sub_category[] = 'Watches';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Women';
			$sub_category = array();
			$sub_category[] = 'Accessories';
			$sub_category[] = 'Bags & Purses';
			$sub_category[] = 'Bottoms';
			$sub_category[] = 'Costumes';
			$sub_category[] = 'Dresses';
			$sub_category[] = 'Outerwear';
			$sub_category[] = 'Shoes';
			$sub_category[] = 'Nightwear & Intimates';
			$sub_category[] = 'Specialty';
			$sub_category[] = 'Sizes';
			$sub_category[] = 'Swimwear & Coverups';
			$sub_category[] = 'Tops';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Men';
			$sub_category = array();
			$sub_category[] = 'Tanks';
			$sub_category[] = 'Bags & Wallets';
			$sub_category[] = 'Belts & Buckles';
			$sub_category[] = 'Bottoms';
			$sub_category[] = 'Coats & Jackets';
			$sub_category[] = 'Costumes';
			$sub_category[] = 'Cufflinks';
			$sub_category[] = 'Hats';
			$sub_category[] = 'Hoodies & Sweatshirts';
			$sub_category[] = 'Shirts';
			$sub_category[] = 'Shoes';
			$sub_category[] = 'Suits';
			$sub_category[] = 'Jumpers';
			$sub_category[] = 'T-Shirts';
			$sub_category[] = 'Ties, Clips & Bow Ties';
			$sub_category[] = 'Other Clothing';
			$sub_category[] = 'Other Accessories';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Kids';
			$sub_category = array();
			$sub_category[] = 'Baby & Toddler';
			$sub_category[] = 'Bath';
			$sub_category[] = 'Boys';
			$sub_category[] = 'Costumes';
			$sub_category[] = 'Eco-Friendly';
			$sub_category[] = 'Furniture & Decor';
			$sub_category[] = 'Girls';
			$sub_category[] = 'Personalised';
			$sub_category[] = 'School & Learning';
			$sub_category[] = 'Special Occasions';
			$sub_category[] = 'Toys';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Vintage';
			$sub_category = array();
			$sub_category[] = 'Accessories';
			$sub_category[] = 'Antiques';
			$sub_category[] = 'Art';
			$sub_category[] = 'Bags & Purses';
			$sub_category[] = 'Books';
			$sub_category[] = 'Clothing';
			$sub_category[] = 'Collectables';
			$sub_category[] = 'Electronics';
			$sub_category[] = 'Furniture';
			$sub_category[] = 'Home Decor';
			$sub_category[] = 'Housewares';
			$sub_category[] = 'Jewellery';
			$sub_category[] = 'Paper Ephemera';
			$sub_category[] = 'Serving';
			$sub_category[] = 'Toys';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Weddings';
			$sub_category = array();
			$sub_category[] = 'Bridal Accessories';
			$sub_category[] = 'Decor';
			$sub_category[] = 'Dresses';
			$sub_category[] = 'Groom\'s Corner';
			$sub_category[] = 'Paper Goods';
			$sub_category[] = 'Rings';
			$sub_category[] = 'Wedding Party';
			$sub_category[] = 'Real Weddings';
			$sub_category[] = 'Registry';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Craft Supplies';
			$sub_category = array();
			$sub_category[] = 'DIY Weddings';
			$sub_category[] = 'Fine Arts';
			$sub_category[] = 'Floral & Gardening';
			$sub_category[] = 'Food Crafting';
			$sub_category[] = 'Framing';
			$sub_category[] = 'Gift Wrapping';
			$sub_category[] = 'Jewellery Making';
			$sub_category[] = 'Knitting & Crochet';
			$sub_category[] = 'Paper Crafts';
			$sub_category[] = 'Scrapbooking';
			$sub_category[] = 'Sewing, Quilting & Needle Crafts';
			$sub_category[] = 'Woodworking';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$category['main'] = 'Gift Ideas';
			$sub_category = array();
			$sub_category[] = 'For Couples';
			$sub_category[] = 'For Her';
			$sub_category[] = 'For Him';
			$sub_category[] = 'Teens';
			$sub_category[] = 'Kids & Baby';
			$sub_category[] = 'DIYer';
			$sub_category[] = 'Friends & Coworkers';
			$sub_category[] = 'Gardener & Naturalist';
			$sub_category[] = 'Hostess & Gourmet';
			$sub_category[] = 'Outdoor & Sportsman';
			$sub_category[] = 'Pets & Pet Lovers';
			$sub_category[] = 'Tech Lover';
			$sub_category[] = 'Novelty & Gag Gifts';
			$sub_category[] = 'Stocking Stuffers';
			$sub_category[] = 'Cards & Wrap';
		$category['sub'] = $sub_category;
		$categories[] = $category;

		$template_id = $addresses_id = 1;
		$id = 2;
		$category_left = 2;
		foreach($categories as $main_category){
			//main category details
			$main = array();
			$main['id'] = $id;
			$main['seo_category_name'] = $this->slugify($main_category['main']);
			$main['category_name'] = $main_category['main'];
			$main['category_level'] = 1;
			$main['parent_category_id'] = 1;
			$main['category_left'] = $category_left;
			$main['category_right'] = $category_left + (count($main_category['sub']) * 2) + 1;
			$main['date_added'] = $now;
			$category_left++;
			$id++;

			DB::table('product_category')->insert($main);
			//echo '$main_category:'. $main_category['main']; echo  "\n";
			foreach($main_category['sub'] as $sub_category) {
				//sub category details
				$sub = array();
				$sub['id'] = $id;
				$sub['seo_category_name'] = $this->slugify($sub_category);
				$sub['category_name'] = $sub_category;
				$sub['category_level'] = 2;
				$sub['parent_category_id'] = $main['id'];
				$sub['category_left'] = $category_left;
				$sub['category_right'] = $category_left + 1;
				$sub['date_added'] = $now;
				$category_left++;
				$category_left++;
				$id++;
				//echo ' ****************************** $sub_category:'. $sub_category; echo "\n";
				DB::table('product_category')->insert($sub);
			}
			$category_left++;
		}
		DB::table('product_category')->where('id', 1)->update(array('category_right' => $category_left));
	}

	public function slugify($text) {
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