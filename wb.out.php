<?php

foreach ($notes AS $note) {
	print '<div class="fb_post'.($note['id'] == 83000 ? ' admin' : '').($note['id'] == 3 ? ' dv_admin' : '').'">';
	if ( !is_null($note['name']) && trim($note['name']) != '' ) {
		print '<p class="fb_name">' . $note['name'] . ($note['oldnames'] != '' ? ' <span style="font-size:.825em;">(formerly known as '.substr($note['oldnames'],2).')</span>' : '') . '</p>';
	}
	print '<p class="fb_time">' . date('m/d/Y, h:i a',$note['time']) . '</p>';
	print '<p class="fb_text">' . $note['feedback'] . (strpos($note['feedback'],'cockstail') ? '<img style="" src="img/cockstail.gif" />' : '') . '</p>';
	print '</div>';
}