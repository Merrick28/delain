CREATE OR REPLACE FUNCTION public.vide_cosmique(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function vide cosmique : Dissipe toute magie sur une case     */
/*												 et sur x cases aux alentours					*/
/* On passe en paramètres                                        */
/*    $1 = pos_cod              	                               */
/*    $2 = distance concernée		                                 */
/* Pas de code sortie						                                  */
/*****************************************************************/
/* Créé le 22/12/2007                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
        compt integer;    -- variable pour suppression automap
	code_retour text;
	position_case alias for $1;
	v_distance alias for $2;
	v_x integer;
	v_y integer;
	v_etage integer;
-- variable pour les evts
	texte_evt text;
	ligne record;
	ligne_bonus record;
	ligne_arme record;
	ligne_lieu record;
        lieu_depart integer;		--lieu cod du depart passage a partir du lieu arrivee
	lieu_arrivee integer;		--lieu cod de l'arrivée du passage pour sa suppression aussi
	compteur integer;               -- pour saisir le code text dans la zone retour
compteur_text text;	
	v_perso integer;				-- Code du perso suite à la première boucle
	v_nom text;							-- Nom du perso suite à la première boucle

begin
/*on détermine toutes les positions touchées, et donc, tous les persos*/
code_retour := '';
texte_evt := '';
select into v_x,v_y,v_etage
pos_x,pos_y,pos_etage
from positions
where pos_cod = position_case;
		
for ligne in select pos_cod,perso_cod,perso_nom
				from perso,perso_position,positions
				where perso_actif = 'O'
				and perso_tangible = 'O'
				and ppos_perso_cod = perso_cod
				and ppos_pos_cod = pos_cod
				and perso_tangible = 'O'
				and pos_x between (v_x - v_distance) and (v_x + v_distance)
				and pos_y between (v_y - v_distance) and (v_y + v_distance)
				and pos_etage = v_etage 
				and not exists
				(select 1 from lieu_position,lieu
				where lpos_pos_cod = pos_cod
				and lpos_lieu_cod = lieu_cod
				and lieu_refuge = 'O')
                order by perso_cod loop
/*On commence à générer les évènements, et le code retour*/
		texte_evt := '';
		compteur := 1;
		v_perso := ligne.perso_cod;
		v_nom := ligne.perso_nom;
/* Suppression des armes élémentaires  : on a la liste des persos qui peuvent être concernés, on va regarder leur équipement porté*/
		texte_evt := '';
		for ligne_arme in select perobj_obj_cod,obj_nom from perso_objets,objets,objet_generique
				where perobj_perso_cod = v_perso 
				and perobj_equipe = 'O' 
				and obj_cod = perobj_obj_cod
				and obj_gobj_cod = gobj_cod
				and perobj_dfin IS NOT NULL
				and perobj_dfin - '50 hours'::interval < now() loop
				if compteur = 1 then
					code_retour := code_retour||'Pour '||v_nom;
					texte_evt := 'l''arme de [cible], '||ligne_arme.obj_nom||', a été dissipée par une sphère d''annulation de magie';
				else
					texte_evt := texte_evt||', tout comme '||ligne_arme.obj_nom;
				end if;
				compteur := compteur + 1;
				code_retour := code_retour||', '||ligne_arme.obj_nom||' supprimé';
/*Suppression de l objet en question*/
perform f_del_objet (ligne_arme.perobj_obj_cod);
		end loop;
		if compteur > 1 then
		code_retour := code_retour||'. ';
		end if;
	/*rajout de l évènement pour le perso concerné*/
insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_cible)
			values(nextval('seq_levt_cod'),14,now(),1,v_perso,texte_evt,'O','N',v_perso);  

	end loop;

/*Suppression de l objet en question*/

/* Suppression des passages magiques */
compteur := 0;
for ligne_lieu in select lpos_lieu_cod,lpos_pos_cod,lieu_dest from lieu,lieu_position,positions
				where lpos_pos_cod = pos_cod
				and pos_x between (v_x - v_distance) and (v_x + v_distance)
				and pos_y between (v_y - v_distance) and (v_y + v_distance)
				and pos_etage = v_etage 
				and lieu_cod = lpos_lieu_cod 
				and lieu_tlieu_cod	= 10 
				and (lieu_url = 'passage.php' 
					or lieu_url = 'passage_b.php') 
				and lieu_port_dfin is not NULL loop
-- on regarde si on est sur un lieu de depart ou non 
                                if ligne_lieu.lieu_dest <> 0 then
					select into lieu_arrivee lpos_lieu_cod from lieu_position where lpos_pos_cod = ligne_lieu.lieu_dest;
				else
					select into lieu_arrivee lieu_cod from lieu, lieu_position where lieu_dest = lpos_pos_cod and lpos_lieu_cod = ligne_lieu.lpos_lieu_cod;
				end if;
				update lieu set lieu_port_dfin = now() where lieu_cod = ligne_lieu.lpos_lieu_cod;
				update lieu set lieu_port_dfin = now() where lieu_cod = lieu_arrivee;
				compteur := compteur + 1;
				end loop;
compteur_text := compteur;
code_retour := code_retour||'<br>'||compteur_text||' lieu(x) supprimé(s) (passages magiques)';
return code_retour;
end;$function$

