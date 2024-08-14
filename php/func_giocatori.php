<?php

function lista_stat_giocatore($chiamate) {
	$out = '<i class="bi bi-play-fill"></i>' . $chiamate[7];
	$out .= '&nbsp;&nbsp;<i class="bi bi-hand-thumbs-up"></i>' . $chiamate[0];
	$out .= '&nbsp;&nbsp;<i class="bi bi-hand-thumbs-down"></i>' . $chiamate[1];
	$out .= ($chiamate[2] > 0 ? '&nbsp;&nbsp;<i class="bi bi-arrows-collapse"></i>' . $chiamate[2] : '');
	$out .= ($chiamate[3] > 0 ? '&nbsp;&nbsp;<i class="bi bi-person-bounding-box"></i>' . $chiamate[3] : '');
	$out .= '&nbsp;&nbsp;<i class="bi bi-incognito"></i>' . $chiamate[4];
	$out .= ($chiamate[5] > 0 ? '&nbsp;&nbsp;<i class="bi bi-star-fill"></i>' . $chiamate[5] : '');
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