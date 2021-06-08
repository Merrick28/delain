ALTER TABLE public.coffre_objets
   ADD COLUMN coffre_pos_cod integer;

ALTER TABLE public.coffre_objets
   ADD COLUMN coffre_date_dispo timestamp with time zone NOT NULL DEFAULT now();

ALTER TABLE public.coffre_objets
   ADD COLUMN coffre_relais_poste character varying NOT NULL DEFAULT 'N';
