--
-- Name: objet_valide_usage(integer, integer, integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION public.objet_valide_usage(integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function objet_utilisation                           */
/* parametres :                                         */
/*  $1 = v_perso_cod qui utilise l'objet                */
/*  $2 = v_obj_cod						            */
/*  $3 = v_objusebm_cod						            */
/* Sortie :                                             */
/*  code_retour = code objusebm_cod de l'objet          */
/*  (pas celui du generique)          */
/*  code d'erreur en negatif         */
/*********************************************************/
/**************************************************************/
/*																														*/
/**************************************************************/
declare
  v_perso_cod alias for $1;	-- perso_cod
  v_obj_cod alias for $2;	-- obj_cod
  v_objusebm_cod alias for $3;	-- usage de l'objet ou de son generique
  v_temp integer;				-- variable temporaire
  v_gobj_cod integer;				-- code de l'objet générique
  v_nb_usage_max integer;			-- nombre d'utilisation maximum de l'objet
  v_nb_usage integer;				-- nombre d'utilisation de l'objet
  v_vide_detruit char(1);		    -- destruction de l'objet si vide

begin

    /*********************************************************/
    /*                  C O N T R O L E S                    */
    /*********************************************************/
    -- contrle sur l'existance de l'objet (recupération du code de l'objet générique)
    select obj_gobj_cod into v_gobj_cod from objets where obj_cod = v_obj_cod limit 1;
    if not found then
        return -1;      -- objet non trouvé
    end if;

    -- controle sur la possession et de l'identification de l'objet par le personnage
    select perobj_cod into v_temp from perso_objets where perobj_perso_cod = v_perso_cod and perobj_obj_cod = v_obj_cod and perobj_identifie='O' limit 1;
    if not found then
        return -2;      -- objet non possédé (ou non identifié) par le personnage
    end if;

    -- controle sur l'existence de l'usage de l'objet
    select objusebm_cod into v_temp from objets_usage_bm where objusebm_obj_cod = v_obj_cod and objusebm_cod = v_objusebm_cod limit 1;
    if not found then
        -- usage de l'objet non trouvé => verification de l'existence d'un usage générique
        select objusebm_cod into v_temp from objets_usage_bm where objusebm_gobj_cod = v_gobj_cod and objusebm_cod = v_objusebm_cod limit 1;
        if not found then
            return -3;      -- usage de sur l'objet et son générique non trouvé
        end if;

        -- créer un usage specifique pour l'objet à partir de l'usage générique
        INSERT INTO objets_usage_bm (objusebm_parent_cod, objusebm_gobj_cod, objusebm_obj_cod, objusebm_tbonus_cod, objusebm_bonus_valeur, objusebm_bonus_nb_tours, objusebm_cout, objusebm_malchance, objusebm_nb_utilisation_max, objusebm_nb_utilisation, objusebm_bonus_distance, objusebm_bonus_aggressif, objusebm_bonus_soutien, objusebm_bonus_soi_meme, objusebm_bonus_monstre, objusebm_bonus_joueur, objusebm_bonus_case, objusebm_bonus_mode, objusebm_bonus_familier, objusebm_vide_detruit)
            SELECT  objusebm_cod, null, v_obj_cod, objusebm_tbonus_cod, objusebm_bonus_valeur, objusebm_bonus_nb_tours, objusebm_cout, objusebm_malchance, objusebm_nb_utilisation_max, objusebm_nb_utilisation, objusebm_bonus_distance, objusebm_bonus_aggressif, objusebm_bonus_soutien, objusebm_bonus_soi_meme, objusebm_bonus_monstre, objusebm_bonus_joueur, objusebm_bonus_case, objusebm_bonus_mode, objusebm_bonus_familier, objusebm_vide_detruit
            FROM objets_usage_bm WHERE objusebm_cod = v_objusebm_cod limit 1
            RETURNING objusebm_cod INTO v_temp;
        if not found then
            return -4;      -- erreur lors de la création de l'usage spécifique
        end if;

        -- mise à jour du code de l'usage spécifique pour l'objet
        v_objusebm_cod := v_temp;
    end if;

    -- récupération du nombre d'utilisation de l'objet
    select objusebm_nb_utilisation_max, objusebm_nb_utilisation, objusebm_vide_detruit
        into v_nb_usage_max, v_nb_usage, v_vide_detruit
        from objets_usage_bm where objusebm_cod = v_objusebm_cod limit 1;
    if not found then
        return -5;      -- erreur lors de la récupération du nombre d'utilisation
    end if;

    -- controle sur le nombre d'utilisation maximum de l'objet
    if v_nb_usage_max > 0 and v_nb_usage >= v_nb_usage_max then
        -- l'usage est pas illimité et le nombre d'utilisation maximum est atteint, destruction de l'objet puisque vide
        if v_vide_detruit = 'O' then
            perform f_del_objet(v_obj_cod);
            return -7;      -- nombre d'utilisation maximum atteint et l'objet est détruit
        end if;
        return -6;      -- nombre d'utilisation maximum atteint
    end if;

    -- si tout est OK, on retourne le code de l'usage spécifique pour l'objet
    return v_objusebm_cod;

end;	$_$;


ALTER FUNCTION public.objet_valide_usage(integer, integer, integer) OWNER TO delain;