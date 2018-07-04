## Deal Plugin - Buysell 2.0

## Buysell Installation Instructions
	Steps to install Buysell:
	- Upload the deals script zip file into the Buysell script location of web server (Ex: /home/buysell/)
	- Unzip the file in the webserver
	- Give permissions (chmod –R 777 folder/) for the following folders & files
		- public/files
		- app/plugins/deals/lang
	- Execute the sql files in /app/plugins/deals/database/deals.sql

	That’s it! Deals Plugin - Buysell 2.0 is installed successfully.

## Cron settings
	Cron setting in command prompt
	* * * * * curl --get http://yourdomain.com/deals-cron/update-tipped-deals
	* * * * * curl --get http://yourdomain.com/deals-cron/update-expired-deals
	**Note: Please replace the yourdomain.com to your full URL where the script is installed.**

## Methods to get support
	In case, if you are stuck at any point and need help in installation, please don’t hesitate to contact us (http://support.agriya.com/), we would be very happy to assist you.
	Please make sure to check and download the latest version of the software if available, frequently

	Live chat & Support ticket: http://support.agriya.com/

	Contact Email: buysell@agriya.in

## Recommended web hosts
	Agriya has teamed up with a number of Web Hosts that are able to support our products. Check http://www.agriya.com/partners for recommended web hosting


