BBOX="13.303317 53.029361 13.313832 53.033845"

rm layer-*.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/landuse.xml layer-0-landuse.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/ways.xml layer-1-ways.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/buildings.xml layer-2-buildings.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/tents.xml layer-3-tents.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/noc.xml layer-4-noc.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/gpscoords.xml layer-5-gpscoords.png

gm convert -compose Atop layer-*.png -flatten X-composition.png
