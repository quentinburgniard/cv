<?php ini_set('pcre.backtrack_limit', '1000000000');
ini_set('post_max_size', '0');
setlocale(LC_ALL, 'fr_FR.UTF-8');
require __DIR__ . '/../vendor/autoload.php';

$fields = $_POST['fields'];
$fields = json_decode($fields);
if (!empty($fields->datedenaissance)) {
	$naissance = strftime('%e %b %Y', strtotime($fields->datedenaissance));
}
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

$language = $fields->langue;
include '../translations.php';
include '../pdf.php';

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

$mpdf->SetTitle('CV ' . $fields->prenom . ' ' . $fields->nom);
$mpdf->SetAuthor($fields->prenom . ' ' . $fields->nom);
$mpdf->SetSubject($fields->intituleduposte);
$mpdf->SetDisplayMode('fullpage');

$mpdf->WriteHTML(file_get_contents('mpdf.css'), \Mpdf\HTMLParserMode::HEADER_CSS);
$theme = '#487d96';
switch ($fields->theme) {
	case 'bleu':
		$theme = '#487d96';
		break;
	case 'rouge':
		$theme = '#df7fa0';
		break;
	case 'bleuclair':
		$theme = '#71a5de';
		break;
	case 'vert':
		$theme = '#55d6c2';
		break;
	case 'orange':
		$theme = '#ffa48f';
		break;
}
$mpdf->WriteHTML('.header { background-color: ' . $theme . ' }', \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($pdf);

$mpdf->Output($fields->prenom . '-' . $fields->nom . '-CV.pdf', 'I');