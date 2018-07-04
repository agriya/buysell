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
class SudoPay_Utils
{
    public static function getSignature($secret, $fields_arr)
    {
        unset($fields_arr['signature']);
        $query_string = '';
        foreach($fields_arr as $key => $val) {
            $query_string.= $key . '=' . $val . '&';
        }
        $query_string = substr($query_string, 0, -1); // remove final &
        //echo $secret . $query_string; //Display md5 query string
        return md5($secret . $query_string);
    }
    public static function isValidSignature($secret, $fields_arr)
    {
        return ($fields_arr['signature'] == getSignature($secret, $fields_arr));
    }
}
?>