#!/bin/bash
ETAGE=-1

STORAGE=/home/delain/evol-1
FILE=/tmp/map-full.js
IMGFILE=/tmp/map.png
TMPFILE=/tmp/map
CMDFILE=/tmp/map.sh
echo "" > $FILE
DEPART=0
for row in `/usr/bin/psql -t -d delain -U delainadm << EOF
select distinct pos_y from positions where pos_etage = $ETAGE order by pos_y desc
EOF`
do
	/usr/bin/psql -t -d delain -U delainadm << EOF > $TMPFILE
select detail_carte_monstre($ETAGE,$row,$DEPART)
EOF
	DEPART=`tail -n2 $TMPFILE | cut -d'#' -f2`
	grep '^ carte' $TMPFILE | sed 's/ //g' >> $FILE
done
# "Conversion des informations"

# carte['1470']=[2117,-13,-13,13,1,4,0,0,1,8,2,2,0,0,' '];\r
convert -size 43x43 xc:white $IMGFILE
echo "" > $TMPFILE
echo "convert $IMGFILE \\" > $CMDFILE
for point in `cat $FILE`
do
    #x=`echo $point | cut -d, -f2`
    #y=`echo $point | cut -d, -f3`
    #p=`echo $point | cut -d, -f4`0
    #m=`echo $point | cut -d, -f5`0
    #w=`echo $point | cut -d, -f10`
    set `echo -e $point | sed 's/,/ /g'`
    x=$2
    y=$3
    p=$40
    m=$50
    shift
    w=$9

    if [ $p -gt 120 ]
    then
        p=120
    fi
    if [ $m -gt 120 ]
    then
        m=120
    fi
    # Convert p,m,w to a color
    if [ $w -ge 900 ] ;
    then
        # Mur
        stroke="black"
    elif [ $m$p -eq "0000" ] ;
    then
        stroke="white"
        continue
    else
        stroke="rgb($((256-p)),$((256-m-p)),$((256-m)))"
    fi
    #convert $IMGFILE -fill "$stroke" -draw "point $((x+21)),$((21-y))" $IMGFILE
    echo " -fill '$stroke' -draw 'point $((x+21)),$((21-y))' \\" >> $CMDFILE
done
echo "$IMGFILE" >> $CMDFILE

source $CMDFILE
mv $IMGFILE $STORAGE/"`date +%Y-%m-%d.%H:%M:%S`".png

