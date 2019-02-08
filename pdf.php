<?php $pdf = '<style>
.contenu {
	padding: 0 2mm 8mm 2mm;
}
.objet {
	padding: 10mm 20mm 8mm 20mm;
}
.left {
	width: 100mm;
}
.right, .profil {
	width: 60mm;
}
.profil {
	padding: 0 0 8mm 0;
}
</style>';
$pdf .= '<div class="header"><table>
<tr><td><h1>' . $json->acf->nom . '</h1><p>' . $json->acf->slogan . '<br>
Date de naissance : ' . $naissance . ' - ' . $age . ' ans</p></td>
<td><img src="' . $json->acf->telephone . '">' . $json->acf->courriel . '<br>
<a href="' . $json->acf->site . '">' . $site . '</a><br><br>
<a href="' . $json->acf->linkedin . '" style="text-decoration: none;">
<span class="fa fa-linkedin-square" aria-hidden="true">&#xf08c</span>
</a>
<a href="' . $json->acf->github . '" style="text-decoration: none;">
<span class="fa fa-github-square" aria-hidden="true">&#xf092</span>
</a>
</td></tr></table></div>
<div class="objet">' . $json->acf->objet . '</div>
<div class="left">
<div class="titre">Expériences</div><div class="contenu contenu-left">';

foreach ($json->acf->experiences as $item) {
	$pdf .= '<p class="annees">' . $item->annees . '</p>' . $item->intitule;

	if (!empty($item->site)) $pdf .= ' <a class="icon" href="' . $item->site . '">
	<span class="fa fa-globe">&#xf0ac;</span></a>';

	$pdf .= '<div class="description">' . $item->description . '</div>';
}

$pdf .= '</div><div class="titre">Formations</div><div class="contenu contenu-left">';

foreach ($json->acf->formations as $item) {
	$pdf .= '<p class="annees">' . $item->annees . '</p>' . $item->intitule;

	if (!empty($item->site)) $pdf .= ' <a class="icon" href="' . $item->site . '">
	<span class="fa fa-globe">&#xf0ac;</span></a>';

	$pdf .= '<div class="description">' . $item->description . '</div>';
}

$pdf .= '</div><div class="titre">Divers</div><div class="contenu contenu-left">';

foreach ($json->acf->divers as $item) {
	$pdf .= '<p class="annees">' . $item->annees . '</p>' . $item->intitule .
	'<div class="description">' . $item->description . '</div>';
}

$pdf .= '</div></div>
<div class="right">
<img src="' . $json->acf->image .'" class="profil">
<div class="titre">Compétences</div><div class="contenu contenu-right">';

foreach ($json->acf->competences as $item) {
	$pdf .= '<p>' . $item->intitule . '</p>' .
	'<div class="description">' . $item->description . '</div>';
}

$pdf .= '</div><div class="titre">Intérêts</div><div class="contenu contenu-right">';

foreach ($json->acf->interets as $item) {
	$pdf .= '<p>' . $item->intitule . '</p>' .
	'<div class="description">' . $item->description . '</div>';
}

$pdf .= '</div></div>';

$pdf .= "<div class='footer'>CV généré dynamiquement
<a href='https://github.com/quentinburgniard/cv' style='text-decoration: none'>
<span class='fa fa-github' aria-hidden='true'>&#xf09b</span>
</a> - Dernière mise à jour : ". $maj . "</div>";
