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

function medagliere_giocatore($medaglie, $limit = 5) {
	$out = '';
	foreach ($medaglie as $i => $m) {
		if ($m > 0 && $i < $limit) {
			$out .= '<img src="media/img/Medaglia' . ($i+1) . '.png" height=25px>' . $m . '&nbsp;&nbsp;';
		}
	}
	return $out;
}

function specchietto_chiamate($chiamate) {
	$out = '<div class="text-center mb-3"><h6>Chiamate</h6>';
	$out .= '<strong class="text-success"><i class="bi bi-hand-thumbs-up"></i>&nbsp;' . $chiamate[0] . ' </strong>';
	
	$sum = $chiamate[0] + $chiamate[1] + $chiamate[2];
	$out .= ($sum > 0 ? '<small>(' . floor(($chiamate[0] / $sum) * 100) . '%)</small>' : '');
	$out .= '&nbsp;&nbsp;';
	$out .= '<strong class="text-danger"><i class="bi bi-hand-thumbs-down"></i>&nbsp;' . $chiamate[1] . ' </strong>&nbsp;&nbsp;';
	if ($chiamate[2] > 0) {
		$out .= '<strong class="text-warning"><i class="bi bi-arrows-collapse"></i>&nbsp;' . $chiamate[2] . '</strong>';
	}
	$out .= '</div>';

	return $out;
}

function specchietto_chiamate_inmano($chiamate, $link_dettaglio = false) {
	$out = '<div class="text-center mb-3"><h6><i class="bi bi-person-bounding-box"></i> Chiamate in mano</h6>';
	$out .= '<strong class="text-success"' . ($link_dettaglio ? ' onclick="mostra_dettagliopartite(0);"' : '') . '><i class="bi bi-hand-thumbs-up"></i>&nbsp;' . $chiamate[0] . ' </strong>&nbsp;&nbsp;';
	$out .= '<strong class="text-danger"' . ($link_dettaglio ? ' onclick="mostra_dettagliopartite(1);"' : '') . '><i class="bi bi-hand-thumbs-down"></i>&nbsp;' . $chiamate[1] . ' </strong>&nbsp;&nbsp;';
	if ($chiamate[2] > 0) {
		$out .= '<strong class="text-warning"' . ($link_dettaglio ? ' onclick="mostra_dettagliopartite(2);"' : '') . '><i class="bi bi-arrows-collapse"></i>&nbsp;' . $chiamate[2] . '</strong>';
	}
	$out .= '</div>';

	return $out;
}

function specchietto_alleanze($chiamate) {
	$out = '<div class="text-center mb-3"><h6><i class="bi bi-incognito"></i> Alleanze</h6>';
	$out .= '<strong class="text-success"><i class="bi bi-hand-thumbs-up"></i>&nbsp;' . $chiamate[0] . ' </strong>';
	
	$sum = $chiamate[0] + $chiamate[1] + $chiamate[2];
	$out .= ($sum > 0 ? '<small>(' . floor(($chiamate[0] / $sum) * 100) . '%)</small>' : '');
	$out .= '&nbsp;&nbsp;';
	$out .= '<strong class="text-danger"><i class="bi bi-hand-thumbs-down"></i>&nbsp;' . $chiamate[1] . ' </strong>&nbsp;&nbsp;';
	if ($chiamate[2] > 0) {
		$out .= '<strong class="text-warning"><i class="bi bi-arrows-collapse"></i>&nbsp;' . $chiamate[2] . '</strong>';
	}
	$out .= '</div>';

	return $out;
}

function specchietto_cappotti($chiamate, $link_dettaglio = false) {
	$out = '<div class="text-center mb-3"><h6><i class="bi bi-star-fill"></i> Cappotti</h6>';
	$out .= '<strong class="text-success"' . ($link_dettaglio ? ' onclick="mostra_dettagliopartite(3);"' : '') . '><i class="bi bi-hand-thumbs-up"></i>&nbsp;' . $chiamate[0] . ' </strong>&nbsp;&nbsp;';
	$out .= '<strong class="text-danger"' . ($link_dettaglio ? ' onclick="mostra_dettagliopartite(4);"' : '') . '><i class="bi bi-hand-thumbs-down"></i>&nbsp;' . $chiamate[1] . '</strong>';
	$out .= '</div>';
	
	return $out;
}