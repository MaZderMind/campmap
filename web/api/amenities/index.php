<?php

$db = new PDO('pgsql:dbname=campmap');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
/*
		ST_X(ST_Transform(ST_PointOnSurface(way), 4326)) AS x,
		ST_Y(ST_Transform(ST_PointOnSurface(way), 4326)) AS y,

		ST_X(ST_Transform(way, 4326)) AS x,
		ST_Y(ST_Transform(way, 4326)) AS y,

		ST_GeometryType(way) AS type,
*/
$q = $db->query("
	SELECT
		ST_AsGeoJSON(ST_Transform(way, 4326)) AS geojson,

		tags->'name' AS name,
		tags->'amenity' AS amenity,
		tags->'building' AS building,
		tags->'power' AS power,

		tags->'rating' AS rating,
		tags->'phone' AS phone,
		tags->'website' AS website
	FROM
		planet_osm_polygon
	WHERE
		tags->'amenity' IN ('hospital', 'toilets', 'shower', 'restaurant', 'bar', 'recycling') OR
		tags->'building' IN ('datenklo') OR
		(tags->'building' IN ('yes', 'tent') AND (exist(tags, 'phone') OR exist(tags, 'website'))) OR
		tags->'power' IN ('cable_distribution_cabinet') OR
		(tags->'amenity' = 'place_of_worship' AND tags->'place_of_worship' = 'himmel')


	UNION ALL

	SELECT
		ST_AsGeoJSON(ST_Transform(way, 4326)) AS geojson,

		tags->'name' AS name,
		tags->'amenity' AS amenity,
		tags->'building' AS building,
		tags->'power' AS power,

		tags->'rating' AS rating,
		tags->'phone' AS phone,
		tags->'website' AS website
	FROM
		planet_osm_point
	WHERE
		tags->'amenity' IN ('hospital', 'toilets', 'shower', 'restaurant', 'bar', 'recycling') OR
		tags->'building' IN ('datenklo') OR
		(tags->'building' IN ('yes', 'tent') AND (exist(tags, 'phone') OR exist(tags, 'website'))) OR
		tags->'power' IN ('cable_distribution_cabinet') OR
		(tags->'amenity' = 'place_of_worship' AND tags->'place_of_worship' = 'himmel')
");

$features = [];
foreach ($q as $row)
{
	$properties = (array)$row;
	unset($properties['geojson']);

	if($properties['power'] == 'cable_distribution_cabinet' && !$properties['website'])
	{
		$properties['website'] = 'https://events.ccc.de/camp/2015/wiki/Power_DistributionBoxes#'.$properties['name'].'_.28'.$properties['rating'].'_CEE.29';
	}

	$features[] = [
		'type' => 'Feature',
		'geometry' => json_decode($row['geojson']),
		'properties' => $properties,
	];
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode(
	[
		'type' => 'FeatureCollection',
		'features' => $features,
	],
	JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);
