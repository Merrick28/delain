ALTER TABLE quetes.aquete_element
  ADD CONSTRAINT fk_aqelem_aquete_cod FOREIGN KEY (aqelem_aquete_cod) REFERENCES quetes.aquete (aquete_cod)
   ON UPDATE CASCADE ON DELETE CASCADE;
CREATE INDEX fki_aqelem_aquete_cod
  ON quetes.aquete_element(aqelem_aquete_cod);
