<?php $mpdf->WriteHTML('<style>
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
</style>');
$mpdf->WriteHTML('<div class="header"><table>
<tr><td><h1>' . $fields->prenom . ' ' . $fields->nom . '</h1><p>' . (empty($fields->intituleduposte) ? '' : $fields->intituleduposte) . '<br>
<span class="fa fa-location-arrow" aria-hidden="true" style="font-size: 12pt">&#xf124</span> ' . $fields->adresse . '<br>
Date de naissance : ' . $naissance . ' - ' . $age . ' ans</p></td>
<td><a href="tel:' . (empty($fields->telephone) ? '' : $fields->telephone) . '" style="text-decoration: none">' . $fields->telephone . '</a><br>' . $fields->email . '<br>');
if (!empty($fields->siteinternet)) {
	$mpdf->WriteHTML('<a href="' . $fields->siteinternet . '">' . $site . '</a><br><br>');
}
if (!empty($json->acf->linkedin)) {
	$mpdf->WriteHTML('<a href="' . $json->acf->linkedin . '" style="text-decoration: none;">
<span class="fa fa-linkedin" aria-hidden="true">&#xf0e1</span>
</a>');
}
if (!empty($json->acf->github)) {
	$mpdf->WriteHTML('<a href="' . $json->acf->github  . '" style="text-decoration: none;">
<span class="fa fa-github" aria-hidden="true">&#xf09b</span>
</a>');
}
if (!empty($json->acf->medium)) {
	$mpdf->WriteHTML('<a href="' . $json->acf->medium  . '" style="text-decoration: none;">
<span class="fa fa-medium" aria-hidden="true">&#xf23a</span>
</a>');
}
$mpdf->WriteHTML('</td></tr></table></div>
<div class="objet">' . $fields->phrasedaccroche . '</div>
<div class="left">
<div class="titre">Expériences</div><div class="contenu contenu-left">');

if (!empty($fields->experiences)) {
	foreach ($fields->experiences as $item) {
		if (!empty($item->date) && !empty($item->intitule)) {
			$mpdf->WriteHTML('<p class="annees">' . $item->date . '</p>' . $item->intitule);

			if (!empty($item->site)) {
				$mpdf->WriteHTML(' <a class="icon" href="' . $item->site . '">
			<span class="fa fa-link">&#xf0c1;</span></a>');
			}

			$mpdf->WriteHTML('<div class="description">' . $item->description . '</div>');
		}
	}
}

$mpdf->WriteHTML('</div><div class="titre">Formations</div><div class="contenu contenu-left">');

if (!empty($fields->formations)) {
	foreach ($fields->formations as $item) {
		if (!empty($item->date) && !empty($item->intitule)) {
			$mpdf->WriteHTML('<p class="annees">' . $item->date . '</p>' . $item->intitule);

			if (!empty($item->site)) {
				$mpdf->WriteHTML(' <a class="icon" href="' . $item->site . '">
			<span class="fa fa-link">&#xf0c1;</span></a>');
			}

			$mpdf->WriteHTML('<div class="description">' . $item->description . '</div>');
		}
	}
}

$mpdf->WriteHTML('</div><div class="titre">Divers</div><div class="contenu contenu-left">');

if (!empty($fields->divers)) {
	foreach ($fields->divers as $item) {
		if (!empty($item->date) && !empty($item->intitule)) {
			$mpdf->WriteHTML('<p class="annees">' . $item->date . '</p>' . $item->intitule);

			if (!empty($item->site)) {
				$mpdf->WriteHTML(' <a class="icon" href="' . $item->site . '">
			<span class="fa fa-link">&#xf0c1;</span></a>');
			}

			$mpdf->WriteHTML('<div class="description">' . $item->description . '</div>');
		}
	}
}

$mpdf->WriteHTML('</div></div>
<div class="right">
<img src="' . $fields->image .'" class="profil">
<div class="titre">Compétences</div><div class="contenu contenu-right">');

if (!empty($fields->competences)) {
	foreach ($fields->competences as $item) {
		if (!empty($item->intitule)) {
			$mpdf->WriteHTML('<p>' . $item->intitule . '</p>' .
			'<div class="description">' . $item->description . '</div>');
		}
	}
}

$mpdf->WriteHTML('</div><div class="titre">Intérêts</div><div class="contenu contenu-right">');

if (!empty($fields->interets)) {
	foreach ($fields->interets as $item) {
		if (!empty($item->intitule)) {
			$mpdf->WriteHTML('<p>' . $item->intitule . '</p>' .
			'<div class="description">' . $item->description . '</div>');
		}
	}
}

$mpdf->WriteHTML('</div></div>');

if (!empty($fields->pieddepage)) $mpdf->WriteHTML("<div class='footer'>" . $fields->pieddepage ."</div>");