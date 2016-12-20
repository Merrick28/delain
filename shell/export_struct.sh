#/bin/sh
echo "Sauvegarde de la base en cours" >> /home/delain/public_html/stop_jeu
/usr/bin/pg_dump -U delainadm -s delain > /home/delain/svn/delainsvn/schema_base.sql
cd /home/delain/svn/delainsvn
svn commit -m "Export quotidien de la structure de la base"
rm -f /home/delain/public_html/stop_jeu

