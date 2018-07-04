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
include 'installation_files/boot.php'; ?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width">
        <title>Buysell Installation</title>

        <!-- FAVICONS
		================================================== -->
		<link rel="shortcut icon" href="installation_files/images/favicon.ico">

        <?php if (!isset($fatalError)): ?>
            <script>
            <!--
                installerBaseUrl = '<?= $installer->getBaseUrl() ?>';
            // -->
            </script>
        <?php endif ?>
	    <!-- Bootstrap -->
	    <link href="installation_files/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	    <link href="installation_files/bootstrap/css/todc-bootstrap.css" rel="stylesheet">
		<link href="installation_files/bootstrap/css/install.css" rel="stylesheet">
    </head>

	<body>
    	<div class="container">
			<div class="row">
            	<!-- BEGIN: LOGO -->
                <h1 class="text-center">
                    <img src="installation_files/images/logo.png" alt="Logo" />
                </h1>
                <!-- END: LOGO -->

				<section id="wizard" class="bs-install">
	                <?php if (isset($fatalError)): ?>
	                	<div id="system-error" class="alert alert-danger" role="alert"><i>&nbsp;</i> <?= $fatalError ?></div>
	                <?php else: ?>
                        <div id="rootwizard">
                        	<div class="row">
                                <div class="col-sm-3 col-xs-12">
                                	<!-- BEGIN: PAGE HEADER -->
                                    <h2>Buysell Installation</h2>
                                    <!-- END: PAGE HEADER -->

                                    <!-- BEGIN: TABS -->
                                    <ul class="list-unstyled ver-inline-menu">
                                        <li><a href="#tab1" data-toggle="tab">Welcome</a></li>
                                        <li><a href="#tab2" data-toggle="tab">System Check</a></li>
                                        <li><a href="#tab3" data-toggle="tab">Database</a></li>
                                        <li><a href="#tab4" data-toggle="tab">Administrator</a></li>
                                        <li><a href="#tab5" data-toggle="tab">Mailer Setup</a></li>
                                        <li><a href="#tab7" data-toggle="tab">Installation</a></li>
                                        <li><a href="#tab8" data-toggle="tab">Finish</a></li>
                                    </ul>
                                    <!-- END: TABS -->
                                </div>

                                <div class="col-sm-9 col-xs-12">
                                    <!-- BEGIN: PROGRESS BAR -->
                                    <div class="progress" id="top-bar">
                                        <div id="install-progress-bar" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"></div>
                                    </div>
                                    <!-- END: PROGRESS BAR -->

                                    <!-- BEGIN: TAB CONTENT -->
                                    <div class="tab-content">
                                        <!-- BEGIN: FIRST TAB - AGREEMENT -->
                                        <div class="tab-pane license-agree" id="tab1">
                                            <h3>Agriya License and Support Service Level Agreement</h3>
                                            <h4>Agriya License Agreement v1.0</h4>

                                            <p><strong>Copyright Notice:</strong> This software is the intellectual property of Ahsan Technologies Pvt. Ltd.
                                            (henceforth known as the COMPANY), and is covered by retained intellectual property rights, including copyright.
                                            The application is made available to you under the following terms and conditions.</p>

                                            <p>This License governs use of the accompanying Software, and your use of the Software constitutes acceptance of this license.
                                            "Software" includes, without limitation: all PHP / Java / Scala files, all resource files (CSS, JPEGs, Javascript, SQL, database/XML files,
                                            XML schema files, etc.), all source code, and all documentation. "Software" may include a number of third party scripts and files and are
                                            subject to their own licensing agreements. Product names, logos, brands, and other trademarks featured or referred to within the "Software"
                                            are the property of their respective trademark holders. These trademark holders are not affiliated with Ahsan Technologies Pvt. Ltd., Agriya,
                                            our products, or our website.</p>

                                            <p>You may use this Software for any purpose, commercial or otherwise, subject to the restrictions in this license and in accordance
                                            with the law (including by-laws) in the country in which it is operated.</p>

                                            <p>YOU ARE GIVEN TWO LICENSES TO INSTALL THE “SOFTWARE”. ONE FOR YOUR MAIN DOMAIN AND ONE FOR LOCALHOST (development purposes). Multiple
                                            instances of the “Software” cannot be operated on multiple domains under a single license. Additional licenses must be purchased for every
                                            domain you wish to install the software on. You are permitted to install multiple instances of the “Software” on a single domain, for
                                            example on sub-domains and on a localhost for testing and development purposes. Your license to lawfully use the “Software” will be terminated
                                            if evidence is found that the “Software” is being run on multiple domains. An additional development license can be provided upon request
                                            to run the software on localhost subject to approval by COMPANY based on information provided by CLIENT.</p>

                                            <p>YOU MAY NOT DISTRIBUTE TO ANY OTHER PERSON UNDER ANY CIRCUMSTANCES. This Software is made available exclusively to the purchasing
                                            party (henceforth known as the CLIENT), without the right to provide or demonstrate the Software for any third party. Your rights are
                                            personal, and are under no circumstances assignable, licensable, or otherwise transferable unless prior agreement is made with the COMPANY.</p>

                                            <p>YOU MAY MODIFY THIS SOFTWARE FOR ANY PURPOSE, except that you may NOT distribute the Software to any other person in original or modified
                                            form. You may not&nbsp; distribute this Software or any derivative works in any form for commercial purposes, including (without limitation)
                                            running business operations, licensing, leasing, or selling the Software, or distributing the Software for use with commercial products.</p>

                                            <p>SOFTWARE CUSTOMIZATION PERFORMED BY CLIENT OR THIRD PARTY WILL NOT BE ELIGIBLE FOR FREE TECHNICAL SUPPORT. The technical support desk is
                                             provided to fix problems and answer questions originating in and pertaining to the core files of the SOFTWARE. Requests for advice on how to
                                             modify the files to perform a new or different function compared to the default features will be treated as consultation requests and will be
                                             billed according to COMPANY standard hourly rate.</p>

                                            <p>FREE TECHNICAL SUPPORT IS PROVIDED BY THE COMPANY FOR A PERIOD OF ONE TO THREE MONTHS FROM THE DATE OF PURCHASE (depending upon the product
                                             purchased). You have the right to submit an unlimited number of technical support tickets related to issues or questions to the core files or
                                             default features for three months from the date of your purchase. Support is restricted to one of your licenses, either the live site license
                                             or the development license. The support period does NOT apply if COMPANY is currently in the process of customizing SOFTWARE based on client
                                             requirements. Support period for customization projects begins from the date of delivery and lasts for a period of not more than three months.</p>

                                            <p>In the rare case that COMPANY has not fixed technical support issues CLIENT has submitted during the three month support duration and
                                            the support duration expires COMPANY agrees to continue assigning resources to these tickets until the issues are resolved. CLIENT may not
                                            add new issues to open tickets that have passed the support duration period, any new issues reported will be ignored unless a support extension
                                            has been purchased.</p>

                                            <p>COMPANY will restrict posting of new support tickets by up to 48 hours before support duration expires.</p>

                                            <p>COMPANY AGREES TO RESPOND TO ALL TICKETS WITHIN 48 HOURS EXCLUDING WEEKENDS AND PUBLIC HOLIDAYS. The support desk is staffed 16 hours
                                            per day Monday to Friday and COMPANY agrees to respond to all submitted tickets within a 48 hour timeframe EXCEPT when the support technician
                                            notes that the issue will take longer to fix or the product has just been released. COMPANY agrees to inform CLIENT in advance of public
                                            holiday notices through Facebook and or other ways.</p>

                                            <p>NEW PRODUCT LAUNCHES ARE EXCLUDED FROM THE REGULAR TECHNICAL SUPPORT. Due to the high number of enquiries following a product launch
                                            COMPANY will not accept any technical support tickets posted to the support desk. CLIENT is asked to submit any issues to the bug tracker
                                            so that the core developers can make the fixes and everyone can get access to the updated SOFTWARE. The 48 hour turnaround time is not
                                            applicable for bug reports. Regular technical support will begin one month after the product has launched.</p>

                                            <p>UPGRADES PROVIDED FOR SOFTWARE. As the software is updated to patch errors, bugs, security flaws or to add new features CLIENT will
                                            have free, unrestricted access to the SOFTWARE updates for the duration as stated in each of the product licenses.. COMPANY provides patch
                                            files "as is" which allows CLIENT to perform the upgrade. COMPANY will offer to perform the upgrade for a fixed price however if CLIENT
                                            has made any customizations that they wish to keep COMPANY will quote a price accordingly. APPLYING PATCH FILES MAY OVERWRITE CUSTOMIZATIONS
                                            OR MODIFICATIONS MADE TO THE EXISTING FILES AND COMPANY DOES NOT ACCEPT RESPONSIBILITY FOR LOSS OF DATA WHEN CLIENT PERFORMS THE UPGRADE.</p>

                                            <p>REFUND POLICY.Agriya products are intangible goods that are digitally delivered, we therefore follow a strict refund policy on agreed
                                            terms*. Customers requesting for repeated refunds, will be blocked from making further purchases and from all future communication. Customers
                                            that violate the terms of use of the product may have their right of return revoked. Customer can request for refund within 15 days from
                                            purchase, have to produce an evidence of damages. Our dedicated customer service team &nbsp;would review and verify the evidence and
                                            if it is merit, a refund will be processed.Any refund request will be processed only if the evidence approved by our reliable customer
                                            service team.</p>

                                            <p>You may use any information in intangible form that you remember after accessing the Software. However, this right does not grant you a
                                            license to any of Ahsan Technologies Pvt. Ltd copyrights or patents for anything you might create using such information.</p>

                                            <div class="mb10"><strong>In return, you agree:</strong></div>
                                            <ul>
                                                <li>Not to remove any copyright or other notices from the Software. For products which have "copyright removal" as an add on module.</li>
                                                <li>Not to distribute this Software or any derivative works in any form to any person, entity or individual</li>
                                                <li>Not to distribute the Software to any other person under any circumstances.</li>
                                                <li>Not to try and circumvent or reverse engineer the licensing system in place</li>
                                                <li>THAT THE SOFTWARE COMES "AS IS", WITH NO WARRANTIES. THIS MEANS NO EXPRESS, IMPLIED OR STATUTORY WARRANTY, INCLUDING WITHOUT
                                                LIMITATION, WARRANTIES OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE OR ANY WARRANTY OF TITLE OR NON-INFRINGEMENT.</li>
                                                <li class="">THAT AHSAN TECHNOLOGIES PVT. LTD. WILL NOT BE LIABLE FOR ANY DAMAGES RELATED TO THE SOFTWARE OR THIS LICENSE, INCLUDING
                                                DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL OR INCIDENTAL DAMAGES, TO THE MAXIMUM EXTENT THE LAW PERMITS, NO MATTER WHAT LEGAL THEORY IT
                                                IS BASED ON.</li>
                                                <li class="">ALL PURCHASES ARE CONSIDERED FINAL DUE TO THE DIGITAL NATURE OF THE SOFTWARE. MERCHANT GATEWAY DISPUTES WILL BE TREATED AS
                                                A BREACH OF LICENSE AND YOUR SUPPORT AND UPGRADES WILL BE SUSPENDED FOR THE DURATION OF THE DISPUTE.</li>
                                                <li class="">That your rights under this License may be modified or canceled at any time, for any reason, and without notice to you, but
                                                will always be made available at
                                                <a target="_blank" href="http://customers.agriya.com/license-agreement" title="http://customers.agriya.com/license-agreement">
                                                http://customers.agriya.com/license-agreement</a>, and that any subsequent revision or version of this License will completely supersede
                                                the terms and obligations of all earlier Licenses, including this one.</li>
                                                <li>That your rights under the License end automatically if you breach it in any way, while your obligations remain in effect.</li>
                                                <li>That the terms and conditions of this License shall be governed by Indian law (without regard to its conflict
                                                of law principles), and that any dispute over the terms of this License may be brought only in the state and federal courts of India.</li>
                                            </ul>

                                            <p>Ahsan Technologies Pvt. Ltd. reserves all rights not expressly granted to you in this license. If you have questions about these
                                            terms and conditions, please send email to <a href="mailto:license@agriya.com" title="license@agriya.com">license@agriya.com</a> but
                                            note that such questions or email will not release you from the terms and conditions of this License Agreement.</p>

                                            <h4>Agriya Support Service Level Agreement v1.0</h4>
                                            <p><strong>Services Delivery is the key element of Agriya's integrated capability. Our support/services are focused on helping our customers
                                            become more responsive, resilient, variable and focused. Our delivery centers are currently located in Chennai. All technical support is
                                            handled through the helpdesk and operated purely on a ticket basis. Please note that all priorities have been set to medium as we work
                                            on tickets in the order that we receive them.</strong></p>

                                            <p><strong>This agreement is specifically for the support desk located on
                                            <a target="_blank" href="http://customers.agriya.com">Agriya Customers Portal</a></strong></p>

                                            <div class="mb10"><strong>What We Promise From Support Desk</strong></div>
                                            <ul>
                                                <li>Turn around time(TAT) for all our  support services( like installation requests, 3rd party integrations, Bug fixing,
                                                software-operation's queries) will be rectified/answered within 24 hours from the time of submission of the tickets.</li>
                                                <li>All the new bug reports will be fixed within 48 hours from the time the ticket(bug) is submitted.</li>
                                                <li>An instant reply to all our customer tickets will be sent as a token for receiving the ticket, and this mail will also enable
                                                you to have a clear insight of the details and timelines of the ticket.</li>
                                                <li>An instant update for any fixes found or any updates on the status of the ticket will be reported through a reply to the ticket
                                                thread along with the necessary/corresponding screen shots if required.</li>
                                                <li>The support Desk will support all the current software versions.</li>
                                                <li>If your server is found to be missing essential server requirements to run our software, then you will be notified with
                                                the missing requirements to resolve the same.</li>
                                            </ul>

                                            <div class="mb10"><strong>What we Don't Promise from Support Desk(for a 24 hour timeframe):</strong></div>
                                            <ul>
                                                <li>The support Desk will not give any Coding advice, and coding examples will not be offered through the support desk.</li>
                                                <li>The support desk will not review any code changes for compatibility.</li>
                                                <li>If the client has made changes or customizations to the software that requires a different fix to be enforced , and/or if this
                                                issue was reported as a new bug then the Support Desk will try hard to resolve the ticket/issue within 24/48 hours, but the fix for
                                                the issues is not guaranteed.</li>
                                                <li>If the client is using an older version of the software the 24 hour timeframe will not apply, and if this issue was reported
                                                as a new bug then 48 hour timeframe will not apply. </li>
                                                <li>If the bug cannot be replicated on the demo version of the software the 48 hour timeframe will not apply.</li>
                                            </ul>

                                            <div class="mb10"><strong>What we require:</strong></div>
                                            <ul>
                                                <li> Your correct website log in details, example admin log in details</li>
                                                <li> Your server log in details, example login access for SSH, CPANEL, FTP etc</li>
                                                <li> In case if you have any trouble with 3rd party services, example issues with your payment gateway or with Facebook etc, then
                                                we will need those related account log in details as well, so that our support team can investigate such problems.</li>
                                                <li> Depending upon your issue, we will need 24 hours to 4 days of time.</li>
                                                <li>If in case you submit a ticket with incorrect login details / incomplete info, the 24 hours to 4 days time will be considered only
                                                from the time you provide correct details.</li>
                                            </ul>

                                            <div class="mb10"><strong>Official Working Hours / Holidays / SLA</strong></div>
                                            <ul>
                                                <li>Our official working hours are Monday to Friday (7:00 A.M to 10:00 P.M IST)</li>
                                                <li>Our office will remain closed on all General / Local Holidays.</li>
                                                <li>Tickets posted during the working hours (7:00 A.M to 10:00 P.M IST)  will be resolved within 24/48 hours from the time the
                                                ticket has been submitted. If the ticket gets posted after our scheduled working hours then the tickets will be taken into next
                                                day's account, and will be resolved within 24/48 hours starting from the next day.</li>
                                                <li>Office closures will be announced in the <a target="_blank" href="http://customers.agriya.com">Agriya Customers Portal</a>,
                                                <a target="_blank" href="http://www.facebook.com/agriya">Facebook AgriyaNews</a> and the
                                                <a target="_blank" href="http://twitter.com/agriya">Twitter AgriyaNews</a></li>
                                            </ul>

                                            <p><strong>Ahsan Technologies Pvt. Ltd. reserves all rights not expressly granted to you in this Support Service Level Agreement. If you
                                            have questions about these terms and conditions, please send email to <a href="mailto:support.agriya@agriya.com" title="support.agriya@agriya.com">
                                            support.agriya@agriya.com</a> but note that such questions or email will not release you from the terms and conditions of this Support
                                            Service Level Agreement.</strong></p>
                                        </div>
                                        <!-- END: FIRST TAB - AGREEMENT -->

                                        <!-- BEGIN: SECOND TAB - SYSTEM REQUIREMENTS -->
                                        <div class="tab-pane" id="tab2">
                                            <div class="system"></div>
                                        </div>
                                        <!-- END: SECOND TAB - SYSTEM REQUIREMENTS -->

                                        <!-- BEGIN: THIRD TAB - DATABASE REQUIREMENTS -->
                                        <div class="tab-pane" id="tab3">
                                            <h3>Please prepare an empty database for this installation.</h3>
                                            <form class="form-horizontal" role="form">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="dbhost">MySQL Host</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="dbhost" value="localhost">
                                                        <span class="help-block">Specify the hostname for the database connection.</span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="dbport">MySQL Port</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="dbport" placeholder="3306">
                                                        <span class="help-block">(Optional) Specify a non-default port for the database connection.</span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="dbuser">MySQL Login</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="dbuser">
                                                        <span class="help-block">User with all privileges in the database.</span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="dbpass">MySQL Password</label>
                                                    <div class="col-sm-8">
                                                        <input type="password" class="form-control" id="dbpass">
                                                        <span class="help-block">Password for the specified user.</span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="dbname">Database Name</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="dbname">
                                                        <span class="help-block">Specify the name of the empty database.</span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- END: THIRD TAB - DATABASE REQUIREMENTS -->

                                        <!-- BEGIN: FOURTH TAB - ADMINISTRATOR REQUIREMENTS -->
                                        <div class="tab-pane" id="tab4">
                                            <h3>Please specify details for logging in to the Administration Area.</h3>
                                            <form class="form-horizontal" role="form">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="firstname">First Name</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="firstname" value="Admin">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="lastname">Last Name</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="lastname" value="Admin">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="email">Email Address</label>
                                                    <div class="col-sm-8">
                                                        <input type="email" class="form-control" id="email" value="admin@admin.com">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="password">Admin Password</label>
                                                    <div class="col-sm-8">
                                                        <input type="password" class="form-control" id="password">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="confirmpassword">Confirm Password</label>
                                                    <div class="col-sm-8">
                                                        <input type="password" class="form-control" id="confirmpassword">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="default_currency">Site Default Currency Code</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="default_currency" value="" placeholder="USD">
                                                        <span class="help-block">Specify the site default currency code, you cannot change after installing Buysell.</span>
                                                        <span class="help-block">Default is USD</span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- END: FOURTH TAB - ADMINISTRATOR REQUIREMENTS -->

                                        <!-- BEGIN: FOURTH TAB - MAIL REQUIREMENTS -->
                                        <div class="tab-pane" id="tab5">
                                            <h3>Mailer Setup</h3>
                                            <form class="form-horizontal" role="form">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="driver">Mail Driver (smtp/ sendmail / nativemail)</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="driver" value="smtp">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="host">Host Address (smtp.gmail.com)</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="host" value="smtp.gmail.com">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="port">Host Port (587)</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="port" value="587">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="encryption">E-Mail Encryption Protocol</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="encryption" value="tls">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="sendmail">System Path</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="sendmail" value="/usr/sbin/sendmail -bs">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="mail_username">Server Username</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="mail_username">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="mail_password">Server Password</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="mail_password">
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="mail_fromname">From Name</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="mail_fromname">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-4" for="mail_fromaddress">From Address</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="mail_fromaddress">
                                                    </div>
                                                </div>
                                                
                                            </form>
                                        </div>
                                        <!-- END: FOURTH TAB - ADMINISTRATOR REQUIREMENTS -->

                                        <!-- BEGIN: SIXTH TAB - INSTALLATION REQUIREMENTS -->
                                        <div class="tab-pane" id="tab7">
                                            <div id="setup">
                                                <div class="progress">
                                                    <div id="setup-progress-bar" class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">Setup</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END: SIXTH TAB - INSTALLATION REQUIREMENTS -->

                                        <!-- BEGIN: SEVENTH TAB - FINISH -->
                                        <div class="tab-pane" id="tab8">
                                            <div class="alert alert-success" role="alert">
                                                <strong>Well done!</strong> Installation has been successfully completed.
                                            </div>

                                            <div class="row mb20">
                                                <div class="col-sm-6">
                                                    <h3>Website address</h3>
                                                    <p>Your website is located at this URL:</p>
                                                    <p><a target="_blank" href="<?php echo $installer->getBaseUrl().'public';?>"><?php echo $installer->getBaseUrl().'public';?></a></p>
                                                    <p class="text-info">Map you server to public/ folder to access your site as <?php echo $installer->getBaseUrl();?></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <h3>Administration Details</h3>
                                                    <p>Use the following details to log into the administration area:</p>
                                                    <p><strong>Email:</strong> <span id="admin-email"></span></p>
                                                    <p><strong>Password:</strong> <span id="admin-password"></span></p>
                                                </div>
                                            </div>

                                            <div>
                                                <h3>Setting up the crontab</h3>
                                                <p>
                                                    For automated tasks to operate correctly, you should add the following to your Crontab using <strong>crontab -e</strong><br>
                                                </p>
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <code>
                                                        	* * * * * curl --get <?php echo $installer->getBaseUrl().'public';?>/cron/mass-mail<br />
                                                            0 0 * * * curl --get <?php echo $installer->getBaseUrl().'public';?>/cron/fetch-exchange-rates
                                                        </code>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-warning" role="alert">
                                                <h4>Important!</h4>
                                                For security reasons you should delete the installation files, the install.php script and the installation_files directory.
                                            </div>
                                        </div>
                                        <!-- END: SEVENTH TAB - FINISH -->

                                        <!-- BEGIN: PAGER -->
                                        <ul class="pager wizard">
                                            <li id="previous" class="previous"><a href="#">Previous</a></li>
                                            <li id="next" class="next"><a href="#">Next</a></li>
                                        </ul>
                                        <!-- END: PAGER -->
                                    </div>
                                    <!-- END: TAB CONTENT -->
                                </div>
                            </div>
                        </div>
					<?php endif ?>
				</section>
	 		</div>
		</div>

		<!-- SCRIPTS -->
	    <script src="installation_files/js/jquery-latest.js"></script>
	    <script src="installation_files/bootstrap/js/bootstrap.min.js"></script>
		<script src="installation_files/js/jquery.bootstrap.wizard.js"></script>
		<script src="installation_files/js/prettify.js"></script>
		<script>
			$(document).ready(function() {
				var system_error = false;
				var error_message = '';
				var start_setup = true;
				var started_setup = false;
				var finish_setup = false;
				$('#rootwizard').bootstrapWizard({
					tabClass: 'nav nav-pills',
					onTabClick: function(tab, navigation, index) {
						return false;
					},
					onPrevious: function(tab, navigation, index) {
						$('#system-error').remove();
						system_error = false;
					},
					onNext: function(tab, navigation, index) {
						if (index == 2 && system_error) {
							$('.system').html('<img src="installation_files/images/ajax-loader.gif" alt="loading" />');
							// Use Ajax to submit form data
							system_error = false;
							$.post(installerBaseUrl+'installation_files/check.php', 'system=1', function(system) {
								$('.system').html('');
								$.each(system, function(requirement, details){
									if (details['value']['result']) {
										$('.system').append('<div class="alert alert-success" role="alert"><i>&nbsp;</i> '+ details['label'] +'</div>');
										if (!system_error && details['label'] == 'Permission to write to directories and files') {
											$('#system-error').remove();
											$('.next').click();
										}
									} else {
										system_error = true;
										$('.system').append('<div class="alert alert-danger" role="alert"><i>&nbsp;</i> '+ details['label'] +'</div>');
										if ($('#system-error').length == 0) {
											$('#top-bar').after('<div id="system-error" class="alert alert-danger" role="alert"><i>&nbsp;</i> Check system settings and proceed</div>');
										}
									}
								});
							}, 'json');

							return false;
						} else if (index == 3) {
							// Make sure Database settings are entered
							system_error = false;
							error_message = '';
							$('#dbhost').closest('div').removeClass('has-error');
							$('#dbport').closest('div').removeClass('has-error');
							$('#dbuser').closest('div').removeClass('has-error');
							$('#dbname').closest('div').removeClass('has-error');
							$('#dbhost').val($.trim($('#dbhost').val()));
							$('#dbport').val($.trim($('#dbport').val()));
							$('#dbuser').val($.trim($('#dbuser').val()));
							$('#dbname').val($.trim($('#dbname').val()));
							$('#dbpass').val($.trim($('#dbpass').val()));

							if(!$('#dbhost').val()) {
								system_error = true;
								$('#dbhost').closest('div').addClass('has-error');
								error_message = 'Enter required database details';
								error_field = 'dbhost';
							}
							if($('#dbport').val() && parseInt($('#dbport').val()) != $('#dbport').val()) {
								system_error = true;
								$('#dbport').closest('div').addClass('has-error');
								error_message = 'Enter valid port';
								error_field = 'dbport';
							}
							if(!$('#dbuser').val()) {
								system_error = true;
								$('#dbuser').closest('div').addClass('has-error');
								error_message = 'Enter required database details';
								error_field = 'dbuser';
							}
							if(!$('#dbname').val()) { //has-error
								system_error = true;
								$('#dbname').closest('div').addClass('has-error');
								error_message = 'Enter required database details';
								error_field = 'dbname';
							}
							if (!system_error) {
								params = encodeURI('db=1&dbhost='+$('#dbhost').val()+'&dbport='+parseInt($('#dbport').val())+'&dbuser='+$('#dbuser').val()+'&dbpass='+encodeURIComponent($('#dbpass').val())+'&dbname='+$('#dbname').val());
								$.ajax({
									type: "POST",
									url: installerBaseUrl+'installation_files/check.php',
									data: params,
									dataType: 'json',
									async: false,
									success: function(details){
										if (details['value']) {
										} else {
											system_error = true;
											error_message = details['label'];
										}
									},
									error: function (xhr, ajaxOptions, thrownError) {
										system_error = true;
										error_message = xhr.responseText;
									}
								});
							}
							if(system_error) {
								if ($('#system-error').length == 0) {
									$('#top-bar').after('<div id="system-error" class="alert alert-danger" role="alert"><i>&nbsp;</i> ' + error_message + '</div>');
								} else {
									$('#system-error').html('<i>&nbsp;</i> ' + error_message);
								}
								return false;
							}
						} else if (index == 4) {
							// Make sure Administrator details are entered
							system_error = false;
							error_message = '';
							$('#firstname').closest('div').removeClass('has-error');
							$('#lastname').closest('div').removeClass('has-error');
							$('#email').closest('div').removeClass('has-error');
							$('#password').closest('div').removeClass('has-error');
							$('#confirmpassword').closest('div').removeClass('has-error');
							$('#default_currency').closest('div').removeClass('has-error');
							$('#firstname').val($.trim($('#firstname').val()));
							$('#lastname').val($.trim($('#lastname').val()));
							$('#email').val($.trim($('#email').val()));
							$('#password').val($.trim($('#password').val()));
							$('#confirmpassword').val($.trim($('#confirmpassword').val()));
							$('#default_currency').val($.trim($('#default_currency').val()));

							if(!$('#firstname').val()) {
								system_error = true;
								$('#firstname').closest('div').addClass('has-error');
								error_message = 'Please specify administrator first name';
								error_field = 'firstname';
							} else if(!$('#lastname').val()) {
								system_error = true;
								$('#lastname').closest('div').addClass('has-error');
								error_message = 'Please specify administrator last name';
								error_field = 'lastname';
							} else if(!$('#email').val()) {
								system_error = true;
								$('#email').closest('div').addClass('has-error');
								error_message = 'Please specify administrator email address';
								error_field = 'email';
							} else if(!validateEmail($('#email').val())) {
								system_error = true;
								$('#email').closest('div').addClass('has-error');
								error_message = 'Please specify valid email address';
								error_field = 'email';
							} else if(!$('#password').val()) {
								system_error = true;
								$('#password').closest('div').addClass('has-error');
								error_message = 'Please specify password';
								error_field = 'password';
							} else if(!$('#confirmpassword').val()) {
								system_error = true;
								$('#confirmpassword').closest('div').addClass('has-error');
								error_message = 'Please confirm chosen password';
								error_field = 'confirmpassword';
							} else if($('#password').val() != $('#confirmpassword').val()) {
								system_error = true;
								$('#confirmpassword').closest('div').addClass('has-error');
								error_message = 'Specified password does not match the confirmed password';
								error_field = 'confirmpassword';
							}
							if (!system_error && $('#default_currency').val() != "") {
								params = encodeURI('admin=1&default_currency='+$('#default_currency').val());
								$.ajax({
									type: "POST",
									url: installerBaseUrl+'installation_files/check.php',
									data: params,
									dataType: 'json',
									async: false,
									success: function(details){
										if (details['value']) {
											$('#default_currency').val($('#default_currency').val().toUpperCase());
										} else {
											system_error = true;
											error_message = details['label'];
										}
									},
									error: function (xhr, ajaxOptions, thrownError) {
										system_error = true;
										error_message = xhr.responseText;
									}
								});
							}
							if(system_error) {
								if ($('#system-error').length == 0) {
									$('#top-bar').after('<div id="system-error" class="alert alert-danger" role="alert"><i>&nbsp;</i> ' + error_message + '</div>');
								} else {
									$('#system-error').html('<i>&nbsp;</i> ' + error_message);
								}
								return false;
							}

						} else if (index == 5) {
							// Make sure Mailer details are entered
							system_error = false;
							error_message = '';
							$('#driver').closest('div').removeClass('has-error');
							$('#host').closest('div').removeClass('has-error');
							$('#port').closest('div').removeClass('has-error');
							$('#encryption').closest('div').removeClass('has-error');
							$('#sendmail').closest('div').removeClass('has-error');
							$('#mail_username').closest('div').removeClass('has-error');
							$('#mail_password').closest('div').removeClass('has-error');
							$('#mail_fromname').closest('div').removeClass('has-error');
							$('#mail_fromaddress').closest('div').removeClass('has-error');
							
							$('#driver').val($.trim($('#driver').val()));
							$('#host').val($.trim($('#host').val()));
							$('#port').val($.trim($('#port').val()));
							$('#encryption').val($.trim($('#encryption').val()));
							$('#sendmail').val($.trim($('#sendmail').val()));
							$('#mail_username').val($.trim($('#mail_username').val()));
							$('#mail_password').val($.trim($('#mail_password').val()));							
							$('#mail_fromname').val($.trim($('#mail_fromname').val()));
							$('#mail_fromaddress').val($.trim($('#mail_fromaddress').val()));

							if(!$('#driver').val()) {
								system_error = true;
								$('#driver').closest('div').addClass('has-error');
								error_message = 'Please specify Mail Driver';
								error_field = 'driver';
							} else if(!$('#host').val()) {
								system_error = true;
								$('#host').closest('div').addClass('has-error');
								error_message = 'Please specify Host Address';
								error_field = 'host';
							} else if(!$('#port').val()) {
								system_error = true;
								$('#port').closest('div').addClass('has-error');
								error_message = 'Please specify Host Port';
								error_field = 'port';
							} else if(!$('#encryption').val()) {
								system_error = true;
								$('#encryption').closest('div').addClass('has-error');
								error_message = 'Please specify E-Mail Encryption Protocol';
								error_field = 'encryption';
							} else if(!$('#sendmail').val()) {
								system_error = true;
								$('#sendmail').closest('div').addClass('has-error');
								error_message = 'Please specify System Path';
								error_field = 'sendmail';
							} else if(!$('#mail_username').val()) {
								system_error = true;
								$('#mail_username').closest('div').addClass('has-error');
								error_message = 'Please specify Server Username';
								error_field = 'mail_username';
							} else if(!$('#mail_password').val()) {
								system_error = true;
								$('#mail_password').closest('div').addClass('has-error');
								error_message = 'Please specify Server Password';
								error_field = 'mail_password';
							} else if(!$('#mail_fromname').val()) {
								system_error = true;
								$('#mail_fromname').closest('div').addClass('has-error');
								error_message = 'Please specify from name';
								error_field = 'mail_fromname';
							} else if(!$('#mail_fromaddress').val()) {
								system_error = true;
								$('#mail_fromaddress').closest('div').addClass('has-error');
								error_message = 'Please specify from address';
								error_field = 'mail_fromaddress';
							}
							
							if(system_error) {
								if ($('#system-error').length == 0) {
									$('#top-bar').after('<div id="system-error" class="alert alert-danger" role="alert"><i>&nbsp;</i> ' + error_message + '</div>');
								} else {
									$('#system-error').html('<i>&nbsp;</i> ' + error_message);
								}
								return false;
							}

						} else if (index == 6) {

						} else if (index == 7) {

						}
						if ($('#system-error').length) {
							$('#system-error').remove();
						}
					},
					onTabShow: function(tab, navigation, index) {
						if (index == 1) {
							$('.system').html('<img src="installation_files/images/ajax-loader.gif" alt="loading" />');
							// Use Ajax to submit form data
							system_error = false;
							$.post(installerBaseUrl+'installation_files/check.php', 'system=1', function(system) {
								$('.system').html('');
								$.each(system, function(requirement, details){
									if (details['value']['result']) {
										$('.system').append('<div class="alert alert-success" role="alert"><i>&nbsp;</i> '+ details['label'] +'</div>');
									} else {
										system_error = true;
										$('.system').append('<div class="alert alert-danger" role="alert"><i>&nbsp;</i> '+ details['label'] +'</div>');
									}
								});
							}, 'json');
						} else if (index == 6) {
							//Hide the pager
							$('.next').hide();
							$('.previous').hide();
							$('#setup').hide();

							$('#admin-email').html($('#email').val());
							$('#admin-password').html($('#password').val());
							startProgress('setup', 'Project setup', 100, 100);
							return false;
						} else if (index == 7) {
							//Hide the pager
							$('.next').hide();
							$('.previous').hide();
							$('#admin-email').html($('#email').val());
							$('#admin-password').html($('#password').val());
						}
						var $total = navigation.find('li').length;
						var $current = index+1;
						var $percent = ($current/$total) * 100;
						$('#rootwizard').find('#install-progress-bar').css({width:$percent+'%'});
					}
				});

				var previous_progress_control = '';
				// Show progress bar
				function startProgress(barname, label, timelimit, limit){
					var me = $('#rootwizard').find('#'+barname+'-progress-bar');
					var current_perc = 0;
					var start_action = eval("start_" + barname);
					var started_action = eval("started_" + barname);
					var progress_control = setInterval(function() {
						if (start_action) {
							if (current_perc >= limit) {
								if (me.attr('id') == 'setup-progress-bar') {
									me.text(label + ': '+(current_perc)+'% completed, Please wait for a moment');
									$('#rootwizard').find('#extracting-progress-bar').removeClass('active');
									me.addClass('active');
									previous_progress_control = progress_control;
								}
							} else {
								current_perc += 1;
								me.css('width', (current_perc)+'%');
								me.text(label + ': '+(current_perc)+'% completed');
							}

							//Start project setup
							if (current_perc == 1 && me.attr('id') == 'setup-progress-bar' && started_setup == false) {
								started_setup = true;
								$('#'+barname).show();
								//Validate database and start setup project
								params = encodeURI('setup=1&dbhost='+$('#dbhost').val()+'&dbport='+parseInt($('#dbport').val())+'&dbuser='+$('#dbuser').val()+'&dbpass='+encodeURIComponent($('#dbpass').val())+'&dbname='+$('#dbname').val()+'&email='+encodeURIComponent($('#email').val())+'&password='+encodeURIComponent($('#password').val())+'&default_currency='+$('#default_currency').val()+'&firstname='+$('#firstname').val()+'&lastname='+$('#lastname').val()+'&driver='+$('#driver').val()+'&host='+$('#host').val()+'&port='+$('#port').val()+'&encryption='+$('#encryption').val()+'&sendmail='+$('#sendmail').val()+'&mail_username='+$('#mail_username').val()+'&mail_password='+encodeURIComponent($('#mail_password').val()));
								$.ajax({
									type: "POST",
									url: installerBaseUrl+'installation_files/check.php',
									data: params,
									dataType: 'json',
									success: function(details){
										if (details['value']) {
											finish_setup = true;
											previous_progress_control = progress_control;
											me.css('width', '100%');
											me.text(label + ': 100% completed');
										} else {
											system_error = true;
											error_message = details['label'];
											me.removeClass('progress-bar-info');
											me.addClass('progress-bar-danger');
											me.css('width', '100%');
											me.text(error_message);
											clearInterval(progress_control);
										}
									},
									error: function (xhr, ajaxOptions, thrownError) {
										system_error = true;
										error_message = xhr.responseText;
										me.removeClass('progress-bar-info');
										me.addClass('progress-bar-danger');
										me.css('width', '100%');
										me.text(error_message);
										clearInterval(progress_control);
									}
								});
							}
							//Check setup finished
							if (current_perc == 100 && finish_setup == true) {
								if (previous_progress_control) {
									clearInterval(previous_progress_control);
								}
								//Go to next tab on finishing installation process
								$('.next').click();
							}
						}

					}, timelimit);
				};
				//End progress bar
				function endProgress(barname, label, progress){
					var me = $('#rootwizard').find('#'+barname+'-progress-bar');
					clearInterval(progress);
					me.text(label + ': 100% completed');
				};
				var waitforsomesecond = function(milliseconds){
					var start = new Date().getTime();
					for (var i = 0; i < 1e7; i++) {
						if ((new Date().getTime() - start) > milliseconds){
							break;
						}
					}
				};

				// Function that validates email address through a regular expression.
				function validateEmail(sEmail) {
					var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
					if (filter.test(sEmail)) {
						return true;
					} else {
						return false;
					}
				};
				window.prettyPrint && prettyPrint();
			});
		</script>
  	</body>
</html>