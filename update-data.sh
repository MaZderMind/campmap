#!/bin/sh
cd /home/pko/campmap

wget 'http://maps.c3voc.de/api/0.6/map?bbox=13.2776,53.0222,13.345,53.0442' -O camp.osm
/home/pko/osm2pgsql/osm2pgsql -d campmap --hstore-all -c -S camp.style --slim camp.osm
