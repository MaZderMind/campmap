BBOX="13.303317 53.029361 13.313832 53.033845"

nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/noc.xml noc.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/tents.xml tents.png
nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/buildings.xml buildings.png
#nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/ways.xml ways.png
#nik2img.py --no-open --dimensions 1920 1080 --bbox $BBOX styles/landuse.xml landuse.png
