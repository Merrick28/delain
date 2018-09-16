INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('START', 'Déclenchement sur perso', 'Cette étape ne doit être utilisée qu''en tant que 1ère étape d''une quête. Elle permet de déterminer quel personnage va déclencher la quête.',
            '[1:perso|1%1],[2:choix|2%2]',
            'Liste des persos (pnj) qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter la quête, soit la refuser.',
            'Vous abordez [1], après quelques échanges de courtoisie, il vous propose: [2]');

INSERT INTO quetes.aquete_etape_template(
            aqetaptemp_tag, aqetaptemp_nom, aqetaptemp_description,
            aqetaptemp_parametres, aqetaptemp_param_desc, aqetaptemp_template)
    VALUES ('START', 'Déclenchement sur lieux', 'Cette étape ne doit être utilisée qu''en tant que 1ère étape d''une quête. Elle permet de déterminer quels lieux vont déclencher la quête.',
            '[1:lieu|1%0],[2:choix|2%2]',
            'Liste des lieux qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter la quête, soit la refuser.',
            'Cet établissement recherche les services d''un aventurier pour une mission, l''acceptez vous? [2]');
