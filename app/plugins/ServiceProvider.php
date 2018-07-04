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
namespace App\Plugins;

abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function boot()
    {
        if ($module = $this->getModule(func_get_args()))
        {
            $this->package('app/' . $module, $module, app_path() . '/plugins/' . $module);
        }
    }

    public function register()
    {
        if ($module = $this->getModule(func_get_args()))
        {
            $this->app['config']->package('app/' . $module, app_path() . '/plugins/' . $module . '/config');

            // Add routes
            $routes = app_path() . '/plugins/' . $module . '/routes.php';
            if (file_exists($routes)) require $routes;
        }
    }

    public function getModule($args)
    {
        $module = (isset($args[0]) and is_string($args[0])) ? $args[0] : null;

        return $module;
    }

}

?>