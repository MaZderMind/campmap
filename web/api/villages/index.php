<?php

$db = new PDO('pgsql:dbname=campmap');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

if(isset($_GET['id']))
{
	$q = $db->prepare("
		SELECT
			ST_X(ST_Transform(ST_PointOnSurface(way), 4326)) AS x,
			ST_Y(ST_Transform(ST_PointOnSurface(way), 4326)) AS y,
			ST_AsGeoJSON(ST_Transform(way, 4326)) AS geojson
		FROM
			planet_osm_polygon
		WHERE
			tags->'landuse' = 'residential' AND
			exist(tags, 'name') AND
			osm_id = ?
	");
	$q->execute([
		intval($_GET['id'])
	]);
	$row = $q->fetch();

	header(sprintf(
		'Location: http://campmap.mazdermind.de/#zoom=19&lat=%s&lon=%s&drawnItems=%s',
		rawurlencode( $row['y'] ),
		rawurlencode( $row['x'] ),
		rawurlencode( $row['geojson'] )
	));
	exit;
}

if(isset($_GET['name']))
{
	$q = $db->prepare("
		SELECT
			ST_X(ST_Transform(ST_PointOnSurface(way), 4326)) AS x,
			ST_Y(ST_Transform(ST_PointOnSurface(way), 4326)) AS y,
			ST_AsGeoJSON(ST_Transform(way, 4326)) AS geojson
		FROM
			planet_osm_polygon
		WHERE
			tags->'landuse' = 'residential' AND
			regexp_split_to_array(tags->'name', ';') @> ARRAY[?]
	");
	$q->execute([
		$_GET['name']
	]);
	$row = $q->fetch();

	if(!$row)
	{
		die(sprintf('No Village named %s in the Camp-Map-Database, Sorry.', $_GET['name']));
	}

	header(sprintf(
		'Location: http://campmap.mazdermind.de/#zoom=19&lat=%s&lon=%s&drawnItems=%s',
		rawurlencode( $row['y'] ),
		rawurlencode( $row['x'] ),
		rawurlencode( $row['geojson'] )
	));
	exit;
}

$q = $db->query("
	SELECT
		osm_id,
		tags->'name' AS name,
		tags->'website' AS website,
		tags->'website2' AS website2,
		tags->'way_area' AS area,
		ST_X(ST_Transform(ST_PointOnSurface(way), 4326)) AS x,
		ST_Y(ST_Transform(ST_PointOnSurface(way), 4326)) AS y,
		ST_AsGeoJSON(ST_Transform(way, 4326)) AS geojson
	FROM
		planet_osm_polygon
	WHERE
		tags->'landuse' = 'residential' AND
		exist(tags, 'name')
");

$results = [];
foreach ($q as $row)
{
	$result = [];

	$result['names'] = explode(';', $row['name']);
	$result['websites'] = explode(';', $row['website']);
	$result['area'] = floatval($row['area']);
	$result['x'] = floatval($row['x']);
	$result['y'] = floatval($row['y']);
	$result['geojson'] = json_decode($row['geojson']);

	if($row['website2'])
	{
		$website2 = explode(';', $row['website2']);
		$result['websites'] = array_merge($results['websites'], $website2);
	}

	$result['maplink'] = sprintf(
		'http://campmap.mazdermind.de/api/villages/?id=%u',
		rawurlencode( $row['osm_id'] )
	);

	$results[] = $result;
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode(
	$results,
	JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);
