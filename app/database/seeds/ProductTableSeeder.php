<?php

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;


class ProductTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('product')->truncate();
		DB::table('product_price_groups')->truncate();
		DB::table('product_log')->truncate();
		DB::table('product_stocks')->truncate();
		DB::table('product_image')->truncate();

		DB::table('user_products_wishlist')->truncate();
		DB::table('product_taxations')->truncate();
		DB::table('reported_products')->truncate();
		DB::table('product_package_details')->truncate();
		DB::table('product_comments')->truncate();
		DB::table('user_cart')->truncate();

		$now = date('Y-m-d H:i:s');

		$report_thread = array('DifferThanAd','IncorrectData','Prohibited','InaccurateCategory');
		$custom_arr = array('Yes', 'No');

		$product_name = array('Kitty lights v3', 'ChristmasRecycled Haitian Metal', 'Handmade Soap - Frosty - Light', 'Hand knit toddler earflap hat', 'T-Shirt - Sweet dreams - Little', 'Owl - Print - 8 x 10', 'Set of 2 Hanging Mason Jar Glass', 'Vegetable Tan Leather Key', 'HUGS & KISSES stamp with border', 'Unique Marimo Moss Ball Light', 'Bright Spark Pom Pom Studs', 'A4-Original fine art ink drawing', 'Metal TCG Deck Box for Mtg', 'Vintage granny squares afghan', 'The Welder did it Maternity Shirt', 'Vintage Letter Cubes FOUND', 'Good Day, Bad Day, Jack Daniel', 'A Single Wish Dandelion Seed', 'Kitty butt baby skinny pants', 'Migrations - 2015 calendar', 'ROSE GOLD over Sterling Silver', 'Makeup bag - Floral clutch', 'Real Dandelion Necklace');
		$product_name_suffix = array('Gold variant', 'Silver variant', 'Platinum variant', 'Black', 'White', 'Green', 'Steel', 'New', 'Old', 'Middle', 'Daily', 'Kids', 'Excellent', 'Furnished', '3BHK', 'Tools', 'Made', 'Make', 'Get Soon', 'Order Quickly', 'Fine quality', 'Good Condition');
		$product_description[] = 'Cerentha Harris is editor-in-chief of mom.me – a national lifestyle parenting website that covers every stage of motherhood from trying to conceive right through to the empty nest. She lives in Los Angeles with her 9-year-old daughter, a 12-year-old son, a crazy Wheaten terrier and a husband who is an awesome cook.

December has well and truly arrived. And we know the holiday season can be a stressful time. There’s so much to do! But it is also such a lovely time of year – trees to be trimmed, cookies to be baked, snowballs to be thrown, and that wonderful infectious excitement that keeps kids (and, let’s be honest, some moms) up at night. Hopefully this list will help you enjoy the good times by making shopping for the kids’ gifts just that little bit easier.';
		$product_description[] = 'These organic-cotton deer leggings are some of the cutest we’ve seen (aside from this shop’s ice cream cone-printed style, that is); they’ll look great with any solid-colored onesie.


ModernPOP, Custom Nursery Alphabet Art, A-Z Alphabet Print, Alphabet Letters, Boy Alphabet , Girl Alphabet, Toddler Art
by ModernPOP on Etsy
Modern POP’s beautifully illustrated poster can be customized with your child’s name at no extra cost, and the imagery isn’t so baby-ish that he’ll outgrow it immediately (unlike so many other things you buy when they’re young).
Wooden Toy – Personalized Bowling Set – Natural Wood Toy – Waldorf Toddler Toy
by hcwoodcraft on Etsy
Full disclosure: I could very well be partial to this bowling set because my son’s name is Jack. The natural-wood toy, personalized with any name (up to six letters long) and finished with beeswax, will keep those cold winter afternoons when you’re all stuck indoors rolling right along.';
		$product_description[] = 'In my dreams, I am an amazing knitter. In the real world, I can manage a scarf (and that’s about it). Silver Maple Knits creates the kinds of kids’ clothes — like this soft, holiday-ready red sweater — that I see in my mind but know I will never finish on my own.


Kids’ BUNNY RABBIT CRAYONS – Birthday Party Pack of 20 Bunnies – Eco-Friendly Toys Favors in Assorted Colors – For Boys and Girls
by ivylanedesigns on Etsy
It’s not just about aesthetics: these animal-shaped handmade crayons (sold in packs of 20) are actually easier for small fingers to grasp. Pair them with a big roll of paper so your budding artist can really go wild!


Tiger Carnival Costume Kids Tiger Mask and Tail Childrens Eco Friendly Toddler Animal Dress up Pretend Play Felt Toy
by BHBKidstyle on Etsy
What kid doesn’t like to dress up? These funny felt masks will add a little more oomph to the playtime proceedings. (And if a tiger is too scary, there are monkey and panda versions to choose from, too.)';
		$product_description[] = 'Tiny shoes are invariably adorable, but this fringed pair, handmade in Paris from leather and cotton, really kicks the cuteness up a notch.


Winter Holiday Crown – Birthday – Christmas – Party – Dress up – Play – Felted Wool Headband
by sqrlbee on Etsy
A truly enchanting accessory for the little fairy in your life.


Lion Tammer – Circus balancing wooden toy
by WatermelonCatCompany on Etsy
I can just hear the kids roaring while they play with this set — but, thankfully, not over who gets to be the lion, since there are three to choose from.


Hopscotch Mat
by CoolSpacesForKids on Etsy
Such a cool idea: a felt hopscotch mat you can unfurl anywhere (and roll up again for easy storage). Win, win!';
		$product_description[] = 'An Australian artist draws these charmingly original download-and-print finger puppets — giving your kids hours of backseat entertainment for less than five bucks. They’ll be the smash hit of the stocking stuffers.


Plush Huggable Toy Cushion – Little Himalaya 1
by littleoddforest on Etsy
Is one of your wee ones transitioning to a big bed? Celebrate the milestone (and cultivate a sense of adventure) with this happy little trio of plush peaks.


Maisie – Eco Friendly – Natural – Felted Wool – Baby Mobile
by sqrlbee on Etsy
A mobile with a modern edge: this simple wool-felt model brings a joyful burst of color without any unwanted kitsch (and it works just as well for a boy’s room as for a girl’s).';
		$product_description[] = 'As the Style Director for Country Living, Jami Supsic travels to rural locations to direct home and lifestyle shoots; scours markets (both flea and web) seeking great finds to share with readers; and helps plan covers for every issue. When not at work, she can be found renovating her 1920s bungalow in Birmingham, Alabama.

Seasonal decor, like any other collection, is best accumulated over time (after all, buying every last thing in bulk at the post-holiday sales just doesn’t yield the same lived-in, memory-filled charm). To help you add some handmade appeal to your usual array of decorations, we’ve rounded up some special pieces from Etsy sellers to incorporate throughout your home — from a plush plaid reindeer trophy to a festive, not-quite-Fair-Isle pillow cover. Order a few now — and bookmark some for next year, too. (You’ll thank us later.)';
		$product_description[] = '‘Tis the season for warm winter drinks (as any PSL-obsessed pal has surely already informed you). Fortunately, shopping for holiday gifts for a coffee or tea aficionado comes with one very convenient perk: the options are — to use any java junkie’s favorite word — bottomless. From ceramic mugs to stoneware tea pots, cherry-wood serving trays to leather sleeves for mason jars, you’re bound to find a gift even the most in-the-know brew buff has yet to get her hands on. Caffeine not your cup of tea? No matter. You can get your fix (or give one) with a candle scented like dark-roast beans; a bottle of Earl Grey aftershave; or a fragrance inspired by Chinese black tea. Jump, jive, see what’s for sale.';
		$product_description[] = 'Give the globe-trotting coffee lover in your life the opportunity to sample four freshly roasted blends from across the planet — right in the comfort of her kitchen.


Earl Brulee Organic Black Tea, Cream Earl Grey, loose leaf, 2oz (57g)
by Amitea on Etsy

Satisfy a sweet tooth with an organic loose-leaf tea that blends Earl Grey with hints of vanilla and caramel.


Grow Your Own Fragrant Green Tea Plant Kit
by PlantsFromSeed on Etsy

This five-piece kit gives aspiring gardeners the tools to cultivate a green thumb and green tea in one go.


Stocking Stuffer -Coffee Christmas Favors. Set of three freshly roasted “one pot” packs.
by AproposRoasters on Etsy';
		$product_description[] = 'This sleepy fox makes an adorable addition to any tea drinker’s table — his eyes may be closed, but rest assured, he’ll keep the sugar safely stowed.


Reclaimed Wood Coffee Dripper Pour Over Brewer Stand
by natemadegoods on Etsy

Reclaimed barn wood gives every one of these handmade stands — great for design-conscious drip drinkers — its own unique charm.


Exclusive White Porcelain Mug Decorated with Real Gold
by KinaCeramicDesign on Etsy

Perk up in punk-rock style with a mug covered in glimmering gold spikes. (Don’t fret: They’re less prickly than they look.)


Leather Mug Sleeve – Mason Jar
by MyriadSupply on Etsy
';
		$product_description[] = 'Accessories for Enthusiasts

Caffeine Science Chalkboard Notebook for Coffee Lovers. Plain Pages. Scientific Stationery for Geeks. Chemistry Black, UK Printed.
by NewtonAndTheApple on Etsy

Wannabe scientists will love this recycled-paper notebook, which features a drawing of a caffeine molecule and a reference to the year tea was first discovered.


Lapsang Souchong Tea Fragrance – Oil Fragrance, 10 ml
by RavensCtApothecary on Etsy

This fragrance, inspired by smoky Lapsang Souchong tea, is made in a London apothecary from tea leaves steeped in oil. Give it to a cosmetics connoisseur who’s seen it all (or thinks she has, anyway).';
		$product_description[] = 'Know someone who’s recently bid adieu to his Movember mustache? Wrap up a bottle of this Earl Grey Chamomile aftershave to aid in the recovery.


Brooklyn Roast Scented Mason Jar Candle 100% Soy – Vintage Style Apothecary Label – Rustic Bohemian Hand-poured Handmade – Lovely Gift
by brooklyncandlestudio on Etsy

Fill any room with the intoxicating aroma of coffee, cinnamon, and clove, courtesy of this candle that’s made in Brooklyn and packaged in — what else? — a mason jar.';
		$product_description[] = 'Ethan Song is the co-founder and CEO of Frank & Oak, a Montreal-based lifestyle brand and retailer for men. In partnership with Etsy, Frank & Oak recently curated a collection of handmade housewares and home decor by four Etsy vendors, some of which are featured below.

I’ve always needed some kind of creative outlet. Growing up, I took dance and acting lessons, and I even tried my hand at directing for a while. Now, with my company Frank & Oak, I get to work with a design team every day. When we were starting our business, my co-founder and I knew we wanted to build a creative community and to inspire self-expression and entrepreneurship. And what better way to encourage creative small businesses than by supporting independent artists? The items below, in addition to making excellent gifts (for a friend or for yourself), allow you to do just that.

';

		$product_support_content[] = 'My name is Kayla. I’m a designer, momma of two, and the magic behind Fox and the Fawn, where I create unique and dainty jewelry. I’m originally from the gorgeous moss and pine-covered region of western Washington, but I currently live in Las Vegas, Nevada. I work out of my small home studio with my furry assistant Luna by my side.';
		$product_support_content[] = 'My inspiration comes from my upbringing in the forest, where my heart resides. Memories of chasing streams with frogs and newts, surrounded by mountains with soaring birds overhead, fill my thoughts. I grew up immersed in the Tlingit culture; my mother has run her own Tlingit indigenous art business my whole life. She’s also a storyteller, weaver, dancer and jeweler. I learned the art of business from her.';
		$product_support_content[] = 'Fox and the Fawn came to life shortly after my husband returned from his first deployment. I spent the whole time he was away telling myself I was going to jump in and create something for myself; my mother-in-law (who runs L and S Arts) told me to go for it. Once my husband came back, I invested in a small torch and began teaching myself the ropes with help from old books.';
		$product_support_content[] = 'My process is simplistic. I keep sketchbooks scattered throughout my house, and I always have one on me. I love the idea of finding a design that’s completely organic; twigs and birds are some of my favorite things to replicate. I often make a design five to ten times before it becomes part of my line. Once the design is set, I sketch down a recipe to keep, just in case I forget.';
		$product_support_content[] = 'Mandy Pellegrin is an avid DIYer. She documents her crafty projects on Fabric Paper Glue and brings fun crafting experiences to the fine folks of Nashville as half of local business Craftcourse. When she’s not blogging or throwing craft parties, you’ll find her doing health policy research (i.e. her “day job”), fixing up the 1935 home that she shares with her husband Michael and cat Pete, or just looking for a good time with friends.';
		$product_support_content[] = 'Hands down, my favorite part of the holidays is wrapping gifts. Every year, I try my hardest to come up with some clever, new way to wrap my friends’ and families’ presents. This project may be my most clever to date (if I do say so myself!) – using plain and even recycled kraft paper bags to create the cutest little holiday village display. It’s a perfect, non-traditional way to wrap gifts and would even make a great activity to do with the kids.';
		$product_support_content[] = 'You will need:
Kraft gift and/or grocery bags in various sizes
Ruler
Pencil
Scissors
Black marker
Craft paints in white, green, and red
Paint brushes
Ribbon
Craft glue';
		$product_support_content[] = 'When it comes to giving your favorite kitchen enthusiast a gift to remember, I’m a big believer in keeping it useful, beautiful and unique. There’s nothing better than pulling out my trusty measuring cups or mixing bowls and thinking of the loved ones who gave them to me. To help you find the kinds of gifts that will have someone fondly picturing your face as they roll out a perfect, personalized pie crust or prepare a steaming cup of pour-over coffee, I’ve gathered some of my favorite cook’s tools on Etsy. Each one is equally covetable and giftable — especially when you present them in A Better Bag™, designed by Etsy seller Hillary Bird. Keep an eye out for them the next time you’re shopping in Whole Foods Market!';
		$product_support_content[] = 'In case you missed our recent rundowns of new and noteworthy jewelry designers, crystals in every color of the rainbow, and incredible, investment-worthy Victorian keepsakes — or you just can’t get enough of Etsy’s almost-endless supply of glittering gems and funky statement necklaces — we’ve rounded up a few more suggestions for searching out just-right jewelry items (starting at a mere $25!) for everyone on your list.';
		$product_support_content[] = 'Our work is heavily inspired by geometric shapes and imperfect strokes. All our patterns begin on paper, and the design process is usually very free-form. I like to fill pages with markings and repetitive shapes without thinking about the finished composition. Once the designs have been finalized and scaled, we expose the images onto a silkscreen and manually pull each print onto fabric using hand-mixed inks. We both sew, and all our pieces are made in-house from start to finish.

';
		$product_support_content[] = 'I originally opened an Etsy shop to share my paper goods, but I quickly realized that I wanted to create patterns for a wider range of wares. Both Chris and I have backgrounds in business management, and I spent the last three years working for Paper Source. I was lucky to have a day job that was saturated with the idea of making, teaching, and creating something new every day. That job helped me develop stronger business skills while kicking my creativity into overdrive.';
		$product_support_content[] = 'After our first craft show in 2012, Chris and I couldn’t stop thinking about how much fun we’d had. We continued to attend local craft shows and focused on perfecting our process. By the end of the year, we were getting inundated with emails and Instagram messages from people asking where they could find our stuff. When I left my full-time job in January, we still didn’t have an online presence, and it wasn’t until March that we launched our line on Etsy. I’m sure that seems backward to a lot of people; at the very least it was risky, but we’re kind of big risk takers over here.';

		$product_highlight_text[] = 'Explore charming mid-century ceramics, glitter-encrusted cardboard villages, and the cutest Swedish gnomes from vintage sellers on Etsy.';
		$product_highlight_text[] = 'Learn the history of this holiday icon — and get tips for constructing your own extra-special version.';
		$product_highlight_text[] = 'Discover unique items from Etsy designers in boutiques near you — plus inspiring cafes, bars, and more — with our handy guides.';
		$product_highlight_text[] = 'Add glamor and glitz to your New Year’s Eve ensemble with HelloGiggles’ favorite party accessories.';
		$product_highlight_text[] = 'Whip up a sweet garland of smiling dough ornaments with this tutorial by Heather Baird.';
		$product_highlight_text[] = 'Discover unique items from Etsy designers in boutiques near you — plus inspiring cafes, bars, and more — with our handy guides.';
		$product_highlight_text[] = 'Looking for some tiny gifts with big heart? We scoured the site for the best little ways to show you care.';
		$product_highlight_text[] = 'A charming log-shaped mold triggers an exploration into holiday history and how the traditional Yule log morphed into a frosted treat.';
		$product_highlight_text[] = 'Fifteen finds to put the biggest smiles on the littlest faces this holiday season, as chosen by the editors of mom.me.';
		$product_highlight_text[] = 'The editors at Country Living magazine share their favorite Etsy finds for holiday decorating.';
		$product_highlight_text[] = 'Developing personal relationships is a large part of how we like to do business.';

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

		$discount_percentage[] = 0;
		$discount_percentage[] = 5;
		$discount_percentage[] = 10;
		$discount_percentage[] = 15;
		$discount_percentage[] = 20;
		$discount_percentage[] = 25;
		$discount_percentage[] = 30;
		$discount_percentage[] = 35;
		$discount_percentage[] = 40;
		$discount_percentage[] = 45;
		$discount_percentage[] = 50;

		$notes[] = 'Your Product successfully delivered';
		$notes[] = 'Product added.';
		$notes[] = 'new product has been added.';
		$notes[] = 'Some changes made in stock details.';
		$notes[] = 'Notes to admin
Notes to admin
Notes to adminNotes to admin
Notes to admin
Notes to admin
Notes to admin
Notes to admin';
		$notes[] = 'Free product has been added to list.';
		$notes[] = 'Downloadable product added. ';
		$notes[] = 'Please check the details and publish it';
		$notes[] = 'Product added pls check and approve.';
		$notes[] = 'Product images updated & published by admin.';
		$notes[] = 'Image updated & published by admin';
		$notes[] = 'Kindly publish my product asap ';

		$countries[] = array('id' => 98, 'zip_code' => 600034);
		$countries[] = array('id' => 98, 'zip_code' => 600020);
		$countries[] = array('id' => 98, 'zip_code' => 600001);
		$countries[] = array('id' => 98, 'zip_code' => 600041);
		$countries[] = array('id' => 98, 'zip_code' => 600017);
		$countries[] = array('id' => 98, 'zip_code' => 613001);
		$countries[] = array('id' => 98, 'zip_code' => 612501);
		$countries[] = array('id' => 98, 'zip_code' => 620001);
		$countries[] = array('id' => 98, 'zip_code' => 641001);
		$countries[] = array('id' => 115, 'zip_code' => 10732);
		$countries[] = array('id' => 115, 'zip_code' => 11020);
		$countries[] = array('id' => 153, 'zip_code' => 54000);

		for ($product_id = 1; $product_id <= 1000; $product_id++) {

			$products = $product_log = $product_price_groups = $product_image = array();
			$user_products_wishlist = $product_taxations = $reported_products = $product_package_details = $product_comments = $user_cart = array();

			//product details
			$products['id'] = $product_id;
			$products['product_code'] = 'P'.str_pad($product_id, 6, '0', STR_PAD_LEFT);
			$products['product_name'] = ucwords($product_name[array_rand($product_name, 1)]).'  '.ucwords($product_name_suffix[array_rand($product_name_suffix, 1)]);
			$products['product_description'] = $product_description[array_rand($product_description, 1)];
			$products['product_support_content'] = $product_support_content[array_rand($product_support_content, 1)];
			$products['meta_title'] = 'Meta Title of '.$products['product_name'];
			$products['meta_keyword'] = 'Meta, Keywords, '.$products['product_name'];
			$products['meta_description'] = 'Meta description of '.$products['product_name'];
			$products['product_highlight_text'] = $product_highlight_text[array_rand($product_highlight_text, 1)];
			$products['product_slogan'] = '';
			$products['purchase_price'] = '0';
			$products['product_price'] = $products['product_price_usd'] = $price[array_rand($price, 1)];
			$products['product_price_currency'] = 'USD';
			$products['product_user_id'] = (rand(1, 100) * 10);
			$products['product_sold'] = 0;
			$products['product_added_date'] = $now;
			$products['url_slug'] = $this->slugify($products['product_name'].'-'.$product_id);
			$products['product_category_id'] = rand(2, 161);
			$products['product_tags'] = str_replace(' ', ', ', $products['product_name']);
			$products['total_views'] = 0;
			$products['is_featured_product'] = 'No';
			$products['featured_product_expires'] = '0000-00-00 00:00:00';
			$products['is_user_featured_product'] = 'No';
			$products['date_activated'] = date('Y-m-d H:i:s');
			$products['product_discount_price'] = '0.00';
			$products['product_discount_price_usd'] = '0.00';
			$products['product_discount_fromdate'] = '0000-00-00';
			$products['product_discount_todate'] = '0000-00-00';
			$products['product_preview_type'] = 'image';
			$products['is_free_product'] = 'No';
			$products['last_updated_date'] = date('Y-m-d H:i:s');
			$products['total_downloads'] = 0;
			$products['product_moreinfo_url'] = '';
			$products['global_transaction_fee_used'] = 'Yes';
			$products['site_transaction_fee_type'] = 'Flat';
			$products['site_transaction_fee'] = '0.00';
			$products['site_transaction_fee_percent'] = '0.00';
			$products['is_downloadable_product'] = 'No';
			$products['user_section_id'] = '0';
			$products['delivery_days'] = '0';
			$products['date_expires'] = date('Y-m-d H:i:s', strtotime('90 days'));
			$products['default_orig_img_width'] = '0';
			$products['default_orig_img_height'] = '0';
			$products['product_status'] = 'Ok';
			if (!($product_id%30)) {
				$products['product_status'] = 'Draft';
			}
			if (!($product_id%45)) {
				$products['product_status'] = 'ToActivate';
			}
			if (!($product_id%75)) {
				$products['product_status'] = 'Deleted';
			}
			$products['use_cancellation_policy'] = 'Yes';
			$products['use_default_cancellation'] = 'Yes';
			if (!($product_id%12)) {
				$products['use_cancellation_policy'] = 'No';
				$products['use_default_cancellation'] = 'No';
			}
			$products['cancellation_policy_text'] = '';
			$country = $countries[array_rand($countries, 1)];
			$products['shipping_from_country'] = $country['id'];
			$products['shipping_from_zip_code'] = $country['zip_code'];
			$products['shipping_template'] = ($products['product_user_id'] / 10);
			if (!($product_id%8)) {
				$products['is_downloadable_product'] = 'Yes';
				$products['product_status'] = 'Draft';
				$products['shipping_from_country'] = '';
				$products['shipping_from_zip_code'] = '';
				$products['shipping_template'] = ($products['product_user_id'] / 10);
			}

			//product_log details
			$product_log['id'] = NULL;
			$product_log['product_id'] = $product_id;
			$product_log['date_added'] = date('Y-m-d H:i:s');
			$product_log['added_by'] = 'User';
			$product_log['user_id'] = $products['product_user_id'];
			$product_log['notes'] = $notes[array_rand($notes, 1)];

			//seller_request details
			if (!($product_id%25)) {
				$products['is_free_product'] = 'Yes';
				$products['product_price'] = $products['product_price_usd'] = '';
			} else {
				//product_price_groups details
				$product_price_groups['price_group_id'] = NULL;
				$product_price_groups['product_id'] = $product_id;
				$product_price_groups['group_id'] = 0;
				$product_price_groups['range_start'] = 1;
				$product_price_groups['range_end'] = '-1';
				$product_price_groups['currency'] = 'USD';
				$product_price_groups['price'] = $product_price_groups['price_usd'] = $products['product_price'];
				$product_price_groups['discount_percentage'] = $discount_percentage[array_rand($discount_percentage, 1)];;
				$product_price_groups['discount'] = $product_price_groups['discount_usd'] = round($product_price_groups['price'] - ($product_price_groups['price'] * $product_price_groups['discount_percentage'] / 100), 2);
				$product_price_groups['added_on'] = date('Y-m-d H:i:s');
			}

			$product_stocks['id'] = NULL;
			$product_stocks['product_id'] = $product_id;
			$product_stocks['quantity'] = rand(5, 10);
			$product_stocks['serial_numbers'] = '';
			$product_stocks['date_updated'] = $now;


			//product_image details
			$product_image['id'] = NULL;
			$product_image['product_id'] = $product_id;

			DB::table('product')->insert($products);
			DB::table('product_price_groups')->insert($product_price_groups);
			DB::table('product_log')->insert($product_log);
			DB::table('product_stocks')->insert($product_stocks);
			DB::table('product_image')->insert($product_image);

			// User product wish list entry
			for($i=0; $i<=rand(1, 25); $i++)
			{
				$user_products_wishlist['user_id'] = (rand(1, 100));
				$user_products_wishlist['product_id'] = $product_id;
				DB::table('user_products_wishlist')->insert($user_products_wishlist);
			}
			// product_taxations details
			$product_taxations['taxation_id'] = rand(1, 10); // Fetch taxation id from taxation table.
			$product_taxations['product_id'] = $product_id;
			$product_taxations['user_id'] = $products['product_user_id'];
			$product_taxations['tax_fee'] = 0;
			$product_taxations['fee_type'] = 'percentage'; // 'percentage', 'flat'
			DB::table('product_taxations')->insert($product_taxations);

			if (!($product_id%25))
			{
			// reported_products entry
				$reported_products['product_id'] = $product_id;
				$reported_products['user_id'] = $products['product_user_id'];
				$reported_products['report_thread'] = $report_thread[array_rand($report_thread, 1)];
				$reported_products['custom_message'] = 'Custom message for '. $reported_products['report_thread'];
				DB::table('reported_products')->insert($reported_products);
			}
			// product_package_details entry
			if($products['is_downloadable_product'] != 'Yes')
			{
				$product_package_details['product_id'] = $product_id;
				$product_package_details['weight'] = rand(10, 25);
				$product_package_details['length'] = rand(10, 25);
				$product_package_details['width'] = rand(10, 25);
				$product_package_details['height'] = rand(10, 25);
				$product_package_details['custom'] = '';
				$product_package_details['first_qty'] = 0;
				$product_package_details['additional_qty'] = 0;
				$product_package_details['additional_weight'] = 0;
				DB::table('product_package_details')->insert($product_package_details);
			}

			if($products['product_status'] == 'Ok')
			{
				for($i=0; $i<=rand(1, 15); $i++)
				{
					$product_comments['product_id'] = $product_id;
					$product_comments['user_id'] = rand(1, 100);
					$product_comments['comment_id'] = null;
					$product_comments['comments'] = 'Comment for '.$products['product_name'];
					$product_comments['status'] = (!($product_id%50)) ? 'InActive': 'Active';
					DB::table('product_comments')->insert($product_comments);
				}


				$user_cart['user_id'] = rand(1, 100);
				$user_cart['item_id'] = $product_id;
				$user_cart['item_owner_id'] = $products['product_user_id'];
				$user_cart['qty'] = rand(1,5);
				$user_cart['date_added'] = $now;
				$user_cart['cookie_id'] ='';
				DB::table('user_cart')->insert($user_cart);
				
				/*
				$shop_order['buyer_id']= rand(1, 100);
				$shop_order['seller_id']= $products['product_user_id'];
				$shop_order['sub_total']= '';
				$shop_order['discount_amount']= '';
				$shop_order['total_amount']= $product_price_groups['discount'];
				$shop_order['coupon_code']= '';
				$shop_order['tax_fee']= rand(1, 10);
				$shop_order['shipping_fee']= rand(1, 10);
				$shop_order['site_commission']= $product_price_groups['discount'] * 5/100;
				$shop_order['currency']= 'USD';
				$shop_order['order_status']= 'payment_completed';
				$shop_order['set_as_paid_notes']= '';
				$shop_order['set_as_paid_transaction_type']= '';
				$shop_order['set_as_paid_amount']= '';
				$shop_order['shipping_tracking_id']= '';
				$shop_order['pay_key']= '';
				$shop_order['tracking_id']= '';
				$shop_order['payment_status']= '';
				$shop_order['payment_response']= '';
				$shop_order['payment_gateway_type']= '';
				$shop_order['set_as_delivered']= '';
				$shop_order['delivered_date']= '';
				$shop_order['deal_tipping_status']= '';
				DB::table('shop_order')->insert($shop_order);

				$shop_order_item['order_id']
				$shop_order_item['item_id']
				$shop_order_item['buyer_id']
				$shop_order_item['item_owner_id']
				$shop_order_item['item_amount']
				$shop_order_item['item_qty']
				$shop_order_item['shipping_company']
				$shop_order_item['shipping_fee']
				$shop_order_item['total_tax_amount']
				$shop_order_item['tax_ids']
				$shop_order_item['tax_amounts']
				$shop_order_item['services_amount']
				$shop_order_item['total_amount']
				$shop_order_item['discount_ratio_amount']
				$shop_order_item['service_ids']
				$shop_order_item['item_type']
				$shop_order_item['site_commission']
				$shop_order_item['seller_amount']
				$shop_order_item['shipping_tracking_id']
				$shop_order_item['shipping_stock_country']
				$shop_order_item['shipping_serial_number']
				$shop_order_item['shipping_company_name']
				$shop_order_item['shipping_status']
				$shop_order_item['shipping_date']
				$shop_order_item['delivered_date']
				$shop_order_item['date_added']
				$shop_order_item['deal_id']
				$shop_order_item['deal_tipping_status']
				DB::table('shop_order_item')->insert($shop_order_item);
				*/
			}
		}
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