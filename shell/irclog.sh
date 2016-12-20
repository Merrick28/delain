#!/bin/bash
# Si le log échoue, il faut le récupérer à la main:
# ssh -t delain@dedibox.jdr-delain.net screen -r `screen -ls | fgrep dedibox | awk '{ print $1; }'`
# Alt+n/p pour afficher #sd
# /lastlog -file ~/irclogs/netrusk/#sd.log.old
# Éditer #sd.log.old si besoin
# Changer IRCFILE ci-dessous et lancer le script
# Remettre IRCFILE à sa valeur d'origine

IRCFILE=/home/delain/irclogs/netrusk/\#sd.log
LOGFILE=/home/delain/logs/irc.log
EXCERPTSIZE=10

# On démarre irssi s'il est arrêté.
if ps x | fgrep irssi | fgrep -v fgrep >/dev/null
then
    echo "irssi tourne déjà." >/dev/null
else
    echo "Démarrage de irssi"
    screen -S irc -d -m irssi
fi

if [ $# -ne 0 ]
then
    echo -n "Extraction de logs depuis le fichier "
    LOGFILE+=".1"
    echo $LOGFILE
else
    fgrep -v '!-' $IRCFILE > $LOGFILE
    echo "" > $IRCFILE
fi 

# Poster $LOGFILE sur le forum

SQLFILE=/tmp/post.sql
FORUM=5 # Auberge
TOPIC=15699 # Le topic
USER=3370 # irc
USERNAME='irc'
TITLE='Re: Hier dans le salon de discussion'
DATE=`date +%s`
FILELEN=`grep -c . $LOGFILE`
if [ $FILELEN -gt $EXCERPTSIZE ]
then
START=$RANDOM
let "START %= $FILELEN - $EXCERPTSIZE"
POST_ID=`/usr/bin/psql -t -d forum -U forum -c "select nextval('phpbb_posts_seq')"`

echo "INSERT INTO phpbb_posts (post_id, forum_id, topic_id, poster_id, post_time, post_subject, post_text) VALUES ($POST_ID, $FORUM, $TOPIC, $USER, $DATE, '$TITLE', E'" > $SQLFILE
sed -n $START,$(($START+$EXCERPTSIZE))p $LOGFILE | sed "s/'/''/g" >> $SQLFILE
echo "');" >> $SQLFILE

iconv -f iso-8859-1 -t utf-8 $SQLFILE > $SQLFILE.iso
mv $SQLFILE.iso $SQLFILE
/usr/bin/psql -t -d forum -U forum -f $SQLFILE >/dev/null

/usr/bin/psql -t -d forum -U forum -c "UPDATE phpbb_topics SET topic_last_post_time = $DATE, topic_last_poster_name='$USERNAME', topic_last_poster_id=$USER, topic_last_post_id=$POST_ID, topic_last_post_subject = '$TITLE', topic_replies = topic_replies + 1, topic_replies_real = topic_replies_real + 1 where topic_id = $TOPIC" >/dev/null


/usr/bin/psql -t -d forum -U forum -c "UPDATE phpbb_forums SET forum_last_post_time = $DATE, forum_last_poster_name='$USERNAME', forum_last_poster_id=$USER, forum_last_post_id=$POST_ID, forum_last_post_subject = '$TITLE' where forum_id = $FORUM" >/dev/null
fi

echo "UPDATE phpbb_users set user_sig = E'" > $SQLFILE
/usr/games/fortune fr | sed "s/'/''/g" >> $SQLFILE
echo "' where user_id = 2800;" >> $SQLFILE
iconv -f iso-8859-1 -t utf-8 $SQLFILE > $SQLFILE.iso
mv $SQLFILE.iso $SQLFILE
/usr/bin/psql -t -d forum -U forum -f $SQLFILE >/dev/null
