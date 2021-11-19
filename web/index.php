<?php ini_set('pcre.backtrack_limit', '-1');
setlocale(LC_ALL, 'fr_FR.UTF-8');
require __DIR__ . '/../vendor/autoload.php';

$fields = $_POST['fields'];
$fields = json_decode($fields);
$naissance = strftime('%e %b %Y', strtotime($fields->datedenaissance));
$age = '';
if ($naissance) {
	$age = date_diff(new DateTime($fields->datedenaissance), new DateTime('now'))->format('%y');
}
$maj = date('d/m/y', strtotime($fields->modified));
$site = parse_url($fields->siteinternet)['host'];
/*if ($fields->image) {
	$imageFile = str_replace('data:image/png;base64,', '', $fields->image);
	$imageFile = base64_decode($imageFile);
	file_put_contents(__DIR__ . '/../image.png', $imageFile);
}*/

$mpdf = new \Mpdf\Mpdf([
	'default_font' => 'lato',
	'dpi' => 300,
	'fontdata' => [
		'catamaran' => [
			'R' => 'Catamaran-ExtraLight.ttf'
		],
		'lato' => [
			'R' => 'Lato-Regular.ttf',
			'B' => 'Lato-Bold.ttf',
			'I' => 'Lato-Italic.ttf',
		],
		'fontawesome' => [
			'R' => 'fontawesome-webfont.ttf',
		]
	],
	'fontDir' => __DIR__ . '/../fonts',
	'img_dpi' => 300,
	'progressBar' => true
]);

include '../pdf.php';

$mpdf->SetTitle('CV ' . $fields->prenom . ' ' . $fields->nom);
$mpdf->SetAuthor($fields->prenom . ' ' . $fields->nom);
$mpdf->SetSubject($fields->intituleduposte);
$mpdf->SetDisplayMode('fullpage');

$mpdf->WriteHTML(file_get_contents('mpdf.css'), 1);

$mpdf->Output($fields->prenom . '-' . $fields->nom . '-CV.pdf', 'I');