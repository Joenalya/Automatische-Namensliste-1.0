<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB")){die("Direct initialization of this file is not allowed.");}

// hooks
$plugins->add_hook("misc_start", "rpgnameliste_misc");

function rpgnameliste_info()
{
    return array(
        "name"            => "Automatische Namensliste (RPG-Plugin)",
        "description"    => "Erstellt eine automatische Liste, die nach Vor- und Nachnamen, sowie Usernamen sotiert.",
        "website"        => "https://github.com/Joenalya",
        "author"        => "Joenalya aka. Anne",
        "authorsite"    => "https://github.com/Joenalya",
        "version"        => "1.0",
        "codename"        => "rpgnameliste",
        "compatibility" => "18"
    );
}

function rpgnameliste_install()
{
	global $db, $mybb, $cache;
	
	// create settinggroup
	$setting_group = array(
    	'name' => 'rpgnamelistecp',
    	'title' => 'Automatische Namensliste',
    	'description' => 'Einstellungen für die automatische Namensliste.',
    	'disporder' => -1, // The order your setting group will display
    	'isdefault' => 0
	);
	
	// insert settinggroup into database
	$gid = $db->insert_query("settinggroups", $setting_group);
	
	// create settings
	$setting_array = array(
    	'rpgnamelistecp_activate' => array(
        	'title' => 'Soll die Namesliste aktiviert werden?',
        	'description' => '',
        	'optionscode' => 'yesno',
        	'value' => '1', // Default
        	'disporder' => 1
    	),
    	'rpgnamelistecp_genderfid' => array(
        	'title' => 'Geschlecht-Profilfeld',
        	'description' => 'Hier die Field-ID des Geschlecht-Feld angeben.',
        	'optionscode' => 'text',
        	'value' => '99', // Default
        	'disporder' => 2
    	),
		'rpgnamelistecp_doppel' => array(
        	'title' => 'Doppelte Vornamen?',
        	'description' => 'Soll die Funktion für Doppelte Vornamen aktiv sein?',
        	'optionscode' => 'yesno',
        	'value' => '0', // Default
        	'disporder' => 3
		),
    	'rpgnamelistecp_doppelfid' => array(
        	'title' => 'Doppelte Vornamen-Profilfeld',
        	'description' => 'Hier die Field-ID des Doppelte Vornamen-Feld angeben.',
        	'optionscode' => 'text',
        	'value' => '99', // Default
        	'disporder' => 4
    	),
    	'rpgnamelistecp_username' => array(
        	'title' => 'Soll der Spielernamen-Zusatz aktiviert werden?',
        	'description' => '',
        	'optionscode' => 'yesno',
        	'value' => '1', // Default
        	'disporder' => 5
    	),
		'rpgnamelistecp_usernamefid' => array(
        	'title' => 'Spielernamen-Profilfeld',
        	'description' => 'Hier die Field-ID des Spielernamen-Feld angeben.',
        	'optionscode' => 'text',
        	'value' => '99', // Default
        	'disporder' => 6
    	),
    	'rpgnamelistecp_asian' => array(
        	'title' => 'Soll das Asiatische Namessystem (Nachname Vorname) aktiviert werden?',
        	'description' => '',
        	'optionscode' => 'yesno',
        	'value' => '0', // Default
        	'disporder' => 7
    	),
	);

	// insert settings into database
	foreach($setting_array as $name => $setting)
	{
    	$setting['name'] = $name;
    	$setting['gid'] = $gid;

    	$db->insert_query('settings', $setting);
	}

	// Don't forget this!
	rebuild_settings();
	
    // templates
    $insert_array = array(
        'title'        => 'misc_rpgnameliste',
        'template'    => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Namensliste</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
	<tr><td class="thead" colspan="{$colspan}"><strong>Namesliste</strong></td></tr>
	<tr>
		<td class="tcat" width="25%"><strong>Männliche Vornamen</strong></td>
		<td class="tcat" width="25%"><strong>Weibliche Vornamen</strong></td>
		<td class="tcat" width="35%"><strong>Nachnamen</strong></td>
		{$usernamepart}
	</tr>
	
	<tr><td class="trow_sep smalltext" colspan="{$colspan}"><strong>A - B - C - D - E</strong></td></tr>
	<tr>
		<td class="trow2">{$first_name1A}</td>
		<td class="trow2">{$first_name1B}</td>
		<td class="trow2">{$second_name1}</td>
		{$usernamepart1}
	</tr>
	
	<tr><td class="trow_sep smalltext" colspan="{$colspan}"><strong>F - G - H - I - J</strong></td></tr>
	<tr>
		<td class="trow2">{$first_name2A}</td>
		<td class="trow2">{$first_name2B}</td>
		<td class="trow2">{$second_name2}</td>
		{$usernamepart2}
	</tr>
	
	<tr><td class="trow_sep smalltext" colspan="{$colspan}"><strong>K - L - M - N - O</strong></td></tr>
	<tr>
		<td class="trow2">{$first_name3A}</td>
		<td class="trow2">{$first_name3B}</td>
		<td class="trow2">{$second_name3}</td>
		{$usernamepart3}
	</tr>
	
	<tr><td class="trow_sep smalltext" colspan="{$colspan}"><strong>P - Q - R - S - T</strong></td></tr>
	<tr>
		<td class="trow2">{$first_name4A}</td>
		<td class="trow2">{$first_name4B}</td>
		<td class="trow2">{$second_name4}</td>
		{$usernamepart4}
	</tr>
	
	<tr><td class="trow_sep smalltext" colspan="{$colspan}"><strong>U - V - W - X - Y - Z</strong></td></tr>
	<tr>
		<td class="trow2">{$first_name5A}</td>
		<td class="trow2">{$first_name5B}</td>
		<td class="trow2">{$second_name5}</td>
		{$usernamepart5}
	</tr>
	
</table>
{$footer}
</body>
</html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'misc_rpgnameliste_bit',
        'template'    => $db->escape_string('<span class="smalltext"><b>{$Name_Done}{$User_Done}</b></span> <br />'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);	
	
}

function rpgnameliste_is_installed()
{
    global $mybb;
	if(isset($mybb->settings['rpgnamelistecp_activate'])) {
        return true;
    }
    return false;
}

function rpgnameliste_uninstall() 
{
	global $db, $cache;
	
    // drop templates
    $db->delete_query("templates", "title LIKE '%rpgnameliste%'");
	
	// drop settings
	$db->delete_query('settings', "name LIKE '%rpgnamelistecp_%'");
	$db->delete_query('settinggroups', "name = 'rpgnamelistecp'");
}

function rpgnameliste_activate()
{
    global $mybb;
}
	
function rpgnameliste_deactivate()
{
    global $mybb;
	
	// Don't forget this
	rebuild_settings();
}

function rpgnameliste_misc() {
	 global $mybb, $db, $lang, $templates, $headerinclude, $header, $footer, $theme;
	 
	 $plugin_active = (int)$mybb->settings['rpgnamelistecp_activate'];
	 $user_active = (int)$mybb->settings['rpgnamelistecp_username'];
	 $double_active = (int)$mybb->settings['rpgnamelistecp_doppel'];
	 $asian_active = (int)$mybb->settings['rpgnamelistecp_asian'];
	 
	 $get_gender = (int)$mybb->settings['rpgnamelistecp_genderfid'];
	 $get_username = (int)$mybb->settings['rpgnamelistecp_usernamefid'];
	 $get_double = (int)$mybb->settings['rpgnamelistecp_doppelfid'];
	 
	 $doublefid = "fid{$get_double}";
	 $genderfid = "fid{$get_gender}";
	 $usernamefid = "fid{$get_username}";
	 
	 if($mybb->input['action'] == "rpgnameliste") {
		 
		 
	if($asian_active == "1") {
		
		// first name
		$firstname = $db->query("  
		SELECT * FROM ".TABLE_PREFIX."users
		LEFT JOIN ".TABLE_PREFIX."userfields 
		ON ".TABLE_PREFIX."userfields.ufid = ".TABLE_PREFIX."users.uid;");
		while($name = $db->fetch_array($firstname)) {
			
			$fullname = htmlspecialchars($name['username']);
			
			$gender = $name[$genderfid];

			$names = explode(" ", $fullname);
			$Lastname = array_shift($names);
			$Name_Done = implode(" ", $names);
							
			$array = explode(" ", $fullname, 2);
			$lastname[] = $array[0];
			
			if(preg_match("/^(A|a|B|b|C|c|D|d|E|e)/", $Name_Done) && ($gender == "männlich")) { eval("\$first_name1A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};		
			if(preg_match("/^(F|f|G|g|H|h|I|i|J|j)/", $Name_Done) && ($gender == "männlich")) { eval("\$first_name2A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(K|k|L|l|M|m|N|n|O|o)/", $Name_Done) && ($gender == "männlich")) { eval("\$first_name3A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(P|p|Q|q|R|r|S|s|T|t)/", $Name_Done) && ($gender == "männlich")) { eval("\$first_name4A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $Name_Done) && ($gender == "männlich")) { eval("\$first_name5A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			
			if(preg_match("/^(A|a|B|b|C|c|D|d|E|e)/", $Name_Done) && ($gender == "weiblich")) { eval("\$first_name1B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};	
			if(preg_match("/^(F|f|G|g|H|h|I|i|J|j)/", $Name_Done) && ($gender == "weiblich")) { eval("\$first_name2B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(K|k|L|l|M|m|N|n|O|o)/", $Name_Done) && ($gender == "weiblich")) { eval("\$first_name3B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(P|p|Q|q|R|r|S|s|T|t)/", $Name_Done) && ($gender == "weiblich")) { eval("\$first_name4B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $Name_Done) && ($gender == "weiblich")) { eval("\$first_name5B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
		}	
		
		// last name
		$lastname = array_unique($lastname);
		asort($lastname);
		foreach($lastname as $Name_Done){
			if(preg_match("/^(A|a|B|b|C|c|D|d|E|e)/", $Name_Done)) { eval("\$second_name1 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};		
			if(preg_match("/^(F|f|G|g|H|h|I|i|J|j)/", $Name_Done)) { eval("\$second_name2 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(K|k|L|l|M|m|N|n|O|o)/", $Name_Done)) { eval("\$second_name3 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(P|p|Q|q|R|r|S|s|T|t)/", $Name_Done)) { eval("\$second_name4 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $Name_Done)) { eval("\$second_name5 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
		}
		
	}
	else {

		// first name
		$firstname = $db->query("  
		SELECT * FROM ".TABLE_PREFIX."users
		LEFT JOIN ".TABLE_PREFIX."userfields 
		ON ".TABLE_PREFIX."userfields.ufid = ".TABLE_PREFIX."users.uid
		ORDER BY username ASC;");
		while($name = $db->fetch_array($firstname)) {
			
			$fullname = htmlspecialchars($name['username']);
			
			$gender = $name[$genderfid];
			$double = $name[$doublefid];
			
			if($double == "Ja" AND $double_active != "0"){
				$names = explode(" ", $fullname);
				$vorname = array_shift($names);
				$zweitervorname = array_shift($names);
				$Name_Done = "$vorname $zweitervorname";
				$nachnameplayer = implode(" ", $names); 
				
				$array = explode(" ", $fullname, 3);
				$lastname[] = "$array[3]";
			}
			else{
				$names = explode(" ", $fullname);
				$Name_Done = array_shift($names);
				$nachnameplayer = implode(" ", $names);
				
				$array = explode(" ", $fullname, 2);
				$lastname[] = $array[1];
			}
			
			if(preg_match("/^(A|a|B|b|C|c|D|d|E|e)/", $fullname) && ($gender == "männlich")) { eval("\$first_name1A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};		
			if(preg_match("/^(F|f|G|g|H|h|I|i|J|j)/", $fullname) && ($gender == "männlich")) { eval("\$first_name2A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(K|k|L|l|M|m|N|n|O|o)/", $fullname) && ($gender == "männlich")) { eval("\$first_name3A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(P|p|Q|q|R|r|S|s|T|t)/", $fullname) && ($gender == "männlich")) { eval("\$first_name4A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $fullname) && ($gender == "männlich")) { eval("\$first_name5A .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			
			if(preg_match("/^(A|a|B|b|C|c|D|d|E|e)/", $fullname) && ($gender == "weiblich")) { eval("\$first_name1B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};	
			if(preg_match("/^(F|f|G|g|H|h|I|i|J|j)/", $fullname) && ($gender == "weiblich")) { eval("\$first_name2B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(K|k|L|l|M|m|N|n|O|o)/", $fullname) && ($gender == "weiblich")) { eval("\$first_name3B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(P|p|Q|q|R|r|S|s|T|t)/", $fullname) && ($gender == "weiblich")) { eval("\$first_name4B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $fullname) && ($gender == "weiblich")) { eval("\$first_name5B .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
		}
		
		// last name
		$lastname = array_unique($lastname);
		asort($lastname);
		foreach($lastname as $Name_Done){
			if(preg_match("/^(A|a|B|b|C|c|D|d|E|e)/", $Name_Done)) { eval("\$second_name1 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};		
			if(preg_match("/^(F|f|G|g|H|h|I|i|J|j)/", $Name_Done)) { eval("\$second_name2 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(K|k|L|l|M|m|N|n|O|o)/", $Name_Done)) { eval("\$second_name3 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(P|p|Q|q|R|r|S|s|T|t)/", $Name_Done)) { eval("\$second_name4 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $Name_Done)) { eval("\$second_name5 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
		}
	}	
		
	if($user_active == "1") {
		
		// user name
		$username=$db->query("
		  SELECT * FROM mybb_users
		  LEFT JOIN mybb_userfields
		  ON mybb_users.uid = mybb_userfields.ufid
		  WHERE as_uid LIKE '0'
		  ORDER BY $usernamefid ASC"
		  );
		  while($player = $db->fetch_array($username)) {
			  $User_Done = $player[$usernamefid];
			  $Name_Done ="";
		
			if(preg_match("/^(A|a|B|b|C|c|D|d|E|e)/", $User_Done)) { eval("\$user_name1 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};		
			if(preg_match("/^(F|f|G|g|H|h|I|i|J|j)/", $User_Done)) { eval("\$user_name2 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(K|k|L|l|M|m|N|n|O|o)/", $User_Done)) { eval("\$user_name3 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(P|p|Q|q|R|r|S|s|T|t)/", $User_Done)) { eval("\$user_name4 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
			if(preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $User_Done)) { eval("\$user_name5 .= \"".$templates->get("misc_rpgnameliste_bit")."\";");};
		}
		
			$colspan = "4";
			$usernamepart = "<td class=\"tcat\" width=\"20%\"><strong>Usernamen</strong></td>";
			$usernamepart1 = "<td class=\"trow2\">{$user_name1}</td>";
			$usernamepart2 = "<td class=\"trow2\">{$user_name2}</td>";
			$usernamepart3 = "<td class=\"trow2\">{$user_name3}</td>";
			$usernamepart4 = "<td class=\"trow2\">{$user_name4}</td>";
			$usernamepart5 = "<td class=\"trow2\">{$user_name5}</td>";
		} else {
			$colspan = "3";
			$usernamepart = "";
		}
		
		eval("\$page = \"".$templates->get("misc_rpgnameliste")."\";");
		output_page($page);
        
    }
}
