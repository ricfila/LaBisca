<?php

function lista_stat_giocatore($chiamate) {
	$out = '<i class="bi bi-play-fill"></i>' . $chiamate[12];
	$out .= '&nbsp;&nbsp;<i class="bi bi-hand-thumbs-up"></i>' . $chiamate[0];
	$out .= '&nbsp;&nbsp;<i class="bi bi-hand-thumbs-down"></i>' . $chiamate[1];
	$out .= ($chiamate[2] > 0 ? '&nbsp;&nbsp;<i class="bi bi-arrows-collapse"></i>' . $chiamate[2] : '');
	$mano = $chiamate[3] + $chiamate[4] + $chiamate[5];
	$out .= ($mano > 0 ? '&nbsp;&nbsp;<i class="bi bi-person-bounding-box"></i>' . $mano : '');
	$alleanze = $chiamate[6] + $chiamate[7] + $chiamate[8];
	$out .= '&nbsp;&nbsp;<i class="bi bi-incognito"></i>' . $alleanze;
	$cappotti = $chiamate[9] + $chiamate[10];
	$out .= ($cappotti > 0 ? '&nbsp;&nbsp;<i class="bi bi-star-fill"></i>' . $cappotti : '');
	return $out;
}

function medagliere_giocatore($medaglie) {
	$out = '';
	foreach ($medaglie as $i => $m) {
		if ($m > 0) {
			$out .= '<img src="media/img/Medaglia' . ($i+1) . '.png" height=25px><!--i class="bi bi-x"></i-->' . $m . '&nbsp;&nbsp;';
		}
	}
	return $out;
}
?>