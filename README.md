Bf2statistics Installation guide Written by wilson212

Referances:
www.bf2statistics.com

// **************************************************************
// Intro

		Welcome and thank you for downloading BF2statistics Official version 1.5.0! This BF2stats system was written by Chump, MrNiceGuy,
	The shadow, and many more. All i have done is take BF2statistics 1.4.2, and added many fixes that myself and many others have pushed in the
	forums, As well as Re-write the entire ASP. I donot take all credit for these fixes, and I certainly donot take credit for writting 
    any of the original BF2statistics scripts.

// **************************************************************
// Things Needed for intallation

	- Battlefield 2 Version 1.2+ (Recomeneded 1.41 or 1.50). (patchs found on various websites, just google bf2 1.41 / 1.50 patch)
	- Bf2 dedicated server 1.2+ (Recomeneded 1.41 or 1.50).  (found here: http://www.bf-games.net/index.php?action=download_details&new=1&downloadid=953 and here http://www.gamershell.com/download_16478.shtml)
	- Xampp or Wamp (other Apache, PHP, CuRL, and Mysql software)

// **************************************************************
// Installation

	=== Table of contents ===

	1.Installing Xampp or Wamp
	2.Creating the database
	3.Installing the dedicated server
	4.Installing/Updating the database
	5.Final Installation of script files
	6. Online accounts and unlocks
	7.FAQ / Editing
	_______________________________

	1. So first off is to install Xampp or Wamp on your computer ( any version, 1.5.0 tested and works on xampp 1.7.7, and Wamp 2.2c ). When it brings up the 
		screen that asks you to pick which services to install, make sure you check off MySql, and Apache services. If you already have 
		apache, MySQL, PHP, and CuRL installed on your machine, then Xampp/Wamp isnt needed
		
	2. When all is said and done and it is installed, open up your webbrowser and type 127.0.0.1 in the address bar. Select your language (if using Xampp)

	2.1 Click on Phpmyadmin on the left hand side about halfway down the screen, Or under "Your Alias'"if using Wamp
    
    2.1a. If using Wamp, you will need to login to the Phpmyadmin. First you need to set the ROOT password to your mysql database. Look for the Wamp icon
        in yur system tray (near the date/time, bottom right). you should see either a Red, Orange, or Green "W". If you dont, please make sure you have 
        Wamp running. Once the icon is green, that means your Wamp server is running correctly... If you have any problems, please refer to http://wampserver.com.
        Click the green "W", and mouse over the "MySQL" link, a side menu will pop up, Click MySQL Console. Type: 
        UPDATE mysql.user SET password=password("newpassword") WHERE user="root"; .. Replace "newpassword" with your password... hit enter
        It should say something about the query OK and a number of affected rows. Next type: Flush Privileges; hit enter.
        
    2.2 Next, In the phpmyadmin screen, Click "Databases" at the top of the screen.

	2.3 Under "create new database", Type in bf2stats. Then select "create".

	2.4 Next, your going to add a user to access and modify these stats. At the top of the screen, click "localhost", then click on "privilages"

	2.5 you should get a table that looks like this:

		User overview
		A	B 	C	D	E	F	G	H	I	J	K	L	M	N	O	P 	Q	R 	S	T	U	V	W	X	Y	Z	[Show all]
			User 	Host 	Password 	Global privileges Tip 	Grant 	
			pma 	localhost 	No 	SHUTDOWN 	No 	Edit Privileges
			root 	127.0.0.1 	No 	ALL PRIVILEGES 	Yes 	Edit Privileges
			root 	localhost 	No 	ALL PRIVILEGES 	Yes 	Edit Privileges
		With selected: Check All / Uncheck All

		Add a new User  <--- CLICK ON THIS!!!

	2.6 Click on "ADD NEW USER" shown above.

	2.7 For user name type in bf2statslogger, and for host pick local, and for password, put bf2. Down below make sure
	you select all privilages 
	
	3. Next Install the Bf2 dediacted server. by default it shoud install in %systemroot%\program files\EAgames\ 

	3.1 Once installed, swap out the python folder ( battlefield 2 server\python ) with the one in the "Server Files" folder included
		with this readme. ( this folder/Server Files/ )

	3.3 after that, Place the ASP folder in the "Web Files" folder included with this readme ( this folder/Web Files/ ) into your htdocs or www folder ( %systemroot%\xampp\htdocs )
        or ( %systemroot%\wamp\www )
	
	3.4 I also included the newest (Unreleased) version of BF2s Clone. If you want to use this ( Its a player stats web interface like this -> http://bf2s.com ) Then also 
        insert this into your htdocs / www folder
	
	3.4.1 To install BF2s Clone, go "localhost/yourBF2s_clone_folder/install.php"
	
	4. Next, open your browser and type in the address bar "127.0.0.1/asp" ... for the username, type: admin and the password is: admin. The username
		and password ARE CASE SENSATIVE! If you get an error saying "You are NOT Authorised to access this Page! (Ip: '...')", then you need to add
        your Ip address to the config file manually before continueing... Open the config.php file ( ASP/system/config/ ), Look for "$admin_hosts = array( ... )"
        add your servers IP address to that list... make sure to follow the format (EI: add a comma and put the IP in quotes).

	4.1 Once logged in we need to install the database...do so by clicking "install Database" under the "System" navigation pane, or there should also be a link above the main content
        warning you that a database connection could not be made, click here etc etc.

	4.2 On this next screen it will ask you for your database information. Enter the information and click "Install"

	4.3 You will see a screen pop up informing you that the database data is being installed. There are over 50,000 rows of data being installed into the Ip2Nation table,
        so depending on your hardware, this can take up to 5 minutes or even more! Just be patient and donot  close or leave the screen :)
        
    4.4 Once you get confirmation that the system installed Ok, you should click the "Dashboard" navigation link. Once there, you should see a full navigation now :)
    
    4.5 Now you should configure your system. Click "Edit Configuration" under the System navigation pane. Edit the details to your likeing and make sure you
        change the admin username and the password!

	4.6 next click "Test System" under the "System" navigation pane. Confirm the processs and wait for the results :) .. If you get 1 or 2 warnings, thats fine, but
        any errors (except for maybe the BD backup path) will need to be fixed before the system can work properly.
		
	5.  You should now edit your servers config file. It is located "<bf2 server>/python/bf2/BF2StatisticsConfig.py". If you are not hosting the ASP on the same 
        machine as the server, then you need to edit the http_backend in the BF2 server statistics config specifically.
		
	5.2 Next you need to setup your server settings. Go to battlefield 2 server\mods\bf2\setting\serversettings.con
		If you are gonna Play offline, then you leave the sv.serverIP blank. If you want to play locally with a few friends,
		then you need to import your computers IPv4 address.to get your IPv4 address, open your command prompt and type
		"ipconfig" and hit enter...scroll up till you see your IPv4 Address and copy that down. Edit all the other setting to
		your liking.
        
    5.3 Now, for client side... You need to paste the files in the "Client Files" folder included (minus the hosts file) into your bf2 client root directory.
        Create a shortcut to your BF2_Launcher.vbs and Xpack_Launcher.vbs on your desktop. Using a text editor, edit the "strLookupAddr" value to match your web server
        in both of those files... This can be set to a valid DNS host name or and IP Address

    5.4 Next, you need to take the included hosts file, and replace your current hosts file ( %systemroot%\windows\system32\drivers\etc\ ). If you cannot delete 
        your current hosts file, try renaming it. The included hosts file should not have all the strict access and privilages on it.
        
    5.5 You must start the client using the shortcut so the vbs file can add the BF2 gamespy redirect to your hosts file automatically, AND redirect BF2 to 
        your stats DB instead of the gamespy one. This will allow you to play with online accounts, use unlocks on your server, and view your progress in
        the BFHQ.
        
    5.5a There is an alternative to editing your hosts file. You can use a program called simpleDNS plus (windows only). I have uploaded this program along
         with instructions specific to using bf2statistics here (http://www.mediafire.com/?bb1z2ruq2joc3uq)
		
	5.6 LAST step :p ... All you have to do now is Copy the shortcut included on your desktop, and edit the properties
		to point to your servers exe file (BF2_w32ded.exe)
		
	6. If you want to use unlocks on your private server, then there is a few things to do. First... You need to make sure that
		anyone playing on your server, has their host files set like so "<your BF2 Server IP>   bf2web.gamespy.com". Even with hamanchi!
		If the computer is local for you, then its just 127.0.0.1. Users can now login with an online account, and play on your srever. 
		As long as the redirect is there in the hosts file, you can login with online accounts and use this stats system with unlocks.
		
	----------------------------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------------------------
													 FAQ / EDITING

	1.How do i edit the number of bots in a game and there difficulty settings?

		Y can have only up to 32 bots in a coop game. to adjust these settings,
		go to battlefield 2 server\mods\bf2\settings\serversettings.con. towards the bottom you will see the options
		to adjust these settings.

	2.how do i change the gamemodes and maps i play on?

		To easy... Go to battlefield 2 server\mods\bf2\setting\maplist.con. An example is shown here: for Conquest mode
		on map dalian plant looks like this: "mapList.append Dalian_plant gpm_cq 32"... for Co-Op it should look like 
		this: "mapList.append dalian_plant gpm_coop 16"... Note that coop only supports 16 size maps unless its a custom
		map.

	3.How do i change the requirment for earning medals and ranks?

		Open up your "bf2 server directory, in python\bf2\stats\medal_data.py" is where you can edit anything and 
		everything concerning awards and rank. Note that for awards and badges, when it says time, It's refered to in
		seconds. So 3600 is 1 hour of gameplay. Also note that ranks such as SMOC and 4 star general can be awarded
		within your database as well. Type in 127.0.0.1/asp in your address bar. Click on "Edit Players". Select your
		player, and select the new rank.
		
		*NOTE* If you remove any requirements from medals/badges/ribbbons. then it is highly suggested you edit your
			bf2stats server config "python/bf2/BF2StatisticsConfig.py" and enable "medals_force_keystring" ( medals_force_keystring = 1 ).
			Failure to do so can prevent awards from issueing at all.

	4. How do i host my server Locally again?

		Go into your bf2 server directory, \mods\bf2\settings\serversettings.con and under the sv.serverIP, input your
		computers IPv4 address. to get your IPv4 address, open your command prompt and type"ipconfig" and hit 
		enter...scroll up till you see your IPv4 Address and copy that down.

	5.How do i adjust the amount of points i get for kills and flag captures etc?

		For editing kills and those kind of stats, you have to edit batlefield 2 server\mods\bf2\python\game
		\scoringCommon.py. Now to deit the amount of points for flag captures, flag defends, neutralization
		points, battlefield 2 server\mods\bf2\python\game\gamemodes\gpm_coop.py or gpm_cq.py.
		
// **************************************************************
// Referances and Help

	If you have any issues... Please refer to the BF2statistics forums please (bf2statistics.com)
