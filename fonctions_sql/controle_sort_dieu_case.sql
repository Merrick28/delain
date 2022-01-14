--
-- Name: controle_sort_dieu_case(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION controle_sort_dieu_case(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/************************************************/
/* fonction controle_sort_dieu_case             */
/*  effectue les controles préalables au        */
/*  lancement du sort par un perso              */
/* on passe en params :                         */
/*  $1 = sort_cod à lancer                      */
/*  $2 = perso_cod du lanceur                   */
/*  $3 = pos_cod cible                          */
/************************************************/
/* on a en retour une chaine séparées par ;     */
/*   pos0 = résultat (0 OK, -1 bad)             */
/*   pos1 = motif si bad                        */
/************************************************/
/* Créé le 21/09/2003                           */
/************************************************/
declare
	code_retour text;			-- code retour
	num_sort alias for $1;
	lanceur alias for $2;
	pos_cible alias for $3;
-- sort
	cout_pa integer;			-- cout en PA du sort
	distance_sort integer;	-- distance maxi pour sort
	lanceur_pa integer;		-- PA du lanceur
	nb_sort integer;			-- nombre de fois ou la lanceur à lancé ce sort dans le tour
	v_pnbs_cod integer;		-- pnbs_cod pour perso_nb_tours
	pos_lanceur integer;		-- position du lanceur
	distance_cibles integer;-- distance entre les cibles
	compt_rune integer;		-- nombre de runes
	nom_rune varchar(50);	-- nom de la rune
	temp_traj text;			-- variable pour controle trajeactoire
	x_mur integer;				-- X du mur
	y_mur integer;				-- Y du mur
	ligne_rune record;
	pa_magie integer;				-- bonus en cout de lancer de sort
	niveau_religion integer;
	v_dieu_cod integer;
	v_niveau_sort integer;
-- limite pour les sorts lvl 4
        v_perso_puissance integer;
        v_limite integer;
        compt integer;

begin
	code_retour := '0;';
	if to_char(now(),'DD/MM/YYYY') in ('22/04/2011', '23/04/2011') then
		return 'Un silence pesant fait écho à votre requête...';
	end if;
	select into cout_pa sort_cout from sorts where sort_cod = num_sort;
-- on controle les PA
	select into lanceur_pa,pos_lanceur
		perso_pa,ppos_pos_cod
		from perso,perso_position
		where perso_cod = lanceur
		and ppos_perso_cod = lanceur;
	cout_pa := cout_pa + valeur_bonus(lanceur, 'PAM');
	if lanceur_pa < cout_pa then
		code_retour := 'Erreur : Vous n''avez pas assez de PA pour lancer ce sort !';
		return code_retour;
	end if;
-- on vérifie quand même que le sort soit associé au bon dieu
	select into v_dieu_cod,niveau_religion
		dper_dieu_cod,dper_niveau
		from dieu_perso
		where dper_perso_cod = lanceur;
	if not found then
		code_retour := 'Erreur : Vous n''êtes affilié à aucune religion !';
		return code_retour;
	end if;
	select into v_niveau_sort dsort_niveau
		from dieu_sorts
		where dsort_dieu_cod = v_dieu_cod
		and dsort_sort_cod = num_sort;
	if not found then
		code_retour := 'Erreur : Ce sort n''est pas accessible par votre dieu !';
		return code_retour;
	end if;
	if v_niveau_sort > niveau_religion then
		code_retour := 'Erreur : Vous n''avez pas le niveau de religion nécessaire pour lancer ce sort !';
		return code_retour;
	end if;
-- on vérifie les distances
	select into distance_sort
		sort_distance
		from sorts
		where sort_cod = num_sort;
	distance_cibles := distance(pos_lanceur,pos_cible);
	if distance_cibles > distance_sort then
		code_retour := 'Erreur : La cible est trop éloignée pour lancer le sort !';
		return code_retour;
	end if;
	if distance_sort > 1 then
		temp_traj := trajectoire_magie(pos_lanceur,pos_cible,lanceur,1);
		if split_part(temp_traj,';',1) != '0' then
			select into x_mur,y_mur pos_x,pos_y
			from positions
			where pos_cod = split_part(temp_traj,';',2);
			code_retour := 'Votre sort arrive dans un mur en position '||trim(to_char(x_mur,'999999'))||', '||trim(to_char(y_mur,'999999'));
			return code_retour;
		end if;
	end if;
	return code_retour;
-- modification azaghal : dorénavant les sorts de niv 3 et plus demande
-- de la puissance en points de prière personnelle.
  if v_niveau_sort > 2 then
  select into v_perso_puissance
		dper_points
		from dieu_perso
		where dper_perso_cod = lanceur;

  compt := v_niveau_sort * 5;
  v_limite := getparm_n(53);
  v_limite := v_limite + compt;
-- on regarde si le lanceur à plus de PP que la limite minimale
    if v_perso_puissance <= v_limite then
    code_retour := 'Erreur : vous n''avez pas assez prié et manquez de puissance auprès de votre dieu !';
		return code_retour;
    end if;
   end if;
end;
$_$;


ALTER FUNCTION public.controle_sort_dieu_case(integer, integer, integer) OWNER TO delain;
