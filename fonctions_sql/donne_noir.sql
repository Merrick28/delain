CREATE OR REPLACE FUNCTION public.donne_noir(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************/
/* donne_noir                                     */
/**************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_objet integer;
	v_pa integer;
	temp integer;
	temp2 integer;
	v_pv integer;
	v_pos integer;
	texte_evt text;
begin
----------------------------------------------------
-- DEBUT CONTROLES AVANT ACTION  
----------------------------------------------------
--
----------------------------------------------------
-- DEBUT vérification de la possession du cadeau
----------------------------------------------------
	select into v_objet
		obj_cod
		from objets,perso_objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = 327;
	if not found then
		code_retour := 'Erreur ! Vous n''avez pas de cadeau dans votre inventaire !';
		return code_retour;
	end if;
----------------------------------------------------
-- FIN   vérification de la possession du cadeau
----------------------------------------------------
--
----------------------------------------------------
-- DEBUT vérification des PA
----------------------------------------------------
	select into v_pa
		perso_pa
		from perso
		where perso_cod = personnage;
	if v_pa < getparm_n(99) then
		code_retour := 'Erreur ! Vous n''avez pas assez de PA pour cette action !';
		return code_retour;
	end if;
----------------------------------------------------
-- FIN   vérification des PA
----------------------------------------------------
-- 
----------------------------------------------------
-- DEBUT vérification de la présence de lutin
----------------------------------------------------
	select into v_pos
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = personnage;
	select into temp
		perso_cod
		from perso,perso_position
		where ppos_pos_cod = v_pos
		and ppos_perso_cod = perso_cod
		and perso_race_cod = 54;
	if not found then
		code_retour := 'Erreur ! il n''y a pas de lutin sur votre case !';
		return code_retour;
	end if;
----------------------------------------------------
-- FIN   vérification de la présence de lutin
----------------------------------------------------	
--	
----------------------------------------------------
-- FIN   CONTROLES AVANT ACTION  
----------------------------------------------------
--
----------------------------------------------------
-- DEBUT don, fonction principale
----------------------------------------------------
--
----------------------------------------------------
-- DEBUT destruction cadeau
----------------------------------------------------
	v_pa := f_del_objet(v_objet);
----------------------------------------------------
-- FIN   destruction cadeau
----------------------------------------------------
--
	code_retour := 'Le lutin tourne la tête vers vous ce qui fait sonner le grelot de son bonnet. Il ronchonne :<br>
<br>
« - Mais quel costume de naze Vivement que tout ça finisse pour que je puisse me saper correctement »<br>
<br>
Puis il daigne faire attention à votre présence, vous prend le cadeau des mains en précisant dune voix peu amène :<br>
<br>
« - Merci pour le cadeau. Monsieur Jacques va pouvoir le transformer à sa guise » Un sourire sadique ponctue la fin de sa phrase.<br>';
	update perso 
		set perso_pa = perso_pa - getparm_n(99)
		where perso_cod = personnage;
	select into temp2 cn_perso_cod 
		from compt_noel
		where cn_perso_cod = personnage;
	if not found then
		insert into compt_noel
			(cn_perso_cod,cn_compteur)
			values
			(personnage,-1);
	else
		update compt_noel
			set cn_compteur = cn_compteur - 1
			where cn_perso_cod = personnage;
	end if;
	texte_evt := '[attaquant] a donné un cadeau à [cible].';
	insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(61,now(),personnage,texte_evt,'O','O',personnage,temp);
	insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(61,now(),temp,texte_evt,'N','O',personnage,temp);
	return code_retour;

----------------------------------------------------
-- FIN   don, fonction principale
----------------------------------------------------
end;$function$

