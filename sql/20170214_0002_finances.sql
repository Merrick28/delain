CREATE TABLE public.finances
(
  fin_cod serial NOT NULL,
  fin_date date,
  fin_desc character varying(255),
  fin_montant numeric,
  CONSTRAINT pk_finances PRIMARY KEY (fin_cod)
)
WITH (
OIDS=FALSE
);
ALTER TABLE public.finances
  OWNER TO webdelain;

-- Index: public.finances_fin_date_idx

-- DROP INDEX public.finances_fin_date_idx;

CREATE INDEX finances_fin_date_idx
  ON public.finances
  USING btree
  (fin_date);

-- Index: public.finances_fin_desc_idx

-- DROP INDEX public.finances_fin_desc_idx;

CREATE INDEX finances_fin_desc_idx
  ON public.finances
  USING btree
  (fin_desc COLLATE pg_catalog."default");