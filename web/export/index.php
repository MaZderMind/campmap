<?php

foreach(['zoom', 'bbox', 'overlays'] as $parameter)
{
	if(!isset($_GET[$parameter]))
	{
		die("Parameter $parameter missing");
	}
}

$allowed_layers = ['landuse', 'ways', 'buildings', 'noc', 'gpscoords', 'amenities'];

$layers = array_intersect($allowed_layers, explode('.', $_GET['overlays']));
$zoom = intval($_GET['zoom']);
$bbox = $_GET['bbox'];
$styledir = dirname(__FILE__).'/../../styles';

do {
	$tmpdir = '/tmp/campmap-'.mt_rand();
}
while(file_exists($tmpdir));

mkdir($tmpdir);

foreach ($layers as $idx => $layer)
{
	system(sprintf(
		'render --bbox=%s --zoom=%s --scale=2 --style=%s --file=%s >/dev/null',
		escapeshellarg($bbox),
		escapeshellarg($zoom+1),
		escapeshellarg($styledir.'/'.$layer.'.xml'),
		escapeshellarg($tmpdir.'/layer-'.$idx.'-'.$layer)
	));
}

system(sprintf(
	'gm convert -compose Atop %s -flatten %s',
	escapeshellarg($tmpdir.'/layer-*.png'),
	escapeshellarg($tmpdir.'/composition.png')
));

header('Content-Type: image/png');
readfile($tmpdir.'/composition.png');

foreach (glob($tmpdir.'/*.png') as $file)
{
	unlink($file);
}
rmdir($tmpdir);
