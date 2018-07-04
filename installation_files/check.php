<?php include 'boot.php'; ?>
<?php

if (isset($_POST['system'])) {
	$requirements = array();
	$requirements['phpVersion']['label'] = 'PHP version 5.4 or greater required';
	$requirements['phpVersion']['value'] = $installer->checkSystemRequirement('phpVersion');

	$requirements['gdLibrary']['label'] = 'GD PHP Library is required';
	$requirements['gdLibrary']['value'] = $installer->checkSystemRequirement('gdLibrary');

	$requirements['curlLibrary']['label'] = 'cURL PHP Extension is required';
	$requirements['curlLibrary']['value'] = $installer->checkSystemRequirement('curlLibrary');

	$requirements['zipLibrary']['label'] = 'ZipArchive PHP Library is required';
	$requirements['zipLibrary']['value'] = $installer->checkSystemRequirement('zipLibrary');

	$requirements['safeMode']['label'] = 'Safe mode PHP setting is not enabled';
	$requirements['safeMode']['value'] = $installer->checkSystemRequirement('safeMode');

	$requirements['writePermission']['label'] = 'Permission to write to directories and files';
	$requirements['writePermission']['value'] = $installer->checkSystemRequirement('writePermission');
	//$requirements['liveConnection'] = $installer->checkSystemRequirement('liveConnection');
	echo json_encode($requirements);
} else if (isset($_POST['db']))  {
	//Check all required db details
	if (!isset($_POST['dbhost']) || !isset($_POST['dbuser']) || !isset($_POST['dbpass']) || !isset($_POST['dbname'])) {
    	$details['label'] = 'Invalid database details.';
    	$details['value'] = false;
		echo json_encode($details);
		exit;
	}
	$dbhost = trim($_POST['dbhost']);
	$dbport = (isset($_POST['dbport'])) ? trim($_POST['dbport']) : '';
	$dbuser = trim($_POST['dbuser']);
	$dbpass = trim(urldecode($_POST['dbpass']));
	$dbname = trim($_POST['dbname']);

	//validate database details
	$conn = ($dbport) ? mysql_connect($dbhost.':'.$dbport, $dbuser, $dbpass) : mysql_connect($dbhost, $dbuser, $dbpass);
	$details['value'] = true;
	if (!$conn) {
    	$details['label'] = 'Could not connect: ' . mysql_error();
    	$details['value'] = false;
	} else {
		$db_selected = mysql_select_db($dbname, $conn);
		if (!$db_selected) {
		    $details['label'] =  'Can\'t use '.$dbname.' : ' . mysql_error();
		    $details['value'] = false;
		} else {
			$sql = "SHOW TABLES FROM $dbname";
			$result = mysql_query($sql, $conn);
			if(mysql_num_rows($result) > 0) {
			    $details['label'] =  'Database "'.$dbname.'" is not empty. Please empty the database or specify another database.';
			    $details['value'] = false;
			}
		}
	}
	echo json_encode($details);
} else if (isset($_POST['admin']))  {
	//Check all required db details
	$details['value'] = true;
	if (isset($_POST['default_currency'])) {
		$default_currency = strtoupper(trim($_POST['default_currency']));
    	$details['label'] = 'Invalid currency code.';
    	$details['value'] = $installer->checkIsCurrencyCodeValid($default_currency);;
	}
	echo json_encode($details);

} 
	else if (isset($_POST['setup'])) {
	//Check all required db details
	if (!isset($_POST['dbhost']) || !isset($_POST['dbuser']) || !isset($_POST['dbpass']) || !isset($_POST['dbname'])) {
    	$details['label'] = 'Invalid database details.';
    	$details['value'] = false;
		echo json_encode($details);
		exit;
	}

	$default_currency = 'USD';
	if (isset($_POST['default_currency']) && $_POST['default_currency'] != "") {
		$default_currency = trim($_POST['default_currency']);
    	$details['label'] = 'Invalid currency code.';
    	$details['value'] = $installer->checkIsCurrencyCodeValid($default_currency);
    	if(!$details['value'])
    	{
			echo json_encode($details);
			exit;
		}
	}
	$default_currency = strtoupper($default_currency);

	if (!isset($_POST['driver']) || !isset($_POST['host']) || !isset($_POST['port']) || !isset($_POST['encryption']) || !isset($_POST['sendmail']) || !isset($_POST['mail_username']) || !isset($_POST['mail_password'])) {
    	$details['label'] = 'Invalid mailer details.';
    	$details['value'] = false;
		echo json_encode($details);
		exit;
	}
	$dbhost = trim($_POST['dbhost']);
	$dbport = (isset($_POST['dbport'])) ? trim($_POST['dbport']) : '';
	$dbuser = trim($_POST['dbuser']);
	$dbpass = trim(urldecode($_POST['dbpass']));
	$dbname = trim($_POST['dbname']);

	//validate database details
	$hostname = ($dbport != '') ? $dbhost.':'.$dbport : $dbhost;
	$conn = mysql_connect($hostname, $dbuser, $dbpass);
	$details['value'] = true;
	if (!$conn) {
    	$details['label'] = 'Could not connect: ' . mysql_error();
    	$details['value'] = false;
	} else {
		$db_selected = mysql_select_db($dbname, $conn);
		if (!$db_selected) {
		    $details['label'] =  'Can\'t use '.$dbname.' : ' . mysql_error();
		    $details['value'] = false;
		}
	}
	if (!$details['value']) {
		echo json_encode($details);
		exit;
	}

	//Database files path
	$sql_folder = PATH_INSTALL . '/app/database/';

	// Execute update_combined.sql
	$filename = 'update_combined.sql';
	$file_content = file($sql_folder.$filename);
    $query = '';
    foreach ($file_content as $sql_line) {
        $tsl = trim($sql_line);
        if ($sql_line and (substr($tsl, 0, 2) != '--') and (substr($tsl, 0, 1) != '#')) {
            $query .= $sql_line;
            if (substr($tsl, -1) == ';') {
                set_time_limit(300);
                $sql = trim($query, "\0.. ;");
                $result = mysql_query($sql, $conn);
                if (!$result) {
                    $installer->log("Failure in `$filename` upgrade:\n$sql");
                    $error = mysql_error();
                    $installer->log($error);
			    	$details['label'] = "Failure in `$filename` upgrade:\n$sql. $error";
			    	$details['value'] = false;
			    	echo json_encode($details);
                    exit;
                }
                $query = '';
            }
        }
    }
    $remainder = trim($query);
    if ($remainder) {
        $installer->log("Trailing text in `$filename` upgrade:\n$remainder");
    	$details['label'] = "Trailing text in `$filename` upgrade:\n$remainder";
    	$details['value'] = false;
    	echo json_encode($details);
        exit;
    }

	// Execute update_combined.sql
	$filename = 'data_combined.sql';
	$file_content = file($sql_folder.$filename);
    $query = '';
    foreach ($file_content as $sql_line) {
        $tsl = trim($sql_line);
        if ($sql_line and (substr($tsl, 0, 2) != '--') and (substr($tsl, 0, 1) != '#')) {
            $query .= $sql_line;
            if (substr($tsl, -1) == ';') {
                set_time_limit(300);
                $sql = trim($query, "\0.. ;");
                $result = mysql_query($sql, $conn);
                if (!$result) {
                    $installer->log("Failure in `$filename` upgrade:\n$sql");
                    $error = mysql_error();
                    $installer->log($error);
			    	$details['label'] = "Failure in `$filename` upgrade:\n$sql. $error";
			    	$details['value'] = false;
			    	echo json_encode($details);
                    exit;
                }
                $query = '';
            }
        }
    }
    $remainder = trim($query);
    if ($remainder) {
        $installer->log("Trailing text in `$filename` upgrade:\n$remainder");
    	$details['label'] = "Trailing text in `$filename` upgrade:\n$remainder";
    	$details['value'] = false;
    	echo json_encode($details);
        exit;
    }

	// Execute update_combined.sql
	$filename = 'currency_exchange_rate.sql';
	$file_content = file($sql_folder.$filename);
    $query = '';
    foreach ($file_content as $sql_line) {
        $tsl = trim($sql_line);
        if ($sql_line and (substr($tsl, 0, 2) != '--') and (substr($tsl, 0, 1) != '#')) {
            $query .= $sql_line;
            if (substr($tsl, -1) == ';') {
                set_time_limit(300);
                $sql = trim($query, "\0.. ;");
                $result = mysql_query($sql, $conn);
                if (!$result) {
                    $installer->log("Failure in `$filename` upgrade:\n$sql");
                    $error = mysql_error();
                    $installer->log($error);
			    	$details['label'] = "Failure in `$filename` upgrade:\n$sql. $error";
			    	$details['value'] = false;
			    	echo json_encode($details);
                    exit;
                }
                $query = '';
            }
        }
    }
    $remainder = trim($query);
    if ($remainder) {
        $installer->log("Trailing text in `$filename` upgrade:\n$remainder");
    	$details['label'] = "Trailing text in `$filename` upgrade:\n$remainder";
    	$details['value'] = false;
    	echo json_encode($details);
        exit;
    }

	// Execute update_combined.sql
	$filename = 'config_data.sql';
	$file_content = file($sql_folder.$filename);
    $query = '';
    foreach ($file_content as $sql_line) {
        $tsl = trim($sql_line);
        if ($sql_line and (substr($tsl, 0, 2) != '--') and (substr($tsl, 0, 1) != '#')) {
            $query .= $sql_line;
            if (substr($tsl, -1) == ';') {
                set_time_limit(300);
                $sql = trim($query, "\0.. ;");
                $result = mysql_query($sql, $conn);
                if (!$result) {
                    $installer->log("Failure in `$filename` upgrade:\n$sql");
                    $error = mysql_error();
                    $installer->log($error);
			    	$details['label'] = "Failure in `$filename` upgrade:\n$sql. $error";
			    	$details['value'] = false;
			    	echo json_encode($details);
                    exit;
                }
                $query = '';
            }
        }
    }
    $remainder = trim($query);
    if ($remainder) {
        $installer->log("Trailing text in `$filename` upgrade:\n$remainder");
    	$details['label'] = "Trailing text in `$filename` upgrade:\n$remainder";
    	$details['value'] = false;
    	echo json_encode($details);
        exit;
    }

    //Update database details
    $database_file_live = PATH_INSTALL . '/app/config/database.php';
    $database_file_install = PATH_INSTALL . '/installation_files/configs/database.php';
    $hostname = str_ireplace(':NaN', '', $hostname);
    $file_contents = file_get_contents($database_file_install);
	$file_contents = str_replace("VAR_LOCALHOST", $hostname, $file_contents);
	$file_contents = str_replace("VAR_DATABASE", $dbname, $file_contents);
	$file_contents = str_replace("VAR_LOGIN_USER", $dbuser, $file_contents);
	$file_contents = str_replace("VAR_PASSWORD", $dbpass, $file_contents);
	file_put_contents($database_file_live, $file_contents);

	//Update admin login details
	$firstname = trim($_POST['firstname']);
	$lastname = trim($_POST['lastname']);
	$email = trim(urldecode($_POST['email']));
	$password = (trim($_POST['password']))?trim(urldecode($_POST['password'])):'';

	if ($password) {
		$strength = 8;
		$saltLength = 22;
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$salt = substr(str_shuffle(str_repeat($pool, 5)), 0, $saltLength);
		$strength1 = str_pad($strength, 2, '0', STR_PAD_LEFT);
		$prefix = PHP_VERSION_ID < 50307 ? '$2a$' : '$2y$';
		$password = crypt($password, $prefix.$strength1.'$'.$salt.'$');
	}

	$update_query = "UPDATE users SET first_name = '".addslashes($firstname)."'".
									", last_name = '".addslashes($lastname)."'".
									", email = '".addslashes($email)."'";
	if ($password) {
		$update_query .= ", password = '".addslashes($password)."'";
	}
	$update_query .= " WHERE id = 1";
	$installer->log($update_query);
	$result = mysql_query($update_query, $conn);
	if (!$result) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
    	$details['label'] = $error;
    	$details['value'] = false;
    	echo json_encode($details);
        exit;
	}

	//Update mailer details
	$driver = trim($_POST['driver']);
	$host = trim($_POST['host']);
	$port = trim($_POST['port']);
	$encryption = trim($_POST['encryption']);
	$sendmail = trim($_POST['sendmail']);
	$mail_username = trim($_POST['mail_username']);
	$mail_password = trim(urldecode($_POST['mail_password']));
	$mail_fromname = (trim($_POST['mail_fromname'])) ? trim(urldecode($_POST['mail_fromname'])):'';
	$mail_fromaddress = (trim($_POST['mail_fromaddress'])) ? trim(urldecode($_POST['mail_fromaddress'])):'';

	$update_driver_config = "UPDATE config_data SET config_value = '".addslashes($driver)."'".
									" WHERE file_name = 'mail' AND config_var = 'driver'";
	$installer->log($update_driver_config);
	$update_driver = mysql_query($update_driver_config, $conn);
	if (!$update_driver) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_host_config = "UPDATE config_data SET config_value = '".addslashes($host)."'".
									" WHERE file_name = 'mail' AND config_var = 'host'";
	$installer->log($update_host_config);
	$update_host = mysql_query($update_host_config, $conn);
	if (!$update_host) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_port_config = "UPDATE config_data SET config_value = '".addslashes($port)."'".
									" WHERE file_name = 'mail' AND config_var = 'port'";
	$installer->log($update_port_config);
	$update_port = mysql_query($update_port_config, $conn);
	if (!$update_port) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_encryption_config = "UPDATE config_data SET config_value = '".addslashes($encryption)."'".
									" WHERE file_name = 'mail' AND config_var = 'encryption'";
	$installer->log($update_encryption_config);
	$update_encryption = mysql_query($update_encryption_config, $conn);
	if (!$update_encryption) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_sendmail_config = "UPDATE config_data SET config_value = '".addslashes($sendmail)."'".
									" WHERE file_name = 'mail' AND config_var = 'sendmail'";
	$installer->log($update_sendmail_config);
	$update_sendmail = mysql_query($update_sendmail_config, $conn);
	if (!$update_sendmail) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_username_config = "UPDATE config_data SET config_value = '".addslashes($mail_username)."'".
									" WHERE file_name = 'mail' AND config_var = 'username'";
	$installer->log($update_username_config);
	$update_username = mysql_query($update_username_config, $conn);
	if (!$update_username) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_password_config = "UPDATE config_data SET config_value = '".addslashes($mail_password)."'".
									" WHERE file_name = 'mail' AND config_var = 'password'";
	$installer->log($update_password_config);
	$update_password = mysql_query($update_password_config, $conn);
	if (!$update_password) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_mail_fromname_config = "UPDATE config_data SET config_value = '".addslashes($mail_username)."'".
									" WHERE file_name = 'mail' AND config_var = 'from_name'";
	$installer->log($update_mail_fromname_config);
	$update_username = mysql_query($update_mail_fromname_config, $conn);
	if (!$update_username) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_mail_fromaddress_config = "UPDATE config_data SET config_value = '".addslashes($mail_password)."'".
									" WHERE file_name = 'mail' AND config_var = 'from_email'";
	$installer->log($update_mail_fromaddress_config);
	$update_password = mysql_query($update_mail_fromaddress_config, $conn);
	if (!$update_password) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$update_currency_config = "UPDATE config_data SET config_value = '".addslashes($default_currency)."'".
									" WHERE file_name = 'generalConfig' AND config_var = 'site_default_currency'";
	$installer->log($update_currency_config);
	$update_currency = mysql_query($update_currency_config, $conn);
	if (!$update_currency) {
		$error  = 'Invalid query: ' . mysql_error();
		$installer->log($error);
	}

	$version = include(PATH_INSTALL . '/app/config/version.php');
	$host = str_ireplace('installation_files/', '', $installer->getBaseUrl());
	$ip = (isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:'Unknown IP';
	$version_no = $version['version'];
	$svn = $version['svn'];

	$mailSub = "[Buysell] - Buysell installed successfully in ". $host;
	$content = '
Hi,
Buysell is installed successfully in '.$host.' on '.date('Y-m-d H:i:s').'

Version: '.$version_no.'
SVN: '.$svn.'
IP ADDRESS: '.$ip.'

Regards,
Buysell Dev Team
	';

	if (!file_exists(PATH_INSTALL . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php'))
	{
		//"Mail sent, swift mailer class file not exist";
	   	$to = "buysell@agriya.in";
	   	$header = "From:Buysell \r\n";
	   	$header .= "MIME-Version: 1.0\r\n";
	   	$header .= "Content-type: text/html\r\n";
	   	mail($to, $mailSub, $content, $header);
	}
	else
	{
		//Include required path
		include(PATH_INSTALL . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php');
		// Create the SMTP configuration
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 587, 'tls');
		$transport->setUsername("travelhub.ahsan@gmail.com");
		$transport->setPassword("ahsan.in");
		// Create the message
		$message = Swift_Message::newInstance();
		$message->setTo(array("buysell@agriya.in" => "Buysell Agriya"));
		//$message->setCc(array("s.sridharan@agriya.in" => "Sridharan"));
		$message->setSubject($mailSub);
		$message->setBody($content);
		$message->setFrom("noreply@buysell.com", "Buysell");
		// Send the email
		$mailer = Swift_Mailer::newInstance($transport);
		try
		{
			$mailer->send($message, $failedRecipients);
		}
		catch(Exception $exception){
		//	$errMsg = $exception->getMessage();
		//	$content = $content."<br> Unable to send via swift mailer.<br> Swift mailer error message: <br>".$errMsg;
		   	$to = "buysell@agriya.in";
		   	$header = "From:Buysell \r\n";
		   	$header .= "MIME-Version: 1.0\r\n";
		   	$header .= "Content-type: text/html\r\n";
			mail($to, $mailSub, $content, $header);
		}
	}
	echo json_encode($details);
    exit;

} else {
	$details['label'] = 'Invalid request!';
	$details['value'] = false;
	echo json_encode($details);
}
?>