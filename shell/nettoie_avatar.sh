#!/usr/bin/sh
cd /home/sdewitte/public_html/avatars
psql -t -c"select distinct perso_avatar from perso where perso_avatar is not null order by perso_avatar" sdewitte | while read avatar
do
	echo $avatar
	touch $avatar
	echo 'Fait'
done

#suppression des autres
find . -mtime +5|xargs rm

