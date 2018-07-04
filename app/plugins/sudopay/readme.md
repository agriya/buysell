## Sudopay Plugin - Buysell 2.0

## Buysell Installation Instructions
	Steps to install Buysell:
	- Upload the sudopay script zip file into the Buysell script location of web server (Ex: /home/buysell/)
	- Unzip the file in the webserver
	- Give permissions (chmod –R 777 folder/) for the following folders & files
		- public/files
		- app/plugins/sudopay/lang
	- Execute the sql files in /app/plugins/sudopay/database/sudopay.sql

	That’s it! Sudopay Plugin - Buysell 2.0 is installed successfully.

## Sudopay settings
	For Sydopay Sandobox Mode
		Goto http://sandbox.sudopay.com
		Register or Login with your sudopay login details

		Goto http://sandbox.sudopay.com/users/dashboard
		Get Id, Api Key & Secret

		Goto http://sandbox.sudopay.com/websites
		Press "+ Add" Tab & enter your domain
		Get website id generated for the given domain

		Goto http://sandbox.sudopay.com/gateways
		Enable required Payment gateway by press "Not set" drop down and provided required details.

		Goto http://yourdomain.com/admin/sudopay/manage-payment-gateways
		Set "SudoPay" status Active & "Where to use?"

		Edit "SudoPay" and enable "Test Mode?"
		Set "Test Mode Credential"
			Sudopay Merchant Id = Id
			Sudopay Website Id = website id
			Sudopay Secret String = Secret
			Sudopay Api Key	= Api Key
		Press "Sync with SudoPay" button to get enabled gateways.

	For Sydopay Live Mode
		Goto http://sudopay.com
		Register or Login with your sudopay login details

		Goto http://sudopay.com/users/dashboard
		Get Id, Api Key & Secret

		Goto http://sandbox.sudopay.com/websites
		Press "+ Add" Tab & enter your domain
		Get website id generated for the given domain

		Goto http://sudopay.com/gateways
		Enable required Payment gateway by press "Not set" drop down and provided required details.

		Goto http://yourdomain.com/admin/sudopay/manage-payment-gateways
		Set "SudoPay" status Active & "Where to use?"

		Edit "SudoPay" and disable "Test Mode?"
		Set "Live Mode Credential"
			Sudopay Merchant Id = Id
			Sudopay Website Id = website id
			Sudopay Secret String = Secret
			Sudopay Api Key	= Api Key
		Press "Sync with SudoPay" button to get enabled gateways.

## Methods to get support
	In case, if you are stuck at any point and need help in installation, please don’t hesitate to contact us (http://support.agriya.com/), we would be very happy to assist you.
	Please make sure to check and download the latest version of the software if available, frequently

	Live chat & Support ticket: http://support.agriya.com/

	Contact Email: buysell@agriya.in

## Recommended web hosts
	Agriya has teamed up with a number of Web Hosts that are able to support our products. Check http://www.agriya.com/partners for recommended web hosting


