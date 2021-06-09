ALTER TABLE public.coffre_objets
   ADD COLUMN coffre_pos_cod integer;

ALTER TABLE public.coffre_objets
   ADD COLUMN coffre_date_dispo timestamp with time zone NOT NULL DEFAULT now();

ALTER TABLE public.coffre_objets
   ADD COLUMN coffre_relais_poste character varying NOT NULL DEFAULT 'N';


INSERT INTO public.parametres( parm_type, parm_desc, parm_valeur, parm_valeur_texte) VALUES
     ('text', 'Relais de la poste: delai de livraison standard depuis/vers le coffre (chaine ajoutée à la date par strtotime)', null, '1 DAY'),
     ('text', 'Relais de la poste: delai de livraison express depuis/vers le coffre (chaine ajoutée à la date par strtotime)', null, '4 HOURS'),
     ('integer', 'Relais de la poste: facteur des frais port en livraison express depuis/vers le coffre', 5, null);

