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
class Product extends Eloquent
{
    protected $table = "product";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "product_code", "product_name", "product_description", "product_support_content", "meta_title", "meta_keyword", "meta_description", "product_highlight_text", "product_slogan", "purchase_price", "product_price", "product_price_usd", "product_price_currency", "product_user_id", "product_sold", "product_added_date", "url_slug", "demo_url", "demo_details", "product_category_id", "product_tags", "total_views", "is_featured_product", "featured_product_expires", "is_user_featured_product", "date_activated", "product_discount_price", "product_discount_price_usd", "product_discount_fromdate", "product_discount_todate", "product_preview_type", "is_free_product", "last_updated_date", "total_downloads", "product_moreinfo_url", "global_transaction_fee_used", "site_transaction_fee_type", "site_transaction_fee", "site_transaction_fee_percent", "is_downloadable_product", "user_section_id", "delivery_days", "date_expires", "default_orig_img_width", "default_orig_img_height", "product_status", "shipping_template", "shipping_from_country");
}