CREATE OR REPLACE FUNCTION public.enlumineur(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* enlumineur				        			              */
/*Permet à un personnage de devenir enlumineur	*/
/* On passe en paramètres:                      */
/*   $1 = perso_cod					        					  */
/*   $2 = Competence actuelle						        */
/************************************************/
/* Créé le 28 Janvier 2010     	                */
/************************************************/

declare
    -- Parameters
    personnage alias for $1;
    v_comp alias for $2;
		brouzoufs integer; --brouzoufs nécessaires
		v_pa integer;
		v_po integer; --brouzoufs du perso
		v_int integer;
		v_comp_new integer; --Nouvelle compétence acquise
		pourcent integer;
		competence integer;
		seuil integer;

    -- initial data
    v_x_source integer; -- source X position
    v_y_source integer; -- source Y position
    v_et_source integer; -- etage de la source
		v_pos_perso integer;	--Pos_cod du personnage
		v_pos integer;			--Position de la quête
    v_type_source integer; -- Type perso de la source
    v_distance_int integer; -- Range
    v_cibles_type character; -- Type de cible (SAERT)
    v_cibles_nombre integer; -- Nombre de cibles maxi
    v_race_source integer; -- La race de la source
    v_position_source integer; -- Le pos_cod de la source
 		v_pv integer;
 		v_pv_max integer;
    -- Output and data holders 
    des integer;
    code_retour text;
    v_temp text;
    v_bonus_existant text;

begin
    -- Initialisation des conteneurs
/*************/
/* Controles */
/*************/
	code_retour := '';
	select into v_et_source pos_etage from perso_position,positions where ppos_perso_cod = personnage and ppos_pos_cod = pos_cod;
--On détermine quel cas on va devoir traiter :
if v_comp = 0 then --Le perso commence la quête d'enluminure, on doit donc trouver un endroit pour qu'il fasse son introspection
	select into v_pos pos_cod from positions 
	where pos_etage = v_et_source
	and pos_fonction_dessus is null 
	and distance(pos_cod,(select ppos_pos_cod from perso_position where ppos_perso_cod = personnage)) >30
	and not exists (select 1 from murs where mur_pos_cod = pos_cod)
	and not exists (select 1 from lieu_position where lpos_pos_cod = pos_cod)
	--and pos_cod not in (select mur_pos_cod from murs)
	--and pos_cod not in (select lpos_cod from lieu_position)
	order by random() limit 1;
	if not found then
		code_retour := 'Le magicien se retourne vers vous : et vous dit :
										<br>« <i>Malheureusement, les conditions ne sont pas remplies ici. Pour prouver votre valeur, vous devez aller dans un niveau de ces souterrains plus grand.</i>»<br>
										<br>Cet étage n’est pas assez grand pour que cette quête se déroule dans de bonnes conditions.';
		return code_retour;
	else
		code_retour := 'Le magicien se retourne vers vous et vous dit :
		<br>« <i>Pour prouver votre capacité à être un bon enlumineur, vous allez devoir affronter vos propres peurs et vos propres doutes.
		<br>Pour cela, vous devrez d''abord trouver un endroit caché que seul vous pourrez découvrir. Suivez votre boussole, elle vous guidera et vous donnera la direction. 
		<br>Lorsque vous serez à votre potentiel d’action maximum, les vents magiques parleront.
		Vous devrez alors vaincre votre propre Image, et seule sa mort de vos mains pourra nous convaincre de votre valeur.
		<br>Une fois que vous aurez réalisé cette quête introspective, vous pourrez récupérer les deux objets nécessaires à tout bon enlumineur, une plume, et une peau qui pourra servir de parchemin.</i>»<br>
		<br><br>Dans l’onglet <b>Divers</b> de votre feuille de personnage, une nouvelle information est venue se positionner. Consultez la fréquemment pour déterminer où vous devez vous rendre.
		<br>Attention, les événements qui s’y produiront pourront être dangereux, ne les sous estimez pas !
		<br>Il vous faudra <b>activer votre DLT à cet endroit</b> pour que les phénomènes se produisent. Dans le cas contraire, vous ne pourrez pas avancer plus avant dans votre quête.
		<br>Le monstre qui apparaîtra sera le votre, <b>vous devrez être l’aventurier qui portera le coup de grâce à ce monstre</b>.
		<br>';
		--On enregistre la position, on incrémente la quête
		insert into quete_perso (pquete_perso_cod,pquete_quete_cod,pquete_nombre,pquete_param_texte) 
		values (personnage,16,1,v_pos);
		update positions set pos_fonction_dessus = 'deb_tour_generique_case([perso],''QENL'','||to_char(personnage,'99999999999999')||',0,''P'',100,''Evènement'',''Message'')' where pos_cod = v_pos;
		return code_retour;
	end if;
--Fin de la quête premier niveau
elsif v_comp = 1 then --Le perso est engagé dans la quête d'enlumineur et a tué le monstre
	insert into perso_competences (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur) values (personnage,91,20);
	--On supprime les objets de quête
	delete from perso_objets where perobj_perso_cod = personnage and perobj_obj_cod in (select obj_cod from objets where obj_gobj_cod in (730,731));
	-- La quête est terminée
	delete from quete_perso where pquete_quete_cod = 16 and pquete_perso_cod = personnage;
else
	select into v_po,v_pa,v_int perso_po,perso_pa,perso_int
			from perso
			where perso_cod = personnage;
	select into pourcent,competence pcomp_modificateur,pcomp_pcomp_cod 
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod in (91,92,93);
	if competence = 91 then  --On donne les valeurs aux variables pour le niv 2
		v_comp_new := 92;
		brouzoufs := 10000;
		seuil := 90;
	end if;
	if competence = 92 then  --On donne les valeurs aux variables pour le niv 3
		v_comp_new := 93;
		brouzoufs := 20000;
		seuil := 100;
	end if;	
	if competence != v_comp then
		return '<br>Vous ne pouvez pas vous empêcher de tricher ?<br><br>';
	end if;
	if competence = 93 then  --On a atteint le niveau max
			return 'br>Vous êtes déjà au niveau 3 d’enluminure<br><br>';
	end if;
/*Compétence Niv 2 et Niv 3 */
	if v_comp = 91 or v_comp = 92 then
		if v_po < brouzoufs then
				return '<br>Vous n’avez pas suffisamment de brouzoufs pour réaliser cette action.<br><br>';
		end if;
		if v_pa != 12 then
			return '<br>Vous n’avez pas suffisamment de pa pour réaliser cette action.<br><br>';
		end if;
		if pourcent < seuil then --Compétence minimum à atteindre
			return '<br>Vous n’avez pas suffisamment travaillé l’enluminure pour prétendre atteindre un nouveau degré de maîtrise<br><br>';
		end if;
		--On met à jour les PA et les brouzoufs
		update perso set perso_po = perso_po - brouzoufs,perso_pa = 0 where perso_cod = personnage;

		--On met à jour les compétences
		 delete from perso_competences 
		 				where pcomp_perso_cod = personnage
									and pcomp_pcomp_cod in (91,92,93);
		insert into perso_competences (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur) 
													values (personnage,v_comp_new,pourcent);
		code_retour := code_retour||'<br>« <i>Félicitations, vous avez bien profité de mon enseignement. Vous avez fait un nouveau pas dans la congrégation des enlumineurs !</i>»<br><br>';
		return code_retour;
/*Fin du traitement pour les niveaux 2 et 3*/

	
	end if;
end if;

return code_retour;
end;$function$

