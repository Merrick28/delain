#!/bin/ksh
repertoire=/home/sdewitte/shell
logdir=/home/sdewitte/logs
tmp_sortie=$repertoire/tmp_resultat
sortie=$repertoire/resultat
fichier=$repertoire/liste_monstre.txt
/usr/local/pgsql/bin/psql -q -t << EOF >> /home/sdewitte/logs/iae.log 2>&1
insert into news (news_titre,news_texte,news_date,news_auteur,news_mail_auteur) values
('Enfin !','...krrrrrrrr... Kchhhhhhhhh... Message du Comité de Défense des Monstres Gentils, des Rêves Enfantins et autres Bisounours (le CDMGREAB).<br><br>A dater de ce jour, nous, les monstres des souterrains, déclarons qu\'il suffit !<br><br>Nous en avons assez d\'être les instruments de Malkiar qui nous crée et nous oblige à combattre afin de ralentir la progression des aventurières et aventuriers.<br>Nous en avons assez d\'être la chair à canon de ces aventurières et aventuriers qui nous déciment par centaine sans chercher à nous comprendre et nous aimer.<br><br>Pourtant tous ont oublié qu\'ils nous ont aimé quand ils étaient jeunes ; tous ont oublié que nous les faisions rire après l\'école par l\'intermédiaire du petit écran ; tous ont oublié qu\'ils se sont endormis en nous serrant dans leurs bras.<br><br>Aussi nous faisons dorénavant sécession car nous trouvons que cela manque d\'Amour dans ces souterrains !!! Nous décidons de créer une zone pleine de bonheur, de sucrerie et de rires (zé des chants) qui sera notre propre havre de paix et nous l\'investissons de suite : il s\'agit de l\'Île aux Enfants !!!<br>Si vous décidez de rentrer dans ce lieu, vous devrez en suivre <u>NOS</u> lois et non celle de Malkiar ni la votre !<br><br><i>Les passages mystérieux luisent d\'une clarté bizarre. Vous comprenez qu\'ils sont maintenant ouverts.</i>',
now(),'Casimir, porte-parole des monstres gentils.','casimir@jdr-delain.net');
update lieu set lieu_url = 'passage_escalier.php' where lieu_nom = 'Passage mystérieux';
EOF
