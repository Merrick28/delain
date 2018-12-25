CREATE INDEX idx_aquete_actif
  ON quetes.aquete (aquete_actif ASC NULLS LAST);
CREATE INDEX idx_aquete_dates
  ON quetes.aquete (aquete_date_debut ASC NULLS LAST, aquete_date_fin ASC NULLS LAST);
CREATE INDEX idx_aquete_elt_perso
  ON quetes.aquete_element (aqelem_aqperso_cod ASC NULLS LAST);
CREATE INDEX idx_aqute_aqtype
  ON quetes.aquete_element (aqelem_type ASC NULLS LAST);
CREATE INDEX idx_aquete_ddeb
  ON quetes.aquete (aquete_date_debut ASC NULLS LAST);
CREATE INDEX idx_aquete_dfin
  ON quetes.aquete (aquete_date_fin ASC NULLS LAST);
CREATE INDEX idx_aquete_perso_actif
  ON quetes.aquete_perso (aqperso_actif ASC NULLS LAST);
