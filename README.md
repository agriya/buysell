### Buysell Installation document 

### Server Requirements

	### The requirements to run Buysell 2.0 are as follows:
	- PHP (version 5.4.x to 5.6.x)
	- Linux Server (some old distributions are not supported)
	- Apache Web Server
	- MySQL (version 4.1.2 or higher)
	- Jpeg
	- CURL
	- mhash
	- GD Library 2 or higher

	### PHP Configuration Settings:
	1. safe_mode = off.
	2. register_globals = off
	3. open_basedir = (no value)
	4. output_buffering = on
	5. upload_max_filesize = 200M (or more)
	6. post_max_size = 200M (or more)
	7. max_execution_time = 0
	8. magic_gpc_quotes = 0
	9. max_input_time = 6000 (or more)
	10. memory_limit = 32M (or more)
	11. error_reporting = E_ALL & ~E_NOTICE | E_STRICT
	12. display_errors = On
	13. file_uploads = On
	14. session.gc_maxlifetime = 14000 (or more)
	15. CURL, GD library and Freetype library modules need to be installed.

## Buysell Installation Instructions
	Steps to install Buysell:
	- Upload the script zip file into the desired location of web server
	- Unzip the file in the webserver
	- Give permissions (chmod –R 777 folder/) for the following folders & files  (
		- /app/config/database.php
		- /app/storage/cache
		- /app/storage/logs
		- /app/storage/meta
		- /app/storage/sessions
		- /app/storage/views
		- /app/lang
		- /public/css-builds
		- /public/files
		- /public/js-builds
		- /installation_files/install.log
	- Type the domain name followed by install.php (e.g. http://yourdomain.com/install.php) in the address bar of the browser and run it.
	- Follow the steps to install Buysell
	That’s it! Buysell is installed successfully.
	
	**Note: Map your domain to public/ folder to access your site as http://yourdomain.com/, otherwise site will be accessible as http://yourdomain.com/public/ **

## Cron settings
	Cron setting in command prompt
	* * * * * curl --get http://yourdomain.com/cron/mass-mail
	0 0 * * * curl --get http://yourdomain.com/cron/fetch-exchange-rates
	**Note: Please replace the yourdomain.com to your full URL where the script is installed.**

## Facebook App settings
	- Goto Url - https://developers.facebook.com/apps
	- Login with your facebook account
	- Press "+ Add a New App" button
	- In "Add a New App" popup, select "Website"
	- Give the name for your app & press "Create New Facebook App ID"
	- In "Create a New App ID" popup - select Category & press "Create App ID".
	- "Tell us about your website"
		- Provide 'Site URL' & 'Mobile Site URL' then press "Next"
	- Click "Skip to Developer Dashboard"
	- Click "Settings" from left menu
		- Set "App Domains" and "Contact Email" then press save changes
	- Click "Status & Review" from left menu
		- Set "Do you want to make this app and all its live features available to the general public?" as "Yes".
	- Click "Dashboard" from left menu
		- Get "App ID" & "App Secret".
	Note: App ID = App Key

## Twitter App settings
	- Goto https://apps.twitter.com/
	- Login with your twitter account
	- Press "Create New App" button
	- Set App details
		Name - App name
		Description - App description
		Website - Site url
		Callback URL - Site url
	    Check - Yes, I agree
	    Press "Create your Twitter application"
	- Goto "Keys and Access Tokens"
		Get Consumer Key (API Key) & Consumer Secret (API Secret)


