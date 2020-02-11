
update competences set comp_libelle='Identifier équipement' where comp_cod=28;

update competences set comp_libelle='Réparer équipement' where comp_cod=79;

select * from type_objet where tobj_cod in (40,41)