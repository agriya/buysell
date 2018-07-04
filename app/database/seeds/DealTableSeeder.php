<?php

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;


class DealTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('deal')->truncate();
		DB::table('deal_featured')->truncate();
		DB::table('deal_featured_request')->truncate();
		DB::table('deal_item')->truncate();
		DB::table('deal_item_purchased_details')->truncate();

		$now = date('Y-m-d H:i:s');

		$deal_name = array('Kitty lights v3', 'ChristmasRecycled Haitian Metal', 'Handmade Soap - Frosty - Light', 'Hand knit toddler earflap hat', 'T-Shirt - Sweet dreams - Little', 'Owl - Print - 8 x 10', 'Set of 2 Hanging Mason Jar Glass', 'Vegetable Tan Leather Key', 'HUGS & KISSES stamp with border', 'Unique Marimo Moss Ball Light', 'Bright Spark Pom Pom Studs', 'A4-Original fine art ink drawing', 'Metal TCG Deck Box for Mtg', 'Vintage granny squares afghan', 'The Welder did it Maternity Shirt', 'Vintage Letter Cubes FOUND', 'Good Day, Bad Day, Jack Daniel', 'A Single Wish Dandelion Seed', 'Kitty butt baby skinny pants', 'Migrations - 2015 calendar', 'ROSE GOLD over Sterling Silver', 'Makeup bag - Floral clutch', 'Real Dandelion Necklace');
		$deal_name_suffix = array('Gold variant', 'Silver variant', 'Platinum variant', 'Black', 'White', 'Green', 'Steel', 'New', 'Old', 'Middle', 'Daily', 'Kids', 'Excellent', 'Furnished', '3BHK', 'Tools', 'Made', 'Make', 'Get Soon', 'Order Quickly', 'Fine quality', 'Good Condition');
		$deal_description[] = 'Cerentha Harris is editor-in-chief of mom.me – a national lifestyle parenting website that covers every stage of motherhood from trying to conceive right through to the empty nest. She lives in Los Angeles with her 9-year-old daughter, a 12-year-old son, a crazy Wheaten terrier and a husband who is an awesome cook.

December has well and truly arrived. And we know the holiday season can be a stressful time. There’s so much to do! But it is also such a lovely time of year – trees to be trimmed, cookies to be baked, snowballs to be thrown, and that wonderful infectious excitement that keeps kids (and, let’s be honest, some moms) up at night. Hopefully this list will help you enjoy the good times by making shopping for the kids’ gifts just that little bit easier.';
		$deal_description[] = 'These organic-cotton deer leggings are some of the cutest we’ve seen (aside from this shop’s ice cream cone-printed style, that is); they’ll look great with any solid-colored onesie.


ModernPOP, Custom Nursery Alphabet Art, A-Z Alphabet Print, Alphabet Letters, Boy Alphabet , Girl Alphabet, Toddler Art
by ModernPOP on Etsy
Modern POP’s beautifully illustrated poster can be customized with your child’s name at no extra cost, and the imagery isn’t so baby-ish that he’ll outgrow it immediately (unlike so many other things you buy when they’re young).
Wooden Toy – Personalized Bowling Set – Natural Wood Toy – Waldorf Toddler Toy
by hcwoodcraft on Etsy
Full disclosure: I could very well be partial to this bowling set because my son’s name is Jack. The natural-wood toy, personalized with any name (up to six letters long) and finished with beeswax, will keep those cold winter afternoons when you’re all stuck indoors rolling right along.';
		$deal_description[] = 'In my dreams, I am an amazing knitter. In the real world, I can manage a scarf (and that’s about it). Silver Maple Knits creates the kinds of kids’ clothes — like this soft, holiday-ready red sweater — that I see in my mind but know I will never finish on my own.


Kids’ BUNNY RABBIT CRAYONS – Birthday Party Pack of 20 Bunnies – Eco-Friendly Toys Favors in Assorted Colors – For Boys and Girls
by ivylanedesigns on Etsy
It’s not just about aesthetics: these animal-shaped handmade crayons (sold in packs of 20) are actually easier for small fingers to grasp. Pair them with a big roll of paper so your budding artist can really go wild!


Tiger Carnival Costume Kids Tiger Mask and Tail Childrens Eco Friendly Toddler Animal Dress up Pretend Play Felt Toy
by BHBKidstyle on Etsy
What kid doesn’t like to dress up? These funny felt masks will add a little more oomph to the playtime proceedings. (And if a tiger is too scary, there are monkey and panda versions to choose from, too.)';
		$deal_description[] = 'Tiny shoes are invariably adorable, but this fringed pair, handmade in Paris from leather and cotton, really kicks the cuteness up a notch.


Winter Holiday Crown – Birthday – Christmas – Party – Dress up – Play – Felted Wool Headband
by sqrlbee on Etsy
A truly enchanting accessory for the little fairy in your life.


Lion Tammer – Circus balancing wooden toy
by WatermelonCatCompany on Etsy
I can just hear the kids roaring while they play with this set — but, thankfully, not over who gets to be the lion, since there are three to choose from.


Hopscotch Mat
by CoolSpacesForKids on Etsy
Such a cool idea: a felt hopscotch mat you can unfurl anywhere (and roll up again for easy storage). Win, win!';
		$deal_description[] = 'An Australian artist draws these charmingly original download-and-print finger puppets — giving your kids hours of backseat entertainment for less than five bucks. They’ll be the smash hit of the stocking stuffers.


Plush Huggable Toy Cushion – Little Himalaya 1
by littleoddforest on Etsy
Is one of your wee ones transitioning to a big bed? Celebrate the milestone (and cultivate a sense of adventure) with this happy little trio of plush peaks.


Maisie – Eco Friendly – Natural – Felted Wool – Baby Mobile
by sqrlbee on Etsy
A mobile with a modern edge: this simple wool-felt model brings a joyful burst of color without any unwanted kitsch (and it works just as well for a boy’s room as for a girl’s).';
		$deal_description[] = 'As the Style Director for Country Living, Jami Supsic travels to rural locations to direct home and lifestyle shoots; scours markets (both flea and web) seeking great finds to share with readers; and helps plan covers for every issue. When not at work, she can be found renovating her 1920s bungalow in Birmingham, Alabama.

Seasonal decor, like any other collection, is best accumulated over time (after all, buying every last thing in bulk at the post-holiday sales just doesn’t yield the same lived-in, memory-filled charm). To help you add some handmade appeal to your usual array of decorations, we’ve rounded up some special pieces from Etsy sellers to incorporate throughout your home — from a plush plaid reindeer trophy to a festive, not-quite-Fair-Isle pillow cover. Order a few now — and bookmark some for next year, too. (You’ll thank us later.)';
		$deal_description[] = '‘Tis the season for warm winter drinks (as any PSL-obsessed pal has surely already informed you). Fortunately, shopping for holiday gifts for a coffee or tea aficionado comes with one very convenient perk: the options are — to use any java junkie’s favorite word — bottomless. From ceramic mugs to stoneware tea pots, cherry-wood serving trays to leather sleeves for mason jars, you’re bound to find a gift even the most in-the-know brew buff has yet to get her hands on. Caffeine not your cup of tea? No matter. You can get your fix (or give one) with a candle scented like dark-roast beans; a bottle of Earl Grey aftershave; or a fragrance inspired by Chinese black tea. Jump, jive, see what’s for sale.';
		$deal_description[] = 'Give the globe-trotting coffee lover in your life the opportunity to sample four freshly roasted blends from across the planet — right in the comfort of her kitchen.


Earl Brulee Organic Black Tea, Cream Earl Grey, loose leaf, 2oz (57g)
by Amitea on Etsy

Satisfy a sweet tooth with an organic loose-leaf tea that blends Earl Grey with hints of vanilla and caramel.


Grow Your Own Fragrant Green Tea Plant Kit
by PlantsFromSeed on Etsy

This five-piece kit gives aspiring gardeners the tools to cultivate a green thumb and green tea in one go.


Stocking Stuffer -Coffee Christmas Favors. Set of three freshly roasted “one pot” packs.
by AproposRoasters on Etsy';
		$deal_description[] = 'This sleepy fox makes an adorable addition to any tea drinker’s table — his eyes may be closed, but rest assured, he’ll keep the sugar safely stowed.


Reclaimed Wood Coffee Dripper Pour Over Brewer Stand
by natemadegoods on Etsy

Reclaimed barn wood gives every one of these handmade stands — great for design-conscious drip drinkers — its own unique charm.


Exclusive White Porcelain Mug Decorated with Real Gold
by KinaCeramicDesign on Etsy

Perk up in punk-rock style with a mug covered in glimmering gold spikes. (Don’t fret: They’re less prickly than they look.)


Leather Mug Sleeve – Mason Jar
by MyriadSupply on Etsy
';
		$deal_description[] = 'Accessories for Enthusiasts

Caffeine Science Chalkboard Notebook for Coffee Lovers. Plain Pages. Scientific Stationery for Geeks. Chemistry Black, UK Printed.
by NewtonAndTheApple on Etsy

Wannabe scientists will love this recycled-paper notebook, which features a drawing of a caffeine molecule and a reference to the year tea was first discovered.


Lapsang Souchong Tea Fragrance – Oil Fragrance, 10 ml
by RavensCtApothecary on Etsy

This fragrance, inspired by smoky Lapsang Souchong tea, is made in a London apothecary from tea leaves steeped in oil. Give it to a cosmetics connoisseur who’s seen it all (or thinks she has, anyway).';
		$deal_description[] = 'Know someone who’s recently bid adieu to his Movember mustache? Wrap up a bottle of this Earl Grey Chamomile aftershave to aid in the recovery.


Brooklyn Roast Scented Mason Jar Candle 100% Soy – Vintage Style Apothecary Label – Rustic Bohemian Hand-poured Handmade – Lovely Gift
by brooklyncandlestudio on Etsy

Fill any room with the intoxicating aroma of coffee, cinnamon, and clove, courtesy of this candle that’s made in Brooklyn and packaged in — what else? — a mason jar.';
		$deal_description[] = 'Ethan Song is the co-founder and CEO of Frank & Oak, a Montreal-based lifestyle brand and retailer for men. In partnership with Etsy, Frank & Oak recently curated a collection of handmade housewares and home decor by four Etsy vendors, some of which are featured below.

I’ve always needed some kind of creative outlet. Growing up, I took dance and acting lessons, and I even tried my hand at directing for a while. Now, with my company Frank & Oak, I get to work with a design team every day. When we were starting our business, my co-founder and I knew we wanted to build a creative community and to inspire self-expression and entrepreneurship. And what better way to encourage creative small businesses than by supporting independent artists? The items below, in addition to making excellent gifts (for a friend or for yourself), allow you to do just that.

';

		$deal_highlight_text[] = 'Explore charming mid-century ceramics, glitter-encrusted cardboard villages, and the cutest Swedish gnomes from vintage sellers on Etsy.';
		$deal_highlight_text[] = 'Learn the history of this holiday icon — and get tips for constructing your own extra-special version.';
		$deal_highlight_text[] = 'Discover unique items from Etsy designers in boutiques near you — plus inspiring cafes, bars, and more — with our handy guides.';
		$deal_highlight_text[] = 'Add glamor and glitz to your New Year’s Eve ensemble with HelloGiggles’ favorite party accessories.';
		$deal_highlight_text[] = 'Whip up a sweet garland of smiling dough ornaments with this tutorial by Heather Baird.';
		$deal_highlight_text[] = 'Discover unique items from Etsy designers in boutiques near you — plus inspiring cafes, bars, and more — with our handy guides.';
		$deal_highlight_text[] = 'Looking for some tiny gifts with big heart? We scoured the site for the best little ways to show you care.';
		$deal_highlight_text[] = 'A charming log-shaped mold triggers an exploration into holiday history and how the traditional Yule log morphed into a frosted treat.';
		$deal_highlight_text[] = 'Fifteen finds to put the biggest smiles on the littlest faces this holiday season, as chosen by the editors of mom.me.';
		$deal_highlight_text[] = 'The editors at Country Living magazine share their favorite Etsy finds for holiday decorating.';
		$deal_highlight_text[] = 'Developing personal relationships is a large part of how we like to do business.';

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

		for ($deal_id = 1; $deal_id <= 500; $deal_id++)
		{
			$deal = $deal_featured = $deal_featured_request = $deal_item = $deal_item_purchased_details = array();

			$deal['deal_id'] = $deal_id;
			$deal['user_id'] = (rand(1, 100) * 10);
			$deal['deal_title'] = ucwords($deal_name[array_rand($deal_name, 1)]).'  '.ucwords($deal_name_suffix[array_rand($deal_name_suffix, 1)]);
			$deal['url_slug'] = $this->slugify($deal['deal_title'].'-'.$deal_id);
			$deal['deal_short_description'] = $deal_highlight_text[array_rand($deal_highlight_text, 1)];
			$deal['deal_description'] = $deal_description[array_rand($deal_description, 1)];
			$deal['meta_title'] = 'Meta Title of '.$deal['deal_title'];
			$deal['meta_keyword'] = 'Meta, Keywords, '.$deal['deal_title'];
			$deal['meta_description'] = 'Meta description of '.$deal['deal_title'];
			$deal['img_name'] = '';
			$deal['img_ext'] = '';
			$deal['img_width'] = $deal['img_height'] = $deal['l_width'] = $deal['l_height'] = $deal['t_width'] = $deal['t_height'] = 0;
			$deal['server_url'] = '';
			$deal['discount_percentage'] = $discount_percentage[array_rand($discount_percentage, 1)];
			$deal['date_deal_from'] = $now;
			$deal['date_deal_to'] = date('Y-m-d H:i:s', strtotime('90 days'));
			$deal['applicable_for'] = 'all_items';		// 'single_item', 'selected_items', 'all_items'
			$deal['tipping_qty_for_deal'] =	0;
			$deal['date_added'] = $now;
			$deal['deal_status'] = 'active';
			$deal['listing_fee_paid'] =	'No';	// 'Yes', 'No'
			if (!($deal_id%30))
			{
				$deal['deal_status'] = 'deactivated';
				$deal['deal_tipping_status'] =	'';
			}
			if (!($deal_id%45))
			{
				$deal['deal_status'] = 'to_activate';
				$deal['deal_tipping_status'] =	'pending_tipping';
			}
			if (!($deal_id%55))
			{
				$deal['deal_status'] = 'expired';
				$deal['deal_tipping_status'] =	'tipping_reached';
			}
			if (!($deal_id%75))
			{
				$deal['deal_status'] = 'closed';
				$deal['deal_tipping_status'] =	'tipping_failed';
			}

			if(!($deal_id%5))
			{
				$deal['tipping_qty_for_deal'] =	rand(1, 50);
			}

			$deal['tipping_notified'] =	0; // 0,1,2
			DB::table('deal')->insert($deal);

			$deal_featured_request['deal_id'] = $deal_id;
			$deal_featured_request['user_id'] = $deal['user_id'];
			$deal_featured_request['date_featured_from'] = date('Y-m-d H:i:s', strtotime(rand(1, 20).' days'));
			$deal_featured_request['date_featured_to'] = date('Y-m-d H:i:s', strtotime(rand(25, 50).' days'));
			$deal_featured_days =$this->calculateDays($deal_featured_request['date_featured_from'], $deal_featured_request['date_featured_to']);
			$deal_featured_request['deal_featured_days'] = $deal_featured_days;
			$deal_featured_request['fee_paid_status'] = 'Yes';
			$deal_featured_request['date_added'] = date('Y-m-d H:i:s');
			$deal_featured_request['request_status'] = 'pending_for_approval';	// 'pending_for_approval','approved','un_approved'

			if (!($deal_id%25))
			{
				$deal_featured_request['date_approved_on'] = date('Y-m-d H:i:s');
				$deal_featured_request['request_status'] = 'approved';
				$deal_featured_request['admin_comment'] = ' Approved by admin for '.$deal_id;
			}

			if (!($deal_id%60))
			{
				$deal_featured_request['request_status'] = 'un_approved';
				$deal_featured_request['admin_comment'] = 'Un-Approved by admin for '.$deal_id;
			}
			// Deal featured request entry
			$request_id = DB::table('deal_featured_request')->insertGetId($deal_featured_request);
			if($deal_featured_request['request_status'] == 'approved')
			{
				$deal_featured['deal_id'] = $deal_id;
				$deal_featured['date_featured_from'] =  date('Y-m-d H:i:s', strtotime($deal_id.' days'));
				$deal_featured['date_featured_to'] =  date('Y-m-d H:i:s', strtotime($deal_id.' days'));
				$deal_featured['request_id'] =  $request_id;

				DB::table('deal_featured')->insert($deal_featured);
			}
		}

//		DB::table('deal_item')->insert($deal_item);
//		DB::table('deal_item_purchased_details')->insert($deal_item_purchased_details);
	}

	public function calculateDays($from_date, $to_date)
	{
		$from	= date('d-m-Y', strtotime($from_date));
		$to		= date('d-m-Y', strtotime($to_date));
		$deal_featured_days =((strtotime($to) - strtotime($from))/ (60 * 60 * 24)) + 1; //it will count no. of days
		return $deal_featured_days;
	}

	public  function slugify($text) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $text);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		return $clean;
	}


}