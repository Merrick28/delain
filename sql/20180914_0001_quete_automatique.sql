INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('#START', 'Déclenchement sur perso', 'Cette étape ne doit être utilisée qu''en tant que 1ère étape d''une quête. Elle permet de déterminer quel personnage va déclencher la quête.',
            '[1:perso|1%0],[2:choix|2%2]',
            'Liste des persos (pnj) qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter (saisir 0 comme n° d''étape) la quête, soit la refuser (saisir -1).',
            'Vous abordez [1], après quelques échanges de courtoisie, il vous propose: [2]');

INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('#START', 'Déclenchement sur lieu', 'Cette étape ne doit être utilisée qu''en tant que 1ère étape d''une quête. Elle permet de déterminer quels lieux vont déclencher la quête.',
            '[1:lieu|1%0],[2:choix|2%2]',
            'Liste des lieux qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter (saisir 0 comme n° d''étape) la quête, soit la refuser (saisir -1).',
            'Cet établissement recherche les services d''un aventurier pour une mission, l''acceptez vous? [2]');

INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('#START', 'Déclenchement sur type de lieu', 'Cette étape ne doit être utilisée qu''en tant que 1ère étape d''une quête. Elle permet de déterminer quels type de lieux vont déclencher la quête.',
            '[1:lieu_type|1%0],[2:choix|2%2]',
            'Liste des lieux qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter (saisir 0 comme n° d''étape) la quête, soit la refuser (saisir -1).',
            'Cet établissement recherche les services d''un aventurier pour une mission, l''acceptez vous? [2]');

INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('#MOVE_PERSO', 'Déplacement vers perso', 'Dans cette étape on demande à l''aventurier d''aller voir un PNJ.',
           '[1:perso|1%0]',
           'C''est le PNJ à que l''aventurier doit aller voir',
           'Allez donner de mes nouvelles à mon ami [1].');

INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('#MOVE_LIEU', 'Déplacement vers lieu', 'Dans cette étape on demande à l''aventurier d''aller voir un lieu.',
           '[1:lieu|1%0]',
           'C''est le lieu à que l''aventurier doit aller voir',
           'Rendez-vous à [1].');

INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('#TEXTE', 'Texte', 'Cette étape est automatiquement validée, elle sert à jouter du contenu texte dans le déroulement de la quête. (il n''y a pas de paramètre)',
           NULL,
           NULL,
           NULL);


INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('#CHOIX', 'Choix', 'Dans cette étape on demande à l''aventurier de faire un choix, l''étape suivante dépend alors du choix.',
           '[1:choix|0%0]',
           'Ce sont les choix fait à l''aventurier, il faut assossier chaque choix à une étape pour pousuivre la quête.',
           'Faite votre choix?');