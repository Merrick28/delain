psql -t -c "select perso_avatar from perso where perso_actif = 'O' and perso_avatar is not null" sdewitte |\
while read avatar
do
	echo avatars/$avatar
	touch avatars/$avatar
done
#suppression des autres
find ./avatars -mtime +1|xargs rm
# fin
