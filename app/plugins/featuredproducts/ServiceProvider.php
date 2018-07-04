<?php namespace App\Plugins\FeaturedProducts;
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
class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register()
    {
        parent::register('featuredproducts');
    }

    public function boot()
    {
        parent::boot('featuredproducts');
    }

}

?>