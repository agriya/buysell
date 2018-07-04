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
 namespace App\Plugins\Importer\Controllers;
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config, Products;
use Session, Redirect, BaseController;
class ImporterCronController extends \BaseController
{
	public function getIndex()
	{ 
		$importerCronService = new ImporterCronService();
		$pending_files = $importerCronService->getPendingImporterFiles();
		if(count($pending_files) > 0)
		{
			foreach($pending_files as $file)
			{ 
				$file_from = $file->file_from;
				switch($file_from)
				{
					case 'etsy':
						$importerCronService->fetchEtsyProductsById($file);
						break;

					case 'general':
						$importerCronService->fetchGeneralProductsById($file);
						break;
				}
			}
		}
	}

}

?>