INSERT INTO public.type_objet
  (tobj_libelle, tobj_identifie_auto, tobj_equipable, tobj_affichage_char, tobj_degradation)
VALUES
  ('Grisbi',  1, 0, '$', 0);

INSERT INTO quetes.aquete_etape_modele(
          aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
          aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
  VALUES ('#ECHANGE #OBJET', 'Objets - Echange d''objet.',
         'Une liste d’objets proposés à l''échange par un PNJ, contre d''autres objets et ou Bzf du joueur.',
         '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:echange|0%0]',
         'Délai alloué pour cette étape.|Le joueur doit se trouver sur la case de ce PNJ pour faire l'échange.|C'est le nombre d'echange maximum autorisé (0 pour illimité),|Liste des transactions: les objets à acquerir pour le joueur contre les objets qui servent de monnaie d'échange.',
         'Vous voyez que sur l''étal du PNJ, plusieurs objets sont disponibles à l''échange:');


