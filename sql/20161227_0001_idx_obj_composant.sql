CREATE INDEX formule_composant_frmco_gobj_cod_idx
  ON public.formule_composant
  USING btree
  (frmco_gobj_cod);