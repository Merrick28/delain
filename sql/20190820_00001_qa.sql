INSERT INTO public.type_objet
  (tobj_libelle, tobj_identifie_auto, tobj_equipable, tobj_affichage_char, tobj_degradation)
VALUES
  ('Grisbi',  1, 0, '$', 0);

INSERT INTO quetes.aquete_etape_modele(
          aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
          aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
  VALUES ('#ECHANGE #OBJET', 'Objets - Echange d''objet.',
         'Une liste d’objets proposés à l''échange, contre d''autres objets du joueur.',
         '[1:delai|1%1],[2:valeur|1%1],[3:echange|0%0]',
         'Délai alloué pour cette étape.|C''est le nombre d''echange maximum autorisé (0 pour illimité),|Liste des transactions: les objets à acquerir pour le joueur contre les objets qui servent de monnaie d''échange.',
         'Vous voyez que sur l''état du PNJ, plusieurs objets sont disponibles à l''échange:');