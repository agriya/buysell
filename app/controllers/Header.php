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
//@added Vasanthi
class Header{

	public $menus = array();
	public $meta = array();
	public $replace_arr = array();

	function __construct() {
		//todo fetch the values from the meta lang file
		$this->replace_arr = array('VAR_SITE_NAME' => Config::get('generalConfig.site_name'));
		$current_script = URL::current();
		$current_page = substr($current_script, strrpos($current_script, '/')+1);
		$this->meta['title'] =  (Lang::has('meta.'.$current_page.'_title')) ? trans('meta.'.$current_page.'_title'): trans('meta.title');
        $this->meta['keyword'] = (Lang::has('meta.'.$current_page.'_keyword')) ? trans('meta.'.$current_page.'_keyword'): trans('meta.keyword');
        $this->meta['description'] = (Lang::has('meta.'.$current_page.'_description')) ? trans('meta.'.$current_page.'_description'): trans('meta.description');

    }
    public function getMetaTitle()
    {
		if($this->meta['title'])
			return str_replace(array_keys($this->replace_arr), array_values($this->replace_arr), $this->meta['title']);
    	return $this->meta['title'];
	}
    public function setMetaTitle($value)
    {
		$this->meta['title'] = $value;
	}

    public function getMetaKeyword()
    {
    	if($this->meta['keyword'])
			return str_replace(array_keys($this->replace_arr), array_values($this->replace_arr), $this->meta['keyword']);
    	return $this->meta['keyword'];
	}
    public function setMetaKeyword($value)
    {
    	$this->meta['keyword'] = $value;
	}

    public function getMetaDescription()
    {
    	if($this->meta['description'])
			return str_replace(array_keys($this->replace_arr), array_values($this->replace_arr), $this->meta['description']);
    	return $this->meta['description'];
	}
    public function setMetaDescription($value)
    {
    	$this->meta['description'] = $value;
	}
}