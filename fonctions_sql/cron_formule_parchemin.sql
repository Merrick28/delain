CREATE OR REPLACE FUNCTION public.cron_formule_parchemin()
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cron_formule_parchemin : Crée les objets parchemins	 */ 
/*                                                               */
/*****************************************************************/
/* Créé le 18/04/2010                                            */
/*****************************************************************/
declare
		ligne record;
		objet_fini integer;
		temps text;
		texte_evt text;

begin
	for ligne in select foi_obj_cod,foi_gobj_cod,foi_formule_cod,foi_date_crea,frmpr_gobj_cod,frmpr_num,perobj_perso_cod,frm_temps_travail 
											from formule_objet_inacheve,formule,formule_produit,perso_objets
											where foi_formule_cod = frm_cod 
											and frmpr_frm_cod = frm_cod 
											and frm_type = 4
											and perobj_obj_cod = foi_obj_cod loop
		temps := cast (ligne.frm_temps_travail as text);
		select into objet_fini foi_obj_cod from formule_objet_inacheve 
						where foi_obj_cod = ligne.foi_obj_cod and (foi_date_crea + (temps || ' minutes')::interval) < now();
		if found then 
			--On cree l'objet dans l'inventaire du perso possédant la peau
			perform cree_objet_perso_nombre(ligne.frmpr_gobj_cod,ligne.perobj_perso_cod,ligne.frmpr_num);
			--On efface l'objet en cours de réalisation
			delete from formule_objet_inacheve where foi_obj_cod = ligne.foi_obj_cod;
			perform f_del_objet (ligne.foi_obj_cod);
			--on rentre un évènement
	texte_evt := 'Une peau vient de finir de sécher, pour [perso_cod1].';	
 	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
	      	values(nextval('seq_levt_cod'),83,now(),1,ligne.perobj_perso_cod,texte_evt,'N','N',ligne.perobj_perso_cod,ligne.perobj_perso_cod);			
		end if;
	end loop;

end;$function$

