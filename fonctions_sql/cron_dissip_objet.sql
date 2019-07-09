
CREATE OR REPLACE FUNCTION public.cron_dissip_objet()
  RETURNS void
  LANGUAGE plpgsql
  AS
$_$/*****************************************************************/
/* function cron_dissip_objet : détruit les objets restés        */
/* trop longtemps au sol. En compensation, il y a                */
/* une augentation des vents magiques                            */
/*****************************************************************/
/* Créé le 04/07/2019 - Marlyza                                  */
/*****************************************************************/
declare
			v_coeff numeric;
			v_vent numeric;
			obj record;
begin

-- recuperation du coefficient d'absorbtion des vents.
select into v_coeff to_number( getparm_t(142), '9999999.99');

/*
-- mettre en pause le temps de faire un état des lieux!
for obj in select obj_cod, pobj_pos_cod, obj_poids from objets
  join objet_position on pobj_obj_cod=obj_cod
  join objet_generique on gobj_cod=obj_gobj_cod
  join type_objet on tobj_cod=gobj_tobj_cod
  where pobj_dlache<now() - (tobj_degradation::text || ' DAYS')::interval AND tobj_degradation>0
  order by pobj_dlache
  limit 1000

  loop


    if obj.obj_poids < 1 then
      v_vent:= (100 * v_coeff * obj.obj_poids );
    else
      v_vent:= (5 * v_coeff * obj.obj_poids );
    end if;

    -- augmentation du vent
    update positions set pos_magie = pos_magie + v_vent where pos_cod = obj.pobj_pos_cod ;

    -- supression de l'objet
    perform f_del_objet(obj.obj_cod);


  end loop;
*/
end;$_$;
ALTER FUNCTION public.cron_dissip_objet()  OWNER TO delain;
