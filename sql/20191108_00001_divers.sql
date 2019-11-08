ALTER TABLE public.objets_monstre_generique
   ADD COLUMN ogmon_equipe boolean NOT NULL DEFAULT false;
COMMENT ON COLUMN public.objets_monstre_generique.ogmon_equipe
  IS 'Si possible, à la création du monstre tenter de lui équiper cet objet sinon le mettre dans son inventaire';


