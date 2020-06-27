<?php $pdf = '<style>
.contenu {
	padding: 0 2mm 10mm 2mm;
}
.objet {
	padding: 10mm 20mm 10mm 20mm;
}
.left {
	width: 120mm;
}
.right, .profil {
	width: 60mm;
}
.profil {
	padding: 0 0 10mm 0;
}
</style>';
$pdf .= '<div class="header"><table>
<tr><td><h1>' . $fields->prenom . ' ' . $fields->nom . '</h1><p>' . $fields->intituleduposte . '<br>
<span class="fa fa-location-arrow" aria-hidden="true" style="font-size: 12pt">&#xf124</span> ' . $fields->adresse . '<br>
Date de naissance : ' . $naissance . ' - ' . $age . ' ans</p></td>
<td><a href="tel:' . $fields->numerodetelephone . '" style="text-decoration: none">' . $fields->numerodetelephone . '</a><br>' . $fields->email . '<br>';
if (!empty($fields->siteinternet)) $pdf .= '<a href="' . $fields->siteinternet . '">' . $site . '</a><br><br>';
if (!empty($json->acf->linkedin)) $pdf .= '<a href="' . $json->acf->linkedin . '" style="text-decoration: none;">
<span class="fa fa-linkedin" aria-hidden="true">&#xf0e1</span>
</a>';
if (!empty($json->acf->github)) $pdf .= '<a href="' . $json->acf->github  . '" style="text-decoration: none;">
<span class="fa fa-github" aria-hidden="true">&#xf09b</span>
</a>';
if (!empty($json->acf->medium)) $pdf .= '<a href="' . $json->acf->medium  . '" style="text-decoration: none;">
<span class="fa fa-medium" aria-hidden="true">&#xf23a</span>
</a>';
$pdf .= '</td></tr></table></div>
<div class="objet">' . $json->acf->objet . '</div>
<div class="left">
<div class="titre">Expériences</div><div class="contenu contenu-left">';

if (!empty($json->acf->experiences)) {
	foreach ($json->acf->experiences as $item) {
		$pdf .= '<p class="annees">' . $item->annees . '</p>' . $item->intitule;

		if (!empty($item->site)) $pdf .= ' <a class="icon" href="' . $item->site . '">
		<span class="fa fa-link">&#xf0c1;</span></a>';

		$pdf .= '<div class="description">' . $item->description . '</div>';
	}
}

$pdf .= '</div><div class="titre">Formations</div><div class="contenu contenu-left">';

if (!empty($json->acf->formations)) {
	foreach ($json->acf->formations as $item) {
		$pdf .= '<p class="annees">' . $item->annees . '</p>' . $item->intitule;

		if (!empty($item->site)) $pdf .= ' <a class="icon" href="' . $item->site . '">
		<span class="fa fa-link">&#xf0c1;</span></a>';

		$pdf .= '<div class="description">' . $item->description . '</div>';
	}
}

$pdf .= '</div><div class="titre">Divers</div><div class="contenu contenu-left">';

if (!empty($json->acf->divers)) {
	foreach ($json->acf->divers as $item) {
		$pdf .= '<p class="annees">' . $item->annees . '</p>' . $item->intitule .
		'<div class="description">' . $item->description . '</div>';
	}
}

$pdf .= '</div></div>
<div class="right">
<img src="' . $fields->image .'" class="profil">
<div class="titre">Compétences</div><div class="contenu contenu-right">';

if (!empty($json->acf->competences)) {
	foreach ($json->acf->competences as $item) {
		$pdf .= '<p>' . $item->intitule . '</p>' .
		'<div class="description">' . $item->description . '</div>';
	}
}

$pdf .= '</div><div class="titre">Intérêts</div><div class="contenu contenu-right">';

if (!empty($json->acf->interets)) {
	foreach ($json->acf->interets as $item) {
		$pdf .= '<p>' . $item->intitule . '</p>' .
		'<div class="description">' . $item->description . '</div>';
	}
}

$pdf .= '</div></div>';

if (!empty($fields->pieddepage)) $pdf .= "<div class='footer'>" . $fields->pieddepage ."</div>";