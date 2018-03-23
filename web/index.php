<?php setlocale(LC_ALL, 'fr_FR.UTF-8');
require __DIR__ . '/../vendor/autoload.php';

$cache = new Gilbitron\Util\SimpleCache();
$cache->cache_path = '../';
if (!empty($_GET) && $_GET['cache'] == 'no') {
	$cache->cache_time = 0;
} else {
	$cache->cache_time = 259200;
}

$api_wp = $cache->get_data('wp', 'https://api.kent1.xyz/wp-json/wp/v2/pages/21');
$json = json_decode($api_wp);

$naissance = strftime('%e %B %Y', strtotime($json->acf->naissance));
$age = (int)date('Ymd') - (int)$json->acf->naissance;
$age = substr((string)$age, 0 , 2);
$maj = date('d/m/y', strtotime($json->modified));
$site = parse_url($json->acf->site)['host'];

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

$mpdf->SetTitle('CV ' . $json->acf->nom);
$mpdf->SetAuthor($json->acf->nom);
$mpdf->SetSubject($json->acf->slogan);
$mpdf->SetDisplayMode('fullpage');

$mpdf->WriteHTML(file_get_contents('mpdf.css'), 1);
$mpdf->WriteHTML($pdf);

$mpdf->Output('Quentin-Burgniard-CV.pdf', 'I');
