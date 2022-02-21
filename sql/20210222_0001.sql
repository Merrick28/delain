CREATE TABLE perso_nb_sorts_bm
(
  pnbsbm_perso_cod integer NOT NULL,
  pnbsbm_objsortbm_cod integer NOT NULL,
  pnbsbm_nombre integer NOT NULL,
  CONSTRAINT "fk_pnbsbm_perso_cod" FOREIGN KEY (pnbsbm_perso_cod)

      REFERENCES perso (perso_cod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT "fk_pnbsbm_objsortbm_cod" FOREIGN KEY (pnbsbm_objsortbm_cod)
      REFERENCES objets_sorts_bm (objsortbm_cod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
ALTER TABLE perso_nb_sorts_bm  OWNER TO delain;
GRANT ALL ON TABLE perso_nb_sorts_bm TO delain;
COMMENT ON TABLE public.perso_nb_sorts_bm  IS 'Nb de fois où un sort BM est utilisé par un joueur (tour)';

ALTER TABLE perso_nb_sorts_bm  ADD CONSTRAINT uniq_pnbsbm_perso_cod_objsortbm_cod UNIQUE (pnbsbm_perso_cod, pnbsbm_objsortbm_cod);




  INSERT INTO quetes.aquete_type_carac(aqtypecarac_cod, aqtypecarac_nom, aqtypecarac_type, aqtypecarac_description, aqtypecarac_aff)
    VALUES (27, 'Code du Perso', 'CARAC', '','N° de perso');
