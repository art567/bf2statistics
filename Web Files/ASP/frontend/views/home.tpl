<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-sign-post">Welcome Page</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
<pre>
====================================================================================
BF2Statistics Official <?php echo CODE_VER; ?> Release - Private Statistics System for Battlefield 2
====================================================================================

Released by:  		Wilson212 (based on the work of The Shadow, MrNiceGuy, Chump, nylOn, Wolverine, and others)
Release date: 		<?php echo CODE_VER_DATE; ?>&nbsp;
Release version:	<?php echo CODE_VER; ?>&nbsp;
License:		GNU General Public License

Support URL:		http://www.bf2statistics.com/

Original Author:	The Shadow
Release Author:		Wilson212
Author Email:		wilson.steven10@yahoo.com
Author URL:		http://wilson212.net


Legal Bit:
==========
Copyright (C) 2006 - 2012  BF2Statistics

This program is free software; you can redistribute it and/or modify it under the terms of the GNU
General Public License as published by the Free Software Foundation; either version 2 of the License,
or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not,
write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


Credits:
========
Many long hours have been poured into the development of this system. However, none of it would have
been possible without the help of everyone involved in the community at BF2statistics.com. I myself am 
just one of the many people who have contributed to this modification to the game we love: Battlefield 2.
Thank you DICE/EA for producing such a fun and enjoyable game (even if the ride hasn't always been
smooth ;) ).

BF2Statistics is the product of the contributions of many (apologies if I miss anyone! :( ):
	Hosting:
		MrNiceGuy - http://www.bf2statistics.com
		
	Coding:
		Chump
		omero
		nylOn
		Wolverine
		ArmEagle
		THE_WUQKED
		Hand (for solving the 'sendall' bug!)
		Thunder (POE2 stats data)
		Wilson212 (Coder of bf2statitics v1.4.3 and newer)
		
	Testers:
		TheFlyingCorpse
		sysy (Sylvain)
		PowerPanda
		MacNeill_USA
		thomaskunze
		SAGA
		Dogstar
		XMog
		mdjdoniz
		TK
		CSUNO
		Kinsman
		Everyone else @ BF2statistics.com ;)
		
	Special Thanks:
		My Family (for putting up with me over the last few months)
		Everyone who uses this system. It's all for you anyway! :)


Purpose:
========
This system is designed to enable a server admin to run their own Private Statistics system for EA's
Battlefield 2 game. It aims to emulate the functionality of the official statistics system included in 
the game.  However, as it is controlled by the server admin, it can easily be customised to suit a 
particular need/purpose (ie, LAN Sessions, Private Clans, etc...).  This release includes some 
SIGNIFICANT changes to the BF2 private stats system, with a simpified web based admin tool. The entire
system has been validated against actual GameSpy data responses and information from http://ubar.bf2s.com
to ensure the highest levels of compatibility with third-party web based stats viewers AND BF2 itself.


Description:
============
These scripts have been extensively debugged to ensure proper operation.  They have been verified
against BF2 1.2, 1.3 & 1.4.  However, due to changes within the BF2 1.3 code HOSTS entry redirections
WILL cause CTD's on start-up.  To resolve this use the "BF2PrivateStats.vbs" script included in the 
"Utils" folder of this archive (Only works on Windows systems).

There is also a small VBScript (BF2PrivateStats.vbs) file included that helps player's to update
their HOSTS file easily (see: BF2PrivateStats for more info.  In addition, the GameSpy code has been
validated against other third-party BF2 Stats generators.

Note 1:	This has been tested against BF2:SF (ie, xpack) and seems to work well. Thanks MajArcher.
Note 2:	This release has been tested on Linux systems and appears to work well. Thanks PrePOD & others.


Compatibility:
==============
The developer of this release CANNOT guarantee compatibility with all systems. Any bugs reported will
be address on a "best-effort" basis. This release has been developed and tested against the following
systems:
	Game Server:
		Windows Server 2003 w/ SP1
		Battlefield 2 "Unranked" Server version 1.2+ (Windows)
		PIII 1.1GHz, 512MB RAM

	Web Server:
		Windows Server 2003 w/ SP1
		IIS6 w/PHP 5.2.0 or newer ISAPI
		MySQL5 (v4 Compatibility Mode)
		PII 550Mhz, 512MB RAM
		
Note 1:	The scripts should be universal; however Linux users may have to change any path references to
	match Linux file conventions (eg, ___init__.py).
Note 2:	Tested to be compatible with both Windows and Linux platforms! :)
Note 3:	Tested against various MODs (BF2sp64, Project Reality). Seems to function correctly...
		
Ah, before anyone comments, YES the hardware specs of the servers I use are quite low.  But this is
actually good as most people with have MUCH better hardware than me.  I'll code it work on my low-end
systems so your high-end ones should work really well! :)  I've tested this with 32 player Co-Op maps
and 30 'bots! On some maps there is a little bit of lag, but certainly MORE than playable. <GRIN>


Server Operation Tips:
======================
Over the time of operating my own BF2 Server I have found a few tid-bits that help with keeping your 
server up and running smoothly.
 1) If you run your server on lower spec hardware, try setting the bf2 server process to a higher
	priority (how you do this depends upon your platform).
 2) Make the server "roll over" debug errors: add "+ignoreAsserts 1" to the command-line you use to
	start-up your server. This makes the server ignore non-critical errors and just keep going.  
	One side-effect (yet to be confirmed) is that it stops client systems from CTD due to missing
	award data. :)
 3) Update .con file setting WITHOUT stopping your server: add "+fileMonitor 1" to the command-line
	you use to	start-up your server. This makes the server re-check the source files everytime
	the map changes. Quite handy for testing different settings. ;)
 4) Other command-line options:
	"+ranked 1"		This option actually has NO effect on bf2statistics and should NOT be used!
	"+dedicated 1"	If you are running a server, then you should already know about this one! ;)
 5) Shutdown ALL non-essential processes on your server! How you do this is dependant upon you server.
	My Windows Server 2003 system runs VERY lean (the OS uses about 100MB RAM) with only 12 active
	processes! If you run Linux, you could probably do even better than this! ;)


Requirements:
=============
 - Battlefield 2 Server (patch 1.2+)
 - XAMPP/Apache/IIS5+
 - PHP version 5.2.0 or newer
 - MySQL 4.x or MySQL 5.x

Note: IIS requires you to add/edit the file type ".aspx" to use PHP instead of the standard	ASP.NET. 
	This should be configured the same way as ".php" file types.


Helpful Resources:
==================
As can be seen above this system reliese on technologies from around the Internet. Here's a brief list
web sites that you may find helpful in setting you this system:

	PHP:	http://www.php.net/
	MySQL:	http://www.mysql.com/
	Apache:	http://www.apache.org/
	XAMPP:	http://www.apachefriends.org/en/xampp.html


New Installations:
==================
If you are new to BF2Statistics, then this what you need to know to get your own private statistics
system operational. Before proceeding, please ensure you have your web server operational with PHP &
MySQL installed and tested!
For those unsure how to do this, try XAMPP from Apache Friends (http://www.apachefriends.org/en/xampp.html),
or WAMP (http://www.wampserver.com/) if using windows Xp/Vista/7


Database Server (MySQL):
------------------------
	1.	Install MySQL 4 or MySQL 5
	2.	Create New Database (ie, bf2stats)
	3.	Create New DB User Account (ie, bf2statslogger)
	4.	Grant DBO (Database Owner) rights to new user account
	5.	Grant Global Right 'FILE ACCESS' (for database backups within Web Admin: OPTIONAL)


Web Server (PHP):
-----------------
	1.	Install Web Server (ie, Apache, IIS, other...) with PHP support.
	2.	Configure PHP to support the following extensions:
			- MySQL
			- CURL (or set "allow_url_fopen = 1")
	3.	Create a "/ASP/" directory in the root of your web server. Default locations:
			IIS 	==> "C:/InetPub/wwwroot"
			Apache	==> "C:/Program Files/Apache Group/Apache2/htdocs" ==or== /usr/local/httpd/htdocs
			XAMPP	==> "C:/Program Files/XAMPP/htdocs" ==or== /opt/lampp/htdocs/
	4.	Copy contents of "/ASP/" in archive to the location above (including ALL sub-directories)
	5.	Ensure the following files/directories har read/write access by PHP (CHMOD 777):
			/ASP/system/config
			/ASP/system/config/config.php
			/ASP/system/database
			/ASP/system/logs
			/ASP/system/logs/admin_event.log
			/ASP/system/logs/merge_players.log
			/ASP/system/logs/php_errors.log
			/ASP/system/logs/stats_debug.log
			/ASP/system/logs/validate_awards.log
			/ASP/system/logs/validate_ranks.log
			/ASP/system/snapshots/processed
			/ASP/system/snapshots/temp
	6.	Config your web server to process .aspx files as PHP files. For Apache based systems this should
			be automatic (via the .htaccess file). For IIS (and others?) you will have extra work to do.
			For IIS6 users (IIS5.x systems should be similar...):
				a. Start "Internet Information Services" Manager
				b. Navigate to your web site (ie, "Default Web Site"), right-click t and choose	properties
				c. Select the "Home Directory" tab
				d. Click "Configuration..."
				e. In the "Applications Extensions" list edit .aspx (if it doesn't exist, simply add it)
				f. Change the "Executable" to be the same as what your .php files use (ie, C:\PHP\php.exe,
					C:\PHP\php4isapi.dll, or C:\PHP\php5isapi.dll)
				g. Set "Verbs, Limit to:" to GET,POST,HEAD
				h. OK all windows. Done!
	7.	With a Web Browser, browse to: http://localhost/ASP/
			Note: If you are browsing from a remote machine, please change the value of $admin_hosts
			in /ASP/system/config/config.php to include your IP address.
	8.	Login to the Web Admin (Defaults: admin / admin)
	9.	Select the "Install DB" link at the top of the screen, or under the "System" navigation tab
	10.	Enter the database details defined above in the "Database Server" section
	12.	Click "Install" (this may take a few minutes depending on the speed of your systems)
	13.	Review the response for any errors
	14.	If ALL is good, click the Dashboard Navigation Link and you should now have a FULL menu!
	15.	Select the "Edit Configuration" link under the "System" navigation menu and Update your configuration as desired 
		(Make sure you "Update" it!)
		Note: Set the "Error Level" to "Detailed (4) to get highly detailed stats_debug messages"
	16.	Select the "Test System" link under the "System" navigation menu. Confirm that you want to proceed. 
			The script will now perform some basic tests on the Web Server components.
	17.	Review the test results. With luck everything should pass (warnings are OK, it usually just means a
			log file or something hasn't been created yet).


Game Server (Battlefield 2):
----------------------------
	1.	Make a backup of the following folder:
			"<Battlefield 2 Server Path>/python/bf2"
	2.	Copy the contents of "/python/bf2" to "<Battlefield 2 Server Path>/python/bf2" (including sub-
			directories), overwrite existing files. This release supports BOTH BF2 and BF2:SF; the scripts
			will detect which MOD is running.
	3.	Using a text editor, open "python/bf2/BF2StatisticsConfig.py"
	4.	Change the configuration options to suit your needs.  Specifically, change the "Backend Web Server"
			setting to match your configuration.
			WARNING: Even though you can change the "port" and "ASP" settings, this not recommended as BF2
				itself will *NOT* support this! You've been warned!
	5.	In the configuration file, set "debug_enable = 1"
	6.	Edit your server configuration files (ie, "<BF2 Server Path>/mods/bf2/settings/ServerSettings.con"
			& "<BF2 Server Path>/mods/bf2/settings/maplist.con") as desired.
	7.	Redirect "BF2web.gamespy.com" to resolve to your web server's IP address:
		- Windows Servers, use the "/Utils/BF2PrivateStats.vbs" script file contained within this archive.
			a. Copy "/Utils/BF2PrivateStats.vbs" & "/Utils/SetACL.exe" to "<Battlefield 2 Server Path>" 
			b. Using a text editor, edit the "strLookupAddr" value to match your web server. This can be
				set to a valid DNS host	name or and IP Address. Also, change "strBF2exe" to match the file
				used to launch your	server (ie, bf2_w32ded.exe)
			c. Use this script to start your BF2 server. All command-line paramters are passed directly
				to BF2.
		- Windows Users may also use a DNS server redirect like SimpleDNS Plus to easily redirect bf2web.gamespy.com
			If you choose this route, download this version of simpleDNS plus and follow the readme! 
			(http://www.mediafire.com/?bb1z2ruq2joc3uq)
		- For Linux servers, you will have use a DNS redirect spoof (all Linux admins know how to do 
			this right?)
	8.	Start your Battlefield 2 Server (it should start without any errors).
	9.	Check the contents on the log file generated (default location "/python/bf2/logs/"). Look for any
			obvious erros.


Game Client (Battlefield 2):
----------------------------
	1.	Redirect "BF2web.gamespy.com" to resolve to your web server's IP address:
		Note: This *ONLY* works on systems using the NTFS file system!!!
		a. Copy "/Utils/BF2PrivateStats.vbs" & "/Utils/SetACL.exe" to "<Battlefield 2 Path>" 
		b. Using a text editor, edit the "strLookupAddr" value to match your web server. This can be set 
			to a valid DNS host	name or and IP Address.  Also, change "strBF2exe" to match the file used
			to launch your game (ie, bf2.exe)
		c. Use this script to start your BF2 game. All command-line paramters are passed directly to BF2.
		NOTE: You may also use a DNS server redirect like SimpleDNS Plus to easily redirect bf2web.gamespy.com
			If you choose this route, download this version of simpleDNS plus and follow the readme! 
			(http://www.mediafire.com/?bb1z2ruq2joc3uq)
	2.	Play and have fun! :)
	3.	After you have completed one round of play (a player voted map change will speed this up). Check
			BFHQ within the game. If all is working as it should, you will find stats data from your game.
			If not, then go through the troubleshooting section later in this guide.
			NOTE: Please allow up to 1 minute after the End of Round for your stats to update via bfhq.
	4.	If all is good, set the "debug" options on both servers back to defaults!



Upgrade Existing Install:
=========================
If you already have BF2Statistics operational, then you've already done all the hard work getting this
system operational. The following guide should allow you to successfully upgrade to this version:

WARNING: Before proceeding Backup your exisitng system!!!  If something goings wrong, you can always go
	back and try again later! This IS *CRITICAL*!!!!

Database Server (MySQL):
------------------------
	1.	Backup your existing BF2Statistics database! Can't say this enough!!!
	2.	Verify the account used to access this database has DBO (Database Owner) rights!
	3.	Grant Global Right 'FILE ACCESS' (for database backups within Web Admin: OPTIONAL)


Web Server (PHP):
-----------------
	1.	Backup your existing "/ASP/" directory! Default locations:
			IIS 	==> "C:/InetPub/wwwroot"
			Apache	==> "C:/Program Files/Apache Group/Apache2/htdocs" --or-- /usr/local/httpd/htdocs
			XAMPP	==> "C:/Program Files/XAMPP/htdocs" --or-- /opt/lampp/htdocs/
	2.	Esure PHP supports the following extensions:
			- MySQL (it was already working, this should already be set)
			- CURL (or set "allow_url_fopen = 1")
	3.	Remove the current contents of "/ASP/" (Note: the config file is different and it's easier just
			to re-create it)
	4.	Copy contents of "/ASP/" in archive to the location above (including ALL sub-directories)
	5.	Ensure the following files/directories har read/write access by PHP (CHMOD 777):
			/ASP/system/config
			/ASP/system/config/config.php
			/ASP/system/database
			/ASP/system/logs
			/ASP/system/logs/admin_event.log
			/ASP/system/logs/merge_players.log
			/ASP/system/logs/php_errors.log
			/ASP/system/logs/stats_debug.log
			/ASP/system/logs/validate_awards.log
			/ASP/system/logs/validate_ranks.log
			/ASP/system/snapshots/processed
			/ASP/system/snapshots/temp
	6.	Ensure your web server processes .aspx files as PHP files (should already be done, but can't hurt
			right?). For Apache based systems this should be automatic (via the .htaccess file). For IIS
			(and others?) you will have extra work to do.
			For IIS6 users (IIS5.x systems should be similar...):
				a. Start "Internet Information Services" Manager
				b. Navigate to your web site (ie, "Default Web Site"), right-click and choose properties
				c. Select the "Home Directory" tab
				d. Click "Configuration..."
				e. In the "Applications Extensions" list edit .aspx (if it doesn't exist, simply add it)
				f. Change the "Executable" to be the same as what your .php files use (ie, C:\PHP\php.exe,
					C:\PHP\php4isapi.dll, or C:\PHP\php5isapi.dll)
				g. Set "Verbs, Limit to:" to GET,POST,HEAD
				h. OK all windows. Done!
	7.	With a Web Browser, browse to: http://localhost/ASP/
			Note: If you are browsing from a remote machine, please change the value of $admin_hosts
			in /ASP/system/config/config.php to include your IP address.
	8.	Login to the Web Admin (Defaults: admin / admin)
	9.	As the config file is unlikely to know your database details yet, it will highlight the "Install DB"
			link. Simply ignore this and select Edit Configuration". Enter your existsing database details there.
	10. Next Click "Upgrade Database". Confirm the update. (this may take a few minutes depending on the speed of your systems)
	11.	Review the response for any errors.
			Note: if your previous system was running a 1.3 release, then it is most likely you will see some
				errors. This is normal and expected.
	12.	If ALL is good, click the Dahsboard Link and you should now have a FULL menu!
	13.	Select the "Edit Configuration" link. Update your configuration as desired (Make sure you "Update" it!)
	14.	Select the "Test System" link. Confirm that you want to proceed. The script will now perform
			some basic tests on the Web Server components.
	175	Review the test results. With luck everything should pass (warnings are OK, it usually just means a
			log file or something hasn't been created yet).


Game Server (Battlefield 2):
----------------------------
	1.	Make a backup of the following folder:
			"<Battlefield 2 Server Path>/python/bf2"
	2.	Copy the contents of "/python/bf2" to "<Battlefield 2 Server Path>/python/bf2" (including sub-
			directories), overwrite existing files. This release supports BOTH BF2 and BF2:SF; the scripts
			will detect which MOD is running.
	3.	Using a text editor, open "python/bf2/BF2StatisticsConfig.py"
	4.	Change the configuration options to suit your needs.  Specifically, change the "Backend Web Server"
			setting to match your configuration.
			WARNING: Even though you can change the "port" and "ASP" settings, this not recommended as BF2
				itself will *NOT* support this! You've been warned!
	5.	In the configuration file, set "debug_enable = 1"
	6.	Edit your server configuration files (ie, "<BF2 Server Path>/mods/bf2/settings/ServerSettings.con"
			& "<BF2 Server Path>/mods/bf2/settings/maplist.con") as desired.
	7.	Redirect "BF2web.gamespy.com" to resolve to your web server's IP address:
		- Windows Servers, use the "/Utils/BF2PrivateStats.vbs" script file contained within this archive.
			a. Copy "/Utils/BF2PrivateStats.vbs" & "/Utils/SetACL.exe" to "<Battlefield 2 Server Path>" 
			b. Using a text editor, edit the "strLookupAddr" value to match your web server. This can be
				set to a valid DNS host	name or and IP Address. Also, change "strBF2exe" to match the file
				used to launch your	server (ie, bf2_w32ded.exe)
			c. Use this script to start your BF2 server. All command-line paramters are passed directly
				to BF2.
		- Windows Users may also use a DNS server redirect like SimpleDNS Plus to easily redirect bf2web.gamespy.com
			If you choose this route, download this version of simpleDNS plus and follow the readme! 
			(http://www.mediafire.com/?bb1z2ruq2joc3uq)
		- For Linux servers, you will have use a DNS redirect spoof (all Linux admins know how to do 
			this right?)
	8.	Start your Battlefield 2 Server (it should start without any errors).
	9.	Check the contents on the log file generated (default location "<BF2 Server Path>/python/bf2/logs/").
			Look for any obvious erros.


Game Client (Battlefield 2):
----------------------------
	No Update Required!


Troubleshooting:
================
OK, so something is not quite working as it should. Before you start trawling the BF2Statistics.com site
for answers, follow this simple troubleshooting guide. It won't necessarily solve your problem, but it
should help you isolate the cause:
	1.	Re-check you configuration on both the game server and web server
	2.	Review the log files generated in the following locations:
		 - "<BF2 Server Path>/python/bf2/logs/"
		 - "<Web Server>/ASP/system/logs/"
		 - "<PHP Root>/logs/"
	3.	Check the operation of the "BF2web.gamespy.com" redirections:
		 a. Start BF2 (Server or Client) using one of the HOSTS file "work-arounds"
		 b. Open a command prompt and type:
				ping bf2web.gamespy.com
		 c. If the response is from a HOST with IP: 207.38.10.110, then the redirection is not working :(
	4.	Verify you are not using and modified script files
	5.	Disable/Check any firewalls (ZoneAlarms has a nasty habit of blocking everything!)
	6.	Ok, now you can call for help! ;)


Known Issues:
=============
 - Unlocks do NOT work for offline accounts and bots.  This limitation is hard-coded into the BF2
	server executable.  Thanks nyl0n for this info. ;)
 - Medals do not seem to 100%. There have been reports of players incorrectly recieving medals. This
	does not seem consistient or easily reproducable. Further investigation is require to solve.


ToDo:
=====
There's always a ToDo list isn't there! ;)  Anyway, here a short list of what I want/need to do:
 - Move Game Server configuration to central Admin Web GUI. Add option to update config at start of
	each round. Config would be done on a per-server basis.
 - Improve performance and stability (ie, make sure it can run for weeks/months, not days!). Though I
	suspect this is more of an issue with my budget hardware! ;)
 - Enhance support for Central Database server. The current Central Server option is intended for
	LAN and/or Tournament systems. A Community Based Central Server would require additional coding
	(ie, valididation of SNAPSHOT data) to ensure that hackers and/or cheaters don't exploit the
	system. Obviously, someone would have to host such a system or just use ABR instead... ;)


Enjoy,
The Shadow, Wilson212
shadow42@iinet.net.au, wilson.steven10@yahoo.com

-EOF-
</pre>
		</div>
	</div>
</div>