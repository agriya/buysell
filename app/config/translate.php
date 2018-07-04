<?php


return array(
	'trans_folder' => array(
							'lang/%s/',
							'lang/%s/admin/',
							'lang/%s/auth/',
							'lang/%s/myaccount/',
						),

	/*$ct_inc = sizeof($CFG['trans_folder']);
	foreach($CFG['site']['modules_arr'] as $ct_module)
		{
			$CFG['trans']['folder'][$ct_inc] = 'languages/%s/'.$ct_module.'/';
			$ct_inc++;
			$CFG['trans']['folder'][$ct_inc] = 'languages/%s/'.$ct_module.'/admin/';
			$ct_inc++;
			$CFG['trans']['folder'][$ct_inc] = 'languages/%s/'.$ct_module.'/version_upgrade_lang/';
			$ct_inc++;
		}*/

	//Files which not needed to translate
	'not_trans_files' => array(),
);
?>