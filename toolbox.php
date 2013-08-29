<?php
$v = 3.8;
include_once 'system.php';
$db = new Database();

$k = htmlspecialchars(strip_tags($_GET['uk']));
$utd = true;
$sv = (float) $_GET['v'];
if ( !isset($_GET['v']) || $sv < $v ) {
	$utd = false;
}
$status = simplexml_load_file('http://www.die2nite.com/xml/status');

// secure site key (d2n.sindevel.com)
$siteKey = '1047d28543db3ad80932ba020c9fe9bb';
// ingame Link
$xml = simplexml_load_file('http://www.die2nite.com/xml?k=' .$k . ';sk=' . $siteKey);

if ( !$xml ) {
//todo: error
print 'error';
return 'error';
}

?>
<html><head><title>OO toolbox</title>
<style type="text/css">
* { background: transparent; margin: 0; padding: 0;}
body { margin: 0; padding: 0; font-family: "Century Gothic", "Arial", "Trebuchet MS", Verdana, sans-serif; font-size: 12px; width: 420px; height: 100px; background: transparent; overflow: hidden; }
div { padding: 3px; }
.alert { color: #c00; font-variant: small-caps; }
.ok { color: #060; }
img { vertical-align: text-bottom; }
.col { width: 80px; float: left; }
.col1 { width: 130px; }
.hideme { display: none; }
.update { position: absolute; width: 400px; background: rgba(204,0,0,.5); color: #fff; padding: 1px 3px; font-size: 10px; top: 83px; }
.postamt { padding-top: 24px; background: transparent url('css/img/fl_mail.png') center top no-repeat; text-align: center; }
.postamt.alert { background-image: url('css/img/fl_mail_alert.gif'); }
</style>
<!--[if gte IE 5]>
<style type="text/css"> 
body {
  background-color:#000001;
  filter:Chroma(color=#000001);
}
</style>
<![endif]-->
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
</head>
<body>
<?php

// get main objects p=1
$headers = $xml->headers;
$game = $xml->headers->game;
$city = $xml->data->city;
$map = $xml->data->map;
$citizens = $xml->data->citizens;
$cadavers = $xml->data->cadavers;
$expeditions = $xml->data->expeditions;
$bank = $xml->data->bank;
$estimations = $xml->data->estimations->e;
$upgrades = $xml->data->upgrades;
$news = $xml->data->city->news;
$defense = $xml->data->city->defense;
$buildings = $xml->data->city->building;
$owner = $xml->headers->owner->citizen;
$myzone = $xml->headers->owner->myZone;

// current data array
$data = array();

// system
$data['system']['icon_url'] = (string) $headers['iconurl'];
$data['system']['avatar_url'] = (string) $headers['avatarurl'];

// current day
$data['current_day'] = (int) $game['days'];

// map size
$data['map']['height'] = (int) $map['hei'];
$data['map']['width'] = (int) $map['wid'];

// town data
$data['town']['id'] = (int) $game['id'];
$data['town']['name'] = (string) $city['city'];
$data['town']['x'] = (int) $city['x'];
$data['town']['y'] = (int) $city['y'];
$data['town']['door'] = (int) $city['door'];
$data['town']['water'] = (int) $city['water'];
$data['town']['chaos'] = (int) $city['chaos'];
$data['town']['devast'] = (int) $city['devast'];
$data['defense']['total'] = (int) $defense['total'];

// estimations
if ( isset($estimations) && !is_null($estimations) ) {
	foreach ( $estimations AS $e ) {
		$eday = (int) $e['day'];
		$emin = (int) $e['min'];
		$emax = (int) $e['max'];
		$ebest = (int) $e['maxed'];
		$data['estimations'][$eday] = array(
			'min' => $emin,
			'max' => $emax,
			'best' => $ebest,
		);
	}
}

$error = $xml->error;
$error_code = (string) $error['code'];
if ( $error_code == 'horde_attacking' ) {
	print '<span class="alert">Zombies attack your town.</span>';
	exit;
}
elseif ( $error_code == 'not_in_game' ) {
	print '<span class="alert">Do have a new citizenship yet?</span>';
	$user = $db->query(' SELECT id, name, avatar FROM dvoo_citizens WHERE scode = "'.$k.'" ');
	exit;
}
elseif ( $error_code == 'user_not_found' ) {
	print '<span class="alert">User XML not found.</span>';
	exit;
}
elseif ( $error_code != '' ) {
	print '<span class="alert">An unknown error occured. Are you dead by any chance?</span>';
	exit;
}


$door = '<div class="door"><img title="town gate" src="http://data.die2nite.com/gfx/icons/small_door_closed.gif"> ';
if ( $data['town']['door'] == 1 ) {
	$door .= '<span class="alert">Open!</span>';
} 
elseif ( $data['town']['door'] == 0 ) {
	$door .= '<span class="ok">Closed!</span>';
}
$door .= '</div>';

$defense = '<div class="defense"><img title="town defense" src="http://data.die2nite.com/gfx/icons/item_shield_mt.gif"> <span class="'.(isset($data['estimations'][$data['current_day']]['min']) ? ($data['estimations'][$data['current_day']]['max'] > $data['defense']['total'] ? 'alert' : 'ok') : '').'">'.$data['defense']['total'].'</span></div>';
$attack = '<div class="attack"><img title="attack estimation today" src="http://www.die2nite.com/gfx/forum/smiley/h_death.gif"> '.(isset($data['estimations'][$data['current_day']]['min']) && $data['estimations'][$data['current_day']]['min'] > 0 ? $data['estimations'][$data['current_day']]['min'].' - '.$data['estimations'][$data['current_day']]['max'] : '???').($data['estimations'][$data['current_day']]['best'] == 0 ? ' <img src="http://www.die2nite.com/gfx/forum/smiley/h_warning.gif" title="not yet best estimation possible" />' : '').'</div>';

$tomorrow = $data['current_day'] + 1;

if ( isset($data['estimations'][$tomorrow]['min']) && $data['estimations'][$tomorrow]['min'] > 0 ) {
	$attack .= '<div class="attack"><img title="attack estimation tommorrow" src="http://www.die2nite.com/gfx/forum/smiley/h_death.gif"> '.$data['estimations'][$tomorrow]['min'].' - '.$data['estimations'][$tomorrow]['max'].($data['estimations'][$tomorrow]['best'] == 0 ? ' <img src="http://www.die2nite.com/gfx/forum/smiley/h_warning.gif" title="not yet best estimation possible" />' : '').'</div>';
}

$ct = 0; // total
$co = 0; // out
$cd = 0; // door
$ch = 0; // hero
$cb = 0; // ban

foreach ( $citizens->children() AS $ca ) {
	$ct++;
	if ( (int) $ca['out'] == 1 ) { $co++; }
	if ( (int) $ca['out'] == 1 && $data['town']['x'] == (int) $ca['x'] && $data['town']['y'] == (int) $ca['y'] ) { $cd++; }
	if ( (int) $ca['ban'] == 1 ) { $cb++; }
	if ( (int) $ca['hero'] == 1 ) { $ch++; }
}
$citizen = '<div class="citizen"><img title="total residents" src="http://www.die2nite.com/gfx/forum/smiley/h_human.gif"> '.$ct.'</div><div><img title="residents outside" src="http://www.die2nite.com/gfx/forum/smiley/h_camp.gif"> '.$co.'</div><div><img title="residents at the gate" src="http://data.die2nite.com/gfx/icons/small_door_closed.gif"> '.$cd.'</div>';

$water = '<div class="water"><img title="water in well" src="http://www.die2nite.com/gfx/forum/smiley/h_well.gif" /> '.$data['town']['water'].'</div>';

$bdef = 0;
$bwat = 0;
foreach ( $bank->children() AS $bia ) {
	$bi_name = (string) $bia['name'];
	$bi_count = (int) $bia['count'];
	$bi_id = (int) $bia['id'];
	$bi_cat = (string) $bia['cat'];
	$bi_img = (string) $bia['img'];
	$bi_broken = (int) $bia['broken'];

	if ( $bi_cat == 'Armor' ) { $bdef += $bi_count; }
	if ( $bi_id == 1 ) { $bwat = $bi_count; }

}
$water .= '<div class="water"><img title="water in bank" src="http://www.die2nite.com/gfx/forum/smiley/h_water.gif" /> '.$bwat.'</div>';
$water .= '<div class="water"><img title="defensive items" src="http://www.die2nite.com/gfx/forum/smiley/h_guard.gif" /> '.$bdef.'</div>';

// mail
$q = ' SELECT COUNT(*) FROM dvoo_fl_mailbox WHERE receiver = '.((int) $owner['id']).' AND `read` IS NULL ';
$r = $db->query($q);
$newmail = $r[0][0];
$q = ' SELECT COUNT(*) FROM dvoo_fl_invite WHERE b = '.((int) $owner['id']).' ';
$r = $db->query($q);
$newinv = $r[0][0];

if ( $utd === false ) { print '<div class="update"><strong>UPDATE NOW!</strong> Toolbox version '.$v.' is available! (Your current version is '.((string) $_GET['v']).')</div>'; } ?>
<div class="col col1">
<?php
print $door;
print $defense;
print $attack;
?>
</div><div class="col">
<?php
print $citizen;
?>
</div><div class="col">
<?php
print $water;
?>
</div><div class="col postamt <?php print ($newmail + $newinv > 0 ? ' alert' : ''); ?>">
<?php
print '<strong>Oval&nbsp;Office Postal&nbsp;service</strong><br/><span class="'.($newmail > 0 ? 'alert' : '').'">Messages: '.$newmail.'</span>';
if ( $newinv > 0 ) {
	print '<br/><span class="'.($newinv > 0 ? 'alert' : '').'">Invites: '.$newinv.'</span>';
}
?>
</div>
<hr style="clear:both;" class="hideme" />
<!-- Piwik --> 
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://sindevel.com/piwik/" : "http://sindevel.com/piwik/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 3);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://sindevel.com/piwik/piwik.php?idsite=3" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
</body>
</html>