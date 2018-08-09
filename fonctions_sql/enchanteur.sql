CREATE OR REPLACE FUNCTION public.enchanteur(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***************************************/
/* fonction enchanteur : détermine     */
/* le passage au statut d’enchanteur   */
/* $1 = perso_cod                      */
/* $2 = comp_cod competence à atteindre*/
/***************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_comp alias for $2;
	temp integer;
	brouzoufs integer; --brouzoufs nécessaires
	v_pa integer;
	v_po integer; --brouzoufs du perso
	v_int integer;
	v_comp_new integer; --Nouvelle compétence acquise
	pourcent integer;
	competence integer;
	seuil integer;
	indice text;
	code_perso text;
	indice_perso text;
	code_param text;
	code text;
	
begin

/*************/
/* Controles */
/*************/
	code_retour := '';
	select into v_po,v_pa,v_int perso_po,perso_pa,perso_int
			from perso
			where perso_cod = personnage;
	select into pourcent,competence pcomp_modificateur,pcomp_pcomp_cod 
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod in (88,102,103);
	if not found then --On donne les valeurs aux variables pour une nouvelle compétence
		v_comp_new := 88;
		pourcent := v_int * 2;
	end if;
	if competence = 88 then  --On donne les valeurs aux variables pour le niv 2
		v_comp_new := 102;
		brouzoufs := 10000;
		seuil := 85;
	end if;
	if competence = 102 then  --On donne les valeurs aux variables pour le niv 3
		v_comp_new := 103;
		brouzoufs := 20000;
		seuil := 100;
	end if;	
	if v_comp_new != v_comp then
		return '<br>Vous ne pouvez pas vous empêcher de tricher ?<br><br>';
	end if;
	if competence = 103 then  --On a atteint le niveau max
			return 'br>Vous êtes déjà au niveau 3 du forgeamage<br><br>';
	end if;
	
/*On commence le traitement en fonction de la competence*/
/*Compétence Niv 2 et Niv 3 */
	if v_comp = 102 or v_comp = 103 then
		if v_po < brouzoufs then
				return '<br>Vous n''avez pas suffisamment de brouzoufs pour réaliser cette action.<br><br>';
		end if;
		if v_pa != 12 then
			return '<br>Vous n''avez pas suffisamment de pa pour réaliser cette action.<br><br>';
		end if;
		if pourcent < seuil then --Compétence minimum à atteindre
			return '<br>Vous n''avez pas suffisamment travaillé le forgeamage pour prétendre atteindre un nouveau degré de maitrise<br><br>';
		end if;
		--On met à jour les PA et les brouzoufs
		update perso set perso_po = perso_po - brouzoufs,perso_pa = 0 where perso_cod = personnage;

		--On met à jour les compétences
		 delete from perso_competences 
		 				where pcomp_perso_cod = personnage
									and pcomp_pcomp_cod in (88,102,103);
		insert into perso_competences (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur) 
													values (personnage,v_comp_new,pourcent);
		code_retour := code_retour||'<br>« <i>Félicitations, vous avez bien profité de mon enseignement. Vous avez fait un nouveau pas dans la congrégation des enchanteurs !</i>»<br><br>';
		return code_retour;
/*Fin du traitement pour les niveaux 2 et 3*/

/*Compétence Niv 1, deux étapes : quête déjà commencée, ou alors quête reprise. La quête reprise sera traitée en php pour intéger le code à mettre */
	elsif v_comp = 88 then
		select into code_perso pquete_param_texte from quete_perso 
				where pquete_quete_cod = 15 and pquete_perso_cod = personnage;
		if not found then
			code_retour := code_retour||'<br>« <i>Vous voici à une étape importante ! Vous souhaitez devenir enchanteur, mais pour cela, vous devrez répondre à une énigme particulière.
																		<br>Il s''agira de <b>6 questions</b> qui vous donneront le code que vous devrez me restituer.
																		<br>Ce code n''a aucun sens, mais je saurai le comprendre.
																		<br>Voici les questions pour lesquelles vous devez trouver la réponse. Vous ne prendrez à chaque fois que la première lettre de chacune des réponses.
																		<br><br><b><ul>';
			code_perso := '';
			indice_perso := '';
			code := 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			for i in 1..6 loop
				code_param := substring(code,lancer_des(1,26),1);
				code_perso := code_perso||code_param;
				select into indice qpartxt_texte2 from quete_param_texte 
							where qpartxt_quete_cod = 15 and qpartxt_texte1 = code_param
							order by random() limit 1;		
				code_retour := code_retour||'<li>'||indice||'</li><br>';
				indice_perso := indice_perso||indice||'<br>';
			end loop;
			code_retour := code_retour||'</ul></b><br>Mettez toutes vos réponses (<b>première lettre de chaque réponses</b>) bout à bout, et je vous estimerais alors digne de rejoindre notre confrérie ! Revenez me voir lorsque vous aurez tout trouvé.</i>»<br><br>';
			code_perso := code_perso||';'||indice_perso;
			insert into quete_perso (pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_param_texte) values (15,personnage,1,code_perso);
		else
		--On met à jour les PA et les brouzoufs
			update perso set perso_pa = 0 where perso_cod = personnage;

		--On met à jour les compétences
		 	delete from perso_competences 
		 				where pcomp_perso_cod = personnage
									and pcomp_pcomp_cod in (88,102,103);
			insert into perso_competences (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur) 
													values (personnage,v_comp_new,pourcent);
			code_retour := code_retour||'Mais c''est formidable, vous êtes un génie ! Profitez donc de mon enseignement comme il se doit, et faites des choses ... magiques !';
			update quete_perso set pquete_nombre = 2 where pquete_quete_cod = 15 and pquete_perso_cod = personnage;
		end if;
		return code_retour;
	end if;
/*Fin du traitement pour les niveaux 1*/

end;$function$

