
--
-- Name: deplace_code(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION deplace_code(integer, integer, integer default 1) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function deplace_code : idem que deplace sauf qu’un cod_pos   */
/*    est passé en paramètre à la place des coordonnées          */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = pos_cod                                               */
/*    $3 = jump = limit de distance en cas de saut (default 1)   */
/* Le code sortie est une chaine html                            */
/*****************************************************************/
/* Créé le 05/03/2003                                            */
/* Liste des modifications :                                     */
/*    08/03/2003 : modif code sortie                             */
/*    26/01/2004 : code sortie transformé pour html              */
/*    07/02/2006 : ajout de la fonction d’arrivée sur case       */
/*         suppression du double code de sortie                  */
/*    29/11/2006 : ajout d’un marqueur pour forcer ou non le     */
/*         résultat de la fonction à l’affichage                 */
/*    20/12/2009 : Mise en commentaire de la partie fuite pour la*/
/* remplacer par la fonction fuite(perso)			 */
/*    17/12/2010 : Ajout des effets de pré-déplacement		 */
/*    11/11/2013 : Enregistrement de la position de départ dans  */
/* la table ligne_evt (levt_parametres)				 */
/*****************************************************************/
/*
2021-04-29 - Malyza : ajout d'un 3eme parmaetre de sortie (code erreur):
#0   : OK
#1   : Perso non trouvé
#2   : Mur
#3   : Pas assez de PA
#4   : Déplacement sur sa propre position
#5   : Trop loin
#6   : Case trop dangeureuse
#7   : Terrain (monture)
#8   : Trop de poids
#9   : Hors map
#10  : fuite raté
 */
declare
------------------------------------------------
-- variables de retour
------------------------------------------------
	code_retour text;			-- chaine de retour

------------------------------------------------
-- variables concernant la nouvelle position
------------------------------------------------
	v_pos alias for $2; 	-- pos_cod de destination
	v_jump alias for $3; 	-- possibilité de parcourir plus d'un case grace au saut de monture
	x integer;					-- X
	y integer;					-- Y
	e integer;					-- Etage

------------------------------------------------
-- variables du perso
------------------------------------------------
	num_perso alias for $1;		-- perso_cod
	pa integer;					-- PA du perso
	ancien_x integer;			-- ancien X du perso
	ancien_y integer;			-- ancien Y du perso
	ancien_etage integer;		-- ancien etage du perso
	ancien_code_pos integer;	-- ancienne position du perso
	v_type_perso integer;		-- type de perso
	v_perso_pnj integer;		-- 1 pour les PNJ.
	nb_lock integer;			-- nombre de locks
	nb_lock_attaquant integer;	-- nombre de locks attaquant
	nb_lock_cible integer;		-- nombre de locks cible
	v_competence_init integer;	-- competence init pour fuite
	v_competence_modifie integer;	-- competence finale pour fuite
	nb_persos integer;			-- nombre de persos sur la case (fuite)
	fuite_texte text;			--Remplacement par la fonction de fuite
	nb_concentrations integer;	-- nombre de concentrations
	v_poids_max integer;
	v_poids_actu numeric;
	v_poids_objet numeric;
	familier integer;			-- familier rattaché au perso
	v_compt_pvp integer;		-- compteur pvp
	v_malus_pvp integer;		-- malus pour la fuite lié au compteur pvp
	v_compteur_pvp integer;		-- compteur pvp
	nb_lock_malus integer;		-- malus pour la fuite lié aux locks de combat
	malus_nb_persos integer;	-- malus pour la fuite lié aux persos sur la case
	compte_frameless character varying(1);	-- malus pour la fuite lié aux persos sur la case

------------------------------------------------
-- variables fourre tout
------------------------------------------------
	texte text;					-- texte pour évènement
	nb_trans integer;			-- nombre de transaction effacées
	des integer;				-- lancer de dés pour fuite
	tmp_txt text;				-- texte pour améliore (fuite)
	ligne record;
	f_deplace_arrivee text;
	result_deplace_arrivee text;
	curs1 refcursor;
	nb_def integer;
	force_affichage integer;
	temp integer;
	v_anticip integer;
	v_pa_dep integer;			-- Coût du déplacement suivant le terrain
	v_modif_pa_dep integer;			-- cout du déplacement hors bonus/malus
	pa_deplace integer;			-- Coût final du déplacement
	v_monture integer;			-- si c'est un perso joueur qui chevauche une monture
	v_cavalier integer;			-- si c'est une monture qui emène un joueur
	v_pa_terrain integer;			-- si c'est un perso joueur qui chevauche une monture
	v_pa_cavalier integer;			-- nombre de PA du cavalier
	v_perso_fuite integer;			-- c'ets le perso qui ralise la fuite (cavalier dans le cas d'une monture chevauchée)
begin
	force_affichage := 0;
	code_retour := '';

--------------------------
-- Etape 1 : contrôles
--------------------------
	select into x, y, e, f_deplace_arrivee, v_anticip
		pos_x, pos_y, pos_etage, trim(pos_fonction_arrivee), pos_anticipation
	from positions where pos_cod = v_pos;
	if not found then
		code_retour := code_retour || E'1#Erreur : position de destination non trouvée !#9';
		return code_retour;
	end if;

	select into pa, v_poids_max, v_poids_actu
		perso_pa, perso_enc_max, get_poids(perso_cod)
	from perso where perso_cod = num_perso;
	if not found then
		code_retour := code_retour || E'1#Erreur : perso non trouvé !#1#';
		return code_retour;
	end if;

	if (v_poids_actu >= (v_poids_max * 2)) then
		code_retour := code_retour || E'1#Erreur : vous êtes trop encombré pour vous déplacer !#8';
		return code_retour;
	end if;

	if exists (select 1 from murs where mur_pos_cod = v_pos and mur_illusion!='O') then
		code_retour := code_retour || E'1#Erreur : vous ne pouvez vous rendre sur la destination ciblée. Il s’agit soit d’un mur, soit d’un endroit inaccessible par là...#2';
		return code_retour;
	end if;

  v_pa_dep :=  get_pa_dep(num_perso);  -- memo pour eviter de le recalculer à chaque fois

  -- verifier si le perso est en danger dans une zone normalement innacesible: il a une exception pour bouger à 12 PA
  select  getparm_n(9) + pos_modif_pa_dep into v_modif_pa_dep from perso join perso_position on ppos_perso_cod=perso_cod join positions on pos_cod=ppos_pos_cod where perso_cod=num_perso and perso_type_perso=1 and perso_monture is null ;
  if not found then
    -- vérifier aussi pour une monture
    select get_pa_dep_terrain(perso_cod, pos_cod) into v_modif_pa_dep from perso join perso_position on ppos_perso_cod=perso_cod join positions on pos_cod=ppos_pos_cod where perso_cod=num_perso and perso_type_perso=2 ;
    if not found then
       v_modif_pa_dep := 0 ;
    end if;
  end if;


	select into ancien_code_pos, ancien_x, ancien_y, ancien_etage, v_type_perso, v_perso_pnj
		pos_cod, pos_x, pos_y, pos_etage, perso_type_perso, perso_pnj
	from perso_position, positions, perso
	where ppos_perso_cod = num_perso
		and ppos_pos_cod = pos_cod
		and perso_cod = num_perso;
	if (pa < v_pa_dep) and ((v_modif_pa_dep<=12) or (v_modif_pa_dep>12 and pa<12))  then
		code_retour := code_retour || E'1#Erreur : pas assez de PA pour effectuer ce déplacement.#3'; /* pas assez de pa */
		return code_retour;
	end if;

	if ancien_code_pos = v_pos then
		code_retour := code_retour || E'1#Erreur : position d’arrivée égale à la position de départ.#4';
		return code_retour;
	end if;
	if distance(ancien_code_pos,v_pos) > v_jump then
		code_retour := code_retour || E'1#Erreur : distance trop importante entre la position de départ et d’arrivée.#5';
		return code_retour;
	end if;

  v_pa_terrain = get_pa_dep_terrain(num_perso, v_pos) ;
	if v_pa_terrain > 12 and v_modif_pa_dep<=12 then
		code_retour := code_retour || E'1#Le coût de déplacement depuis la case d''arrivée est tel, qu’il est préférable de ne pas s’y rendre!!#6';
		return code_retour;
  elsif v_pa_terrain < 0 then
		code_retour := code_retour || E'1#Votre monture ne peut aller sur ce terrain là!!#7';
		return code_retour;
	end if;

  v_cavalier := f_perso_cavalier(num_perso) ;

  -- s'il y a un cavalier, c'est lui qui doit fuire ses combats
  if v_cavalier is null then
      v_perso_fuite := num_perso ;
  else
      v_perso_fuite := v_cavalier ;
      -- en cas de fuite raté, ce sont les PA du perso qui sont consommés, on s'assure qu'il en a sinon, c'est un ratage directe!
  end if;

---------------------------
-- on regarde si lock
---------------------------
	select count(lock_cod) into nb_lock_cible from lock_combat where lock_cible = v_perso_fuite;
	select count(lock_cod) into nb_lock_attaquant from lock_combat where lock_attaquant = v_perso_fuite;
	nb_lock := nb_lock_cible + nb_lock_attaquant;

---------------------------
-- si lock on passe à la fuite
---------------------------
	if nb_lock != 0 then

	    -- s'il y a un cavalier, il doit avoir des PA pour tenter de fuir, car il lui seront décomptés en cas d'échec!
      if v_cavalier is not null then
           select perso_pa into v_pa_cavalier from  perso where perso_cod = v_cavalier and perso_pa >= getparm_n(19) ;
           if not found then
              return '1#' || code_retour || ' une monture ne peut fuir un combat si le cavalier est engagé dans un combat et qu''il n''a pas de PA' || '#10' ;
           end if;
      end if;

      force_affichage := 1;
      select into fuite_texte fuite(v_perso_fuite);
      if split_part(fuite_texte, '#', 1) = '1' then  --la fuite est ratée, on renvoie directement le code_retour
          code_retour := '1#' || code_retour || split_part(fuite_texte,'#',2);
          return code_retour  || '#10';
      else --La fuite est réussie, on continue
          code_retour := code_retour || split_part(fuite_texte,'#',2);
      end if;
	end if;

---------------------------
-- on vérifie les effets de pré-déplacement
---------------------------
	if v_anticip > 0 and v_type_perso = 1 then
      -- 1 = Case plouf : Pas de déplacement réel
      if v_anticip = 1 then
        -- Coût
        pa_deplace := 2;
        -- Evènement
        texte := '[perso_cod1] s’est embourbé et a été contraint de reculer.';
        insert into ligne_evt (
          levt_cod,
          levt_tevt_cod,
          levt_date,
          levt_type_per1,
          levt_perso_cod1,
          levt_texte,
          levt_lu,
          levt_visible)
        values (nextval('seq_levt_cod'),
          88,
          'now()',
          1,
          num_perso,
          texte,
          'O',
          'O');
        -- Texte
        code_retour := code_retour || 'Vous vous enfoncez soudainement dans le marais ! Pataugeant lourdement, vous parvenez tout juste à reculer et à reprendre pied.';
        force_affichage := 1;
      end if;
	else
---------------------------
-- on déplace
---------------------------
		-- Coût normal
		pa_deplace := LEAST(12, v_pa_dep) ;

		update perso_position
		set ppos_pos_cod = cast(v_pos as integer)
		where ppos_perso_cod = cast(num_perso as integer);

		code_retour := code_retour || 'Déplacement effectué !';

---------------------------
-- les EA liés au déplacement du perso
---------------------------
    code_retour := code_retour || execute_fonctions(num_perso, null, 'DEP', json_build_object('pilote',num_perso,'ancien_pos_cod',ancien_code_pos,'ancien_etage',ancien_etage,'nouveau_pos_cod',v_pos,'nouveau_etage',e)) ;

---------------------------
-- les EA liés au déplacement de la monture ou du cavalier (entrainé par celui des 2 qui pilote, car maintenant les perso peuvent eux-aussi déclencher des EA de déplacement)
---------------------------
    v_monture := coalesce(f_perso_monture(num_perso), f_perso_cavalier(num_perso)) ;
    if v_monture is not null then
        code_retour := code_retour || execute_fonctions(v_monture, num_perso, 'DEP', json_build_object('pilote',num_perso,'ancien_pos_cod',ancien_code_pos,'ancien_etage',ancien_etage,'nouveau_pos_cod',v_pos,'nouveau_etage',e)) ;
    end if;
   /* select m.perso_cod into v_monture
        from perso as p
        join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
        where p.perso_cod=num_perso and p.perso_type_perso=1 ;
    if found then
        code_retour := code_retour || execute_fonctions(v_monture, num_perso, 'DEP', json_build_object('ancien_pos_cod',ancien_code_pos,'ancien_etage',ancien_etage,'nouveau_pos_cod',v_pos,'nouveau_etage',e)) ;
    end if;*/

---------------------------
-- on met un évènement
---------------------------
    -- cas normal montre seul ou joueur avec ou sans monture
    texte := 'Déplacement de ' || trim(to_char(ancien_x,'99999999')) || ',' || trim(to_char(ancien_y,'99999999')) || ',' || trim(to_char(ancien_etage,'99999999')) || ' vers ' || trim(to_char(x,'99999999')) || ',' || trim(to_char(y,'99999999')) || ',' || trim(to_char(e,'99999999'));
    insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_parametres)
    values (nextval('seq_levt_cod'), 2, now(), 1, num_perso, texte, 'O', 'O', ancien_code_pos);


    if v_cavalier is not null then

        -- cas particulier d'un monstre qui se déplace avec un joueur sur le dos. (event=54 effet auto)
        texte := '[attaquant] s’est déplacé avec [cible] de ' || trim(to_char(ancien_x,'99999999')) || ',' || trim(to_char(ancien_y,'99999999')) || ',' || trim(to_char(ancien_etage,'99999999')) || ' vers ' || trim(to_char(x,'99999999')) || ',' || trim(to_char(y,'99999999')) || ',' || trim(to_char(e,'99999999'));
        perform insere_evenement(num_perso, v_cavalier, 54, texte, 'O', 'N', null);

    end if;
	end if;


---------------------------
-- on enlève les PA
---------------------------
	update perso set perso_pa = pa - pa_deplace	where perso_cod = num_perso;

---------------------------
-- si on se déplace avec une monture, traiter le comportement particulier de la monture sur certain terrain
---------------------------
  code_retour := code_retour || monture_comportement(num_perso) ;


---------------------------
-- on enlève les transactions
---------------------------
	select into familier max(pfam_familier_cod) from perso_familier INNER JOIN perso ON perso_cod=pfam_familier_cod WHERE perso_actif='O' and pfam_perso_cod = num_perso;

	delete from transaction
	where tran_vendeur = familier;
	get diagnostics temp = row_count;

	delete from transaction
	where tran_vendeur = num_perso;
	get diagnostics nb_trans = row_count;

	if (nb_trans + temp) != 0 then
		texte := 'Les transactions en cours en tant que vendeur ont été annulées y compris pour votre familier !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values (nextval('seq_levt_cod'),17,'now()',1,num_perso,texte,'O','N');
	end if;

	delete from transaction
	where tran_acheteur = familier;
	get diagnostics temp = row_count;

	delete from transaction
	where tran_acheteur = num_perso;
	get diagnostics nb_trans = row_count;

	if (nb_trans+temp) != 0 then
		texte := 'Les transactions en cours en tant qu’acheteur ont été annulées, y compris pour votre familier !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values (nextval('seq_levt_cod'),17,'now()',1,num_perso,texte,'O','N');
	end if;
---------------------------
-- on enlève les locks
---------------------------
	delete from lock_combat where lock_attaquant = num_perso;
	delete from lock_combat where lock_cible = num_perso;

---------------------------
-- on enlève les ripostes
---------------------------
	delete from riposte where riposte_attaquant = num_perso;

---------------------------
-- on regarde si il n’y a pas une fonction d’arrivée qui traine
---------------------------
	if trim(f_deplace_arrivee) != '' and v_perso_pnj != 1 and v_type_perso = 1 then
		f_deplace_arrivee := 'select ' || replace(f_deplace_arrivee, '[perso]', trim(to_char(num_perso, '99999999999999')));
		f_deplace_arrivee := replace(f_deplace_arrivee, '[position]', trim(to_char(v_pos, '99999999999999')));

		if trim(f_deplace_arrivee) is not null then
			execute f_deplace_arrivee into result_deplace_arrivee;
			if trim(result_deplace_arrivee) != '' then
				force_affichage := 1;
				code_retour := code_retour || '<hr><p><b>' || result_deplace_arrivee || '</b></p>';
			end if;
		end if;
	else
		-- Vérif sur la version frameless à supprimer si elle devient seule et unique version du jeu.
		select into compte_frameless compt_frameless from compte
		inner join perso_compte on pcompt_compt_cod = compt_cod
		where pcompt_perso_cod = num_perso;

		if compte_frameless != 'O' then
			des := lancer_des(1,100);
			if (des > 80) then
				result_deplace_arrivee := choix_rumeur();
				code_retour := code_retour || '<hr>Rumeur : <i>' || result_deplace_arrivee || '</i>';
				force_affichage := 1;
			end if;
		end if;
	end if;
	code_retour := trim(to_char(force_affichage,'9')) || '#' || code_retour || '#0';
	return code_retour;
end;$_$;


ALTER FUNCTION public.deplace_code(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION deplace_code(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION deplace_code(integer, integer, integer) IS 'Gère le déplacement d’un perso.';
