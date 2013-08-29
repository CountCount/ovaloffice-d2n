<?php
include 'error.php';

/* ##### QUICK CONFIG ##### */
$maintenance = 0; // default: 0, values: 0, 1
$version = '5.3'; // current OO version
$language = 'en'; // current options: de, en

/* ######################## */
$tempaccess = false;
if ( isset($_GET['tkey']) ) {
	$key = $_GET['tkey'];
	$tempaccess = true;
}
elseif ( isset($_POST['key']) ) {
	$data_string = 'v=220&r=dv&p=2&k=' . secureKey($_POST['key']);
	$dat2_string = 'v=220&r=dv&p=2';
	setcookie("key",secureKey($_POST['key']),time()+(3*86400));
	$key = $_POST['key'];
}
elseif ( isset($_GET['key']) ) { 
	$data_string = 'v=220&r=dv&p=2&k=' . secureKey($_GET['key']);
	$dat2_string = 'v=220&r=dv&p=2';
	setcookie("key",secureKey($_GET['key']),time()+(3*86400));
	$key = $_GET['key'];
}

elseif ( isset($_COOKIE['key']) ) {
	$data_string = 'v=220&r=co&p=2&k=' . secureKey($_COOKIE['key']);
	$dat2_string = 'v=220&r=co&p=2';
	setcookie("key",secureKey($_COOKIE['key']),time()+(3*86400));
	$key = $_COOKIE['key'];
}
else {
	$data_string = 'v=220&p=0&r='.urlencode($_SERVER['HTTP_REFERER']);
	$key = '';
}
$openmail = (isset($_GET['openmail']) ? $_GET['openmail'] : 0);

// start system
ini_set('display_errors', 0);
// session start
session_start();
include_once 'system.php';
$db = new Database();

// exit if maintenance
if ( $maintenance == 1 ) {
	print t('MAINTENANCE_MSG');
	exit;
}

if ( $tempaccess ) {
	$uid = $_GET['user'];
	$q = ' SELECT c.scode FROM dvoo_citizens c INNER JOIN dvoo_tempkeys t ON t.uid = c.id WHERE c.id = '.$uid.' AND t.tempkey = "'.$key.'" AND t.stamp >= '.time().' ';
	$r = $db->query($q);
	if ( is_array($r[0]) ) {
		$key = $r[0][0];
		$data_string = 'v=220&r=tk&p=2&k=' . $key;
		$dat2_string = 'v=220&r=tk&p=2';
	}
	else {
		$data_string = 'v=220&p=0&r='.urlencode($_SERVER['HTTP_REFERER']);
		$key = '';
	}
}
if ( $key != '' ) {
	$r = $db->query('SELECT name FROM dvoo_citizens WHERE scode = "'.$key.'"');
	if ( is_array($r[0]) ) {
		$fname = $r[0][0];
	}
}
else {
	$fname = 'Guest';
}

// html header
print '<?xml version="1.0" encoding="utf-8"?>';
print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

// head
print '<head>
	<title>'.$fname.' | Oval Office</title>
	<link rel="canonical" href="http://d2n.sindevel.com/oo/" />
	<script type="text/javascript" src="js/jquery-1.5.min.js"></script>
	<script type="text/javascript" src="js/jquery.event.drag-1.5.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
	<script type="text/javascript" src="js/slimbox2.js"></script>
	<link rel="stylesheet" href="css/slimbox2.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/alert.js"></script>
	<link rel="stylesheet" href="css/alert.css" type="text/css" media="screen" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link type="text/css" href="css/oo2.css?v='.$version.'" rel="stylesheet" />
	<script src="js/RGraph/RGraph.common.core.js"></script>
	<script src="js/RGraph/RGraph.common.context.js"></script>
	<script src="js/RGraph/RGraph.common.annotate.js"></script>
	<script src="js/RGraph/RGraph.common.effects.js"></script>
	<script src="js/RGraph/RGraph.common.tooltips.js"></script>
	<script src="js/RGraph/RGraph.common.zoom.js"></script>
	<script src="js/RGraph/RGraph.line.js"></script>
	<script src="js/RGraph/RGraph.scatter.js"></script>
</head>';

// body/container
print '<body>';
?>
	<div id="container-wrapper">
	<div id="spy"><div id="spy-content"></div></div>
	<div id="knife"></div>
	<div id="container-head"></div>
	<div id="container">
	<div id="newlogo"><p><?php print t('VERSION').' '.$version; ?></p></div>
	
		<ul id="tabs">
			<li id="link_apoq" class="event-button" style="top:0px;" onclick="eventSpy(14);"><img class="OLD-eventLogo" src="img/apocalympics.png" /></li>
      <li id="link_cott2" class="event-button" style="top:140px;" onclick="eventSpy(11);"><img class="OLD-eventLogo" src="img/cott2_button.png" /></li>
			<li id="link_bots" class="event-button" style="top:280px;" onclick="eventSpy(7);"><img class="OLD-eventLogo" src="img/bots_button.png" /></li>
			<li id="link_cott" class="event-button" style="top:420px;" onclick="eventSpy(6);"><img class="OLD-eventLogo" src="img/cott_button.png" /></li>
			<li id="link_office5" class="empty"><a href="#office5">Bank</a></li>
			<li id="link_office4" class="empty"><a href="#office4">Citizens</a></li>
			<li id="link_office7" class="empty"><a href="#office7">Construction Yard</a></li>
			<li id="link_office6" class="empty" style="clear:left;"><a href="#office6">Statistics</a></li>
			<li id="link_office1"><a href="#office1">Foyer</a></li>
			<li id="link_office8" class="empty"><a href="#office8">Lounge</a></li>
			
		</ul>
	<div id="intro">
		<div id="refresh"><a title="Refresh display only" href="javascript:processXML(0);"></a></div><div id="update"><a title="Update current zone (download XML from D2N)" href="javascript:processXML(1);"></a></div>
<div id="mailbox-wrapper"><div id="mailbox" class="icon"><a href="javascript:void(0);" onclick="$('#mailbox').toggleClass('icon postbox');" id="mailbox-toggle"></a><?php include 'mailbox.inc.php'; ?></div></div>
		<span id="headtownday"></span>
	</div>
		<div id="tabcontents">
		<div id="office1" class="tabcontent">
				<?php 
				print '<ul id="sub-foyer" class="subtabs hideme">
					<li><a href="#foyer-intro">'.t('FOYER_INTRO').'</a></li>
					<li><a id="ooidtl" href="#foyer-id">'.t('FOYER_ID').'</a></li>
					<li><a href="#foyer-gm">'.t('FOYER_GM').'</a></li>
				</ul>';

				print '<div id="foyer-intro" class="subtabcontent">';
				?>
			<div class="clearfix">
				<h2>Welcome to the <em>Oval Office</em>!</h2>
				<p>The doors are wide open and smiling personel asks you to enter the new building. First you see a big foyer, a helpdesk and some doors. A young woman advances and starts to explain: "The Oval Office is at your service. This new institutions aims at making your hard life in the desert as pleasant as possible. Over there is the <strong>bank</strong> information terminal providing stats on your town's items. Right next to that is a blackboard showing your fellow citizens' status."</p>
				<p>"Behind that next door you'll find the <strong>construction yard</strong> bureau where you can apply for building permits. The large door opens to the <strong>statistics</strong> office where all zombie attacks are registered as well as soul points, distinctions, titles and towns."</p>
				<p>"If you are too overwhelmed, really exhausted or just in need of communication, you may enter the <strong>lounge</strong>. There you can chat with other visitors or leave feedback for us so we can enhance our services. Please feel free to nip on some cocktails which are randomly served by our automatic cocktail robot."</p>
				<p>You take a deep breath after this compressed speech and look around again. Right next to the help desk you discover a sign advertising some free tools to enhance your game experience.</p>
			</div>
		</div>
		<div id="foyer-id" class="subtabcontent hideme">
			<div class="clearfix">
				<h3 id="CitizenIdentificationHeader">Identification in progress...</h3>
				<div id="CitizenIdentificationContent" class="loading"></div>
			</div>
		</div>
		<div id="foyer-gm" class="subtabcontent hideme">
			<div class="clearfix">
				<h2>Addons for a better game experience</h2>
				<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;overflow:hidden;">
					<h3><span style="color:#c00;">NEW</span> Time is precious</h3>
					<p><strong>You are leading expeditions every day? Always have someone in your escort? Exploring ruins every other day?</strong><br/>Then this is for you: <a href="http://userstyles.org/styles/58845/die2nite-open-rucksack" target="_new">D2N open rucksack</a>. This Stylish addon keeps all rucksacks open at any time. No need to open your rucksack to use that serrated knife or eat something. Check it out today!</p>
				</div>
				<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;overflow:hidden;">
					<h3>Google Chrome extension</h3>
					<p>There's an <a href="https://chrome.google.com/webstore/detail/mcbnodoolliadkflmgoebfepeehmelnj">awesome new Chrome extension</a> programmed by <strong>simast</strong>. I encourage you to try it. The extension itself keeps your current zone synchronized with <strong>Atlas AND the Oval Office</strong>. No need to update two maps separatedly, actually no need to do anything as this extension does ALL the work.</p>
					<h3 style="margin-top:1.5em;">Mozilla Firefox plugin</h3>
					<p><strong>isaaclw</strong> provides a <a href="https://addons.mozilla.org/en-US/firefox/addon/die2nite-agent/">Firefox plugin</a> which makes it easy for you to update <strong>the Oval Office as well as Atlas</strong>. You can update the maps by right-clicking the D2N page or use the provided toolbar button.</p>
					<h3 style="margin-top:1.5em;">Greasemonkey Toolbox Script</h3>
					<p>You may download a <a href="/ovaloffice/js/ext/oo_toolbox.user.js">greasemonkey script</a> to enhance your D2N experience while scavenging in the desert. The script displays various stats directly in D2N (normally not available while out of town) which looks similar to this:<br/><img src="img/d2ntoolbox.png" /><br/>
					The left column shows gate status, town defense and attack estimation. Middle column has total numbers of citizens alive, citizens outside and citizens standing at the gate. Finally, right column informs about water in well and bank as well as number of defensive items.</p>
				</div>
				<br/>
				
				
			</div>
		</div>
		</div>
			
	
			
			<div id="office4" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office5" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office6" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office7" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office8" class="tabcontent hideme"><div class="loading">
			</div></div>
			
		</div>
		
	
		<div class="extapp clearfix">
			<h3>External buildings</h3>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=14">From Dusk Till Dawn</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=12">Atlas</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=15">External Map</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=13">Zombie Survival Guide</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=4">Die2Nite Calculator</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=6">Nitelight</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=7">Survivors Handbook</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=9">Day of Dehydratation</a>
			<a target="_new" href="http://www.die2nite.com/#disclaimer?id=18">Die2Nite Herald</a>
			<a target="_new" href="http://die2nitewiki.com/">Wiki</a>
			<a target="_new" href="http://die2nite.net/index.php">Die2Nite.net</a>
			
			<br/>
		</div>
		
</div>	
<div id="container-foot"></div>
</div>

<div id="disclaimer"><div style="float:right;margin:6px 6px 12px 12px;text-decoration:none;border:none;text-align:center;"><g:plusone></g:plusone><br/><br/>
<a href="http://flattr.com/thing/396538/The-Oval-Office" target="_blank">
<img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a>
<br/><br/>
design by&nbsp;&nbsp;&nbsp;<a href="http://buntmacher.net" target="_blank" style="text-decoration:none;border:none;">
<img src="/oo/css/img/bm-logo.png" alt="Logo buntmacher" title="buntmacher" border="0" width="100" style="vertical-align:text-bottom;" /></a><br/>
</div>
"Oval Office" is a fan project for the survival browser game <a href="http://www.die2nite.com" target="_new">Die2Nite</a>.<br/>I give no liability for any data being complete, correct and/or up-to-date.<br/>
Most used icons are taken from <a href="http://www.die2nite.com" target="_new">Die2Nite</a> and hence are copyrighted by <a href="http://www.motion-twin.com/" target="_new">Motion Twin</a>.<br/>
Coding: <a href="mailto:countcount.cc@gmail.com">SinSniper / CountCount</a><br/>
Design: <a href="http://buntmacher.net" target="_new">buntmacher</a><br/>
<em>This project has been actively supported by </em> <span style="color:#333;">copafenris</span>, <span style="color:#333;">haase</span>, <span style="color:#333;">Juliezu</span>, <span style="color:#333;">wurststinker</span>, <span style="color:#333;">Nyrno</span>, <span style="color:#333;">SaintMike</span> &amp; 7 unknown players
</div>

<div id="dynascript"></div>
	<script type="text/javascript">
				var office = {};
				office["1"] = true;
				office["3"] = false;
				office["4"] = false;
				office["5"] = false;
				office["6"] = false;
				office["7"] = false;
				office["8"] = false;

				var phpreg = {};
				phpreg["3"] = 'rb.main';
				phpreg["4"] = 'ema.main';
				phpreg["5"] = 'bank';
				phpreg["6"] = 'stat';
				phpreg["7"] = 'bau';
				phpreg["8"] = 'wb.main';
				
				var loading = '<div class="loading"></div>';
				var amt = '#office1';
				$("ul#tabs li a").click(function (e) { 
					e.preventDefault();
					var newAmt = $(this).attr('href');
					//var lOf = 'loadO' + newAmt.substr(2);
					//lOf(userid);
					//window[lOf](userid);
					loadOffice(newAmt.substr(7),userid);
					$(amt).fadeOut();
					$(newAmt).fadeIn('slow');
					amt = newAmt;
				});
				
				$("ul#tabs li a").hover(function () {
					$(this).addClass("hilitetab");
				}, function () {
					$(this).removeClass("hilitetab");
				});

				
				function processXML(u) {
					$('#infoblock_zone').remove();
					$('#infoblock-zone').remove();
					$('#CitizenIdentificationContent').html(' ').addClass('loading');
					// process XML
					var oo_xml = $.ajax({
						type: 'POST',
						url: 'xml.ajax.php',
						data: '<?php print $data_string; ?>&u='+u,
						success: function(msg) {
							$('#CitizenIdentificationHeader').html('Identification completed.');
							$('#CitizenIdentificationContent').html(msg);
							$('#ooidtl').click();
							refreshOffice();
						}
					});
				}
				
				function loadTabContent(u) {	
					//loadOffice2(u);
					//loadOffice3(u);
					//loadOffice4(u);
					//loadOffice5(u);
					//loadOffice6(u);
					//loadOffice7(u);
					//loadOffice8(u);
				}
				
				function refreshOffice() {
					for ( i = 3; i < 9; i++ ) {
						office[i] = false;
						loadOffice(i,userid);
					}
				}
				
				function loadOffice(i,u) {
					if ( office[i] == false ) {
						// load map content
						$('#link_office'+i).addClass("empty");
						$('#office'+i).html(loading);
						var ooo = $.ajax({
							type: 'POST',
							url: phpreg[i]+'.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#infoblock-zone').remove();
								$('#office'+i).html(msg);
								$('#link_office'+i).removeClass("empty");
							}
						});
						office[i] = true;
					}
				}
				
			
				function loadDeadContent(u) {	
					var loading = '<div class="loading"></div>';
					office["6"] = true;
					office["8"] = true;
					// load chat content
					$('#link_office8').addClass("empty");
					$('#office8').html(loading);
					var oo_fb = $.ajax({
						type: 'POST',
						url: 'wb.main.ajax.php',
						data: '<?php print $dat2_string; ?>&u='+u,
						success: function(msg) {
							$('#office8').html(msg);
							$('#link_office8').removeClass("empty");
						}
					});
					
					// load stat content
					$('#link_office6').addClass("empty");
					$('#office6').html(loading);
					var oo_fa = $.ajax({
						type: 'POST',
						url: 'stat.ajax.php',
						data: '<?php print $dat2_string; ?>&u='+u,
						success: function(msg) {
							$('#office6').html(msg);
							$('#link_office6').removeClass("empty");
						}
					});
				}
				
				var curF = "#foyer-intro";
				$("ul#sub-foyer li a").click(function (e) { 
					e.preventDefault();
					var newF = $(this).attr("href");
					$(curF).fadeOut(100, function() { $(newF).fadeIn("slow", function() { $('#sub-foyer').slideDown("slow"); }); });
					curF = newF;
				});
				
				processXML(1);
				var mailcheck = window.setInterval("checkMailbox()", 600000);
				
				function checkMailbox() {
					var flcl = $.ajax({
						type: 'POST',
						url: 'mailbox.contactlist.php',
						data: 'u=<?php print $key; ?>',
						success: function(msg) {
							$('#mailbox-friendlist-list').html(msg);
						}
					});
					
					var iov = $('#io').val();
					var flml = $.ajax({
						type: 'POST',
						url: 'mailbox.message.php',
						data: 'action=list&u=<?php print $key; ?>&io='+iov,
						success: function(msg) {
							$('#mailbox-messages').html(msg);
						}
					});
				}

	function eventSpy(e) {
		// load stat content
		var st = $.ajax({
			type: "POST",
			url: "etc.ajax.php",
			data: "e="+e+"&k=<?php print $key; ?>",
			success: function(msg) {
				$("#spy-content").hide();
				$("html, body").animate({scrollTop:90}, "slow");
				$("#spy").animate({
					width: "12px",
					height: "720px",
					left: "489px",
					top: "95px"
				}, 250, function() {
					$("#spy").animate({
						width: "930px",
						left: "15px"
					}, 250, function() {
						$("#spy-content").html(msg).fadeIn(500);
					});
				});
			}
		});
	}
	function eventSignup(e,k,o,s) {
		// load stat content
		var st = $.ajax({
			type: "POST",
			url: "ets.ajax.php",
			data: "e="+e+"&k="+k+"&o="+o+"&s="+s,
			success: function(msg) {
				var r = (0 - (e - s));
				eventSpy(r);
			}
		});
	}
	
	<?php
	if ( $openmail == true ) {
		print "$('#mailbox').toggleClass('icon postbox');";
	}
	?>
	</script>	
	<script type="text/javascript">
  window.___gcfg = {lang: 'en-GB'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
</body>
</html>

<?php

function secureKey($k) {
	return htmlspecialchars(strip_tags($k));
}
