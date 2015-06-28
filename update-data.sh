#!/bin/sh
cd /home/pko/campmap
BBOX=13.2776,53.0222,13.345,53.0442

wget "http://maps.c3voc.de/api/0.6/map?bbox=$BBOX" -O camp.osm
/home/pko/osm2pgsql/osm2pgsql -d campmap --hstore-all -c -S camp.style --slim camp.osm

tirex-batch map=campmap-buildings,campmap-datenklo_area,campmap-noc,campmap-railway,campmap-tents bbox=$BBOX z=16-20

