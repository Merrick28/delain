--
-- Name: drop_rune(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.drop_rune(integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/**************************************************/
/* fonction drop_rune : fait tomber les runes en  */
/*   cas de sort                                  */
/* on passe en params :                           */
/*   $1 = gobj_cod de la rune                     */
/*   $2 = perso_cod                               */
/**************************************************/
/* on a en retour un entier                       */
/**************************************************/
/* créé le 25/07/2003                             */
/* modifié le 05/07/2006 :                        */
/*  on remet la chance de drop et l'objet         */
/*  déposable pour les runes utilisées en sort    */
/* marlyza fix: bug si lancé dé=100               */
/**************************************************/
declare
code_retour integer;
	code_rune alias for $1;
	code_perso alias for $2;
	v_perobj_cod perso_objets.perobj_cod%type;
	v_etage integer;
	num_objet integer;
	etage_ref integer;
	v_etage_retour_rune_monstre integer;
	v_des integer;
	compt_mag integer;
	v_magasin integer;
	v_monstre_porteur integer;

begin
	code_retour := 0;
select into v_perobj_cod,num_objet perobj_cod,perobj_obj_cod
from perso_objets,objets
where perobj_perso_cod = code_perso
  and perobj_obj_cod = obj_cod
  and obj_gobj_cod = code_rune
    limit 1;
--
-- on remet la rune en l'état
--
update objets
set obj_deposable = 'O',obj_chance_drop = 15
where obj_cod = num_objet;

delete from transaction where tran_obj_cod = v_perobj_cod; -- On retire la rune utilisée des transactions.
delete from perso_objets where perobj_cod = v_perobj_cod;
v_des := lancer_des(1,100);
	if v_des < getparm_n(72) then
select into compt_mag count(*)
from lieu
where lieu_tlieu_cod in (14);
v_des := lancer_des(1,compt_mag);
		v_des := v_des - 1;
select into v_magasin
    lieu_cod
from lieu
where lieu_tlieu_cod in (14)
offset v_des
    limit 1;
insert into stock_magasin (mstock_obj_cod,mstock_lieu_cod) values (num_objet,v_magasin);
else
		-- Au sol ou dans l'inventaire d'un monstre ?
		v_des := lancer_des(1,100);
select into v_etage pos_etage
from perso_position,positions
where ppos_perso_cod = code_perso
  and ppos_pos_cod = pos_cod;
select into etage_ref, v_etage_retour_rune_monstre
    etage_reference, etage_retour_rune_monstre from etage
where etage_numero = v_etage;
select into v_monstre_porteur perso_cod
from perso,perso_position, positions, etage
where ppos_pos_cod = pos_cod and ppos_perso_cod = perso_cod
  and pos_etage = etage_numero and etage_reference = etage_ref
  and perso_type_perso = 2 and perso_actif = 'O'
order by random() limit 1;
if found and v_des <= v_etage_retour_rune_monstre then
			insert into perso_objets(perobj_perso_cod, perobj_obj_cod)
				values (v_monstre_porteur, num_objet);
else
			insert into objet_position (pobj_obj_cod,pobj_pos_cod)
				values (num_objet,pos_aleatoire_ref(etage_ref));
end if;
end if;
return code_retour;
end;$_$;


ALTER FUNCTION public.drop_rune(integer, integer) OWNER TO delain;