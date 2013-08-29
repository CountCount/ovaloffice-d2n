<?php
include_once 'system.php';

$db = new Database();

$events = array();
$events[1] = array(
	'title' => 'Clash of the Titans',
	'logo' => '<img class="eventLogo" src="http://www.die2nite.com/file/33.dat" />',
	'towns' => array(
		18416, 18425, 18413, 18421, 18404, 18410
	),
	'desc' => 'This is the main comparison tool for the participating towns of the event "Clash of the Titans" which starts on <strong style="color:#00c;">January, 22<sup>nd</sup> 2012</strong>. The currently displayed towns are for developing purposes only.',
);
$signup = array();
$signup[1] = array(
	'title' => 'Clash of the Titans',
	'logo' => '<img class="eventLogo" src="http://www.die2nite.com/file/33.dat" />',
	'options' => array(
		'Guardians' => '<img src="'.t('GAMESERVER_ICON').'r_jguard.gif" />',
		'Scavengers' => '<img src="'.t('GAMESERVER_ICON').'r_jcolle.gif" />',
		'Scouts' => '<img src="'.t('GAMESERVER_ICON').'r_jrangr.gif" />',
		'Survivalists' => '<img src="'.t('GAMESERVER_ICON').'r_jermit.gif" />',
		'Tamers' => '<img src="'.t('GAMESERVER_ICON').'r_jtamer.gif" />',
		'Technicians' => '<img src="'.t('GAMESERVER_ICON').'r_jtech.gif" />',
	),
	'desc' => 'This is the main comparison tool for the participating towns of the event "Clash of the Titans" which starts on <strong style="color:#00c;">January, 22<sup>nd</sup> 2012</strong>. This is the (unofficial and still deactivated) signup form. <span onclick="eventSpy(1);" style="cursor:pointer;text-decoration:underline;color:#00c;">Click here for a test case</span> (current towns).',
);

// get event
$e = (int) $_REQUEST['e'];
$k = (string) $_REQUEST['k'];
$o = (string) $_REQUEST['o'];
$s = (int) $_REQUEST['s'];
if ( $k != "" && $e > 0 && !in_array($e,array(11,13)) ) {
	if ( $o == 'none' ) {
		$q = ' DELETE FROM dvoo_events_signup WHERE event = '.$e.' AND user = "'.$k.'" LIMIT 1 ';
		$db->iquery($q);
	}
	elseif ( $o != '' ) {
		$q = ' SELECT id FROM dvoo_citizens WHERE scode = "'.$k.'" ';
		$r = $db->query($q);
		if ( !isset($r[0]) || $r[0][0] != 0 ) {
			$q = ' INSERT INTO dvoo_events_signup VALUES ('.$e.', "'.$k.'", "'.$o.'", '.time().') ON DUPLICATE KEY UPDATE `option` = "'.$o.'", stamp = '.time().' ';
			$db->iquery($q);
		}
	}
}

print '1';
return '1';