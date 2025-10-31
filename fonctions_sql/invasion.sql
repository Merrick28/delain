
--
-- Name: invasion(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.invasion(integer) RETURNS text
LANGUAGE plpgsql STRICT
AS $_$/*****************************************************************/
/* function invasion: Envahit les étages d''un nombre            */
/*   prédéfini de monstres du type sélectionné (cases / 50)      */
/*   IMPORTANT: Les monstres doivent être calibrés pour les extérieurs */
/*   Ils seront ensuite automatiquement ajustés pour les étages plus bas */
/*   Voir aussi: "cree_monstre_invasion"                         */
/*****************************************************************/
declare
v_gmon_cod alias for $1; -- Le type de monstre à rajouter
code_retour text;
ligne record;

v_nb_monstres integer;
i integer;
v_monstre_temp integer;

begin
code_retour := '';
for ligne in select etage_numero, count(pos_cod) as nombre_positions from etage, positions
where etage_reference != -100 and pos_etage = etage_numero group by etage_numero loop
-- 1 monstre par vue de 3 (49 cases).
-- v_nb_monstres := ligne.nombre_positions / 50;
v_nb_monstres := ligne.nombre_positions / 800;
code_retour := code_retour || ligne.etage_numero::text || E'\r\n';
for i in 1..v_nb_monstres loop
-- Création du monstre
v_monstre_temp := cree_monstre_invasion(v_gmon_cod,ligne.etage_numero);
update perso set perso_type_perso = 1, perso_pnj = 1, perso_actif = 'O',
perso_tangible = 'N', perso_nb_tour_intangible = 9999,
perso_quete = 'quete_groquik.php'
where perso_cod = v_monstre_temp;
code_retour := code_retour || 'Un groquik créé' || E'\r\n';
-- code_retour := code_retour || cree_monstre_invasion(v_gmon_cod,ligne.etage_numero)::text || E')\r\n';
end loop;
end loop;

return code_retour;
end;
$_$;


ALTER FUNCTION public.invasion(integer) OWNER TO delain;

--
-- Name: invasion(integer, integer, integer, boolean); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.invasion(integer, integer, integer, boolean) RETURNS text
LANGUAGE plpgsql
AS $_$/***********************************************************************/
/* function invasion: Invasion des étages par un même type de monstre. */
/*   Paramètres :                                                      */
/*   $1 : gmon_cod : le type de monstre à créer                        */
/*   $2 : etage_numero : le numéro de l’étage où créer les monstres    */
/*   $3 : eparpillement : le nombre N tel qu’y ait 1 monstre pour      */
/*           N cases non mur dans l’étage.                             */
/*   $4 : adapter : si TRUE, les caracs du monstre sont adaptées à     */
/*           l’étage. Attention : cela présuppose un monstre taillé    */
/*           pour les extérieurs !                                     */
/***********************************************************************/
declare
v_gmon_cod alias for $1;     -- Le type de monstre à rajouter
v_etage alias for $2;        -- Le numéro de l’étage où créer les monstres
v_eparpillement alias for $3;-- Le nombre de cases pour un monstre
v_adapter alias for $4;      -- Le niveau du monstre doit-il être adapté ou non ?
code_retour text;

nombre integer;
v_nb_monstres integer;
i integer;
v_monstre_temp integer;
ligne record;

niveau_moyen integer;
v_amelioration_degats integer;
v_amelioration_degats_distance integer;
v_amelioration_armure integer;
v_niveau integer;
v_pv integer;
v_temps_tour integer;
v_modif_carac decimal;
v_con integer;

begin
code_retour := '';
select into nombre count(pos_cod) from etage
inner join positions on pos_etage = etage_numero
left outer join murs on mur_pos_cod = pos_cod
where etage_numero = v_etage and mur_pos_cod is null
group by (etage_numero);

CREATE TEMP TABLE monstres_crees (
mc_perso_cod integer
);

v_nb_monstres := max(nombre / v_eparpillement, 1);

for i in 1..v_nb_monstres loop
-- Création du monstre
v_monstre_temp := cree_monstre_hasard(v_gmon_cod, v_etage);

insert into monstres_crees(mc_perso_cod) values(v_monstre_temp);
end loop;

if (v_adapter) then
/**********************************************/
/* Etape 2 : on retouche les monstres         */
/**********************************************/
select into niveau_moyen sum(perso_niveau)/count(perso_niveau)  from perso, perso_position, positions
where perso_cod = ppos_perso_cod and ppos_pos_cod = pos_cod
and pos_etage = (select etage_reference from etage where etage_numero = v_etage)
and perso_type_perso = 1 and perso_actif = 'O' and perso_pnj != 1;

-- Armure : Niveau moyen * armure / 6
-- Niveau : Niveau_moyen * niveau / 6 (armure = niveau / 2)
-- PX : 0
-- Dégâts : Pas besoin en 2011.
-- Caracs/Comps : Pas besoin non plus
-- pv_max :
-- Temps de tour : dlt - 5*Niveau_moyen
-- caracs : carac * niveau_moyen / 30.

v_modif_carac := niveau_moyen / 30::numeric + 1;

select into v_amelioration_degats, v_amelioration_degats_distance, v_amelioration_armure, v_niveau, v_temps_tour, v_con
gmon_amelioration_degats * MAX(1, niveau_moyen / 6),
gmon_amel_deg_dist * MAX(1, niveau_moyen / 6),
gmon_amelioration_armure * MAX(1, niveau_moyen / 6),
gmon_niveau * MAX(1, niveau_moyen / 6),
gmon_temps_tour - 5 * niveau_moyen,
(gmon_con * v_modif_carac)::integer
from monstre_generique where gmon_cod = v_gmon_cod;

v_pv := v_con * 2 + v_niveau - 1 + lancer_des(v_niveau - 1, cast((v_con / 4) as integer));

update perso set
perso_amelioration_degats = v_amelioration_degats,
perso_amel_deg_dex = v_amelioration_degats_distance,
perso_amelioration_armure = v_amelioration_armure,
perso_niveau = v_niveau,
perso_pv_max = v_pv,
perso_pv = v_pv,
perso_temps_tour = v_temps_tour,
perso_con = (perso_con * v_modif_carac)::integer,
perso_dex = (perso_dex * v_modif_carac)::integer,
perso_for = (perso_for * v_modif_carac)::integer,
perso_int = (perso_int * v_modif_carac)::integer
where perso_cod IN (SELECT mc_perso_cod FROM monstres_crees);

update perso
set perso_px = limite_niveau_actuel(perso_cod)
where perso_cod IN (SELECT mc_perso_cod FROM monstres_crees);

update perso_competences
set pcomp_modificateur = pcomp_modificateur + v_niveau
where pcomp_perso_cod IN (SELECT mc_perso_cod FROM monstres_crees);
end if;

for ligne in select perso_nom || ' correctement créé en ' || pos_x::text || ', ' || pos_y::text as nom from monstres_crees
inner join perso on perso_cod = mc_perso_cod
inner join perso_position on ppos_perso_cod = perso_cod
inner join positions on pos_cod = ppos_pos_cod
loop
code_retour := code_retour || ligne.nom || E'<br />\r\n';
end loop;

DROP TABLE monstres_crees;

return code_retour;
end;		$_$;


ALTER FUNCTION public.invasion(integer, integer, integer, boolean) OWNER TO delain;

--
-- Name: FUNCTION invasion(integer, integer, integer, boolean); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.invasion(integer, integer, integer, boolean) IS 'Lance une invasion sur un étage passé en paramètre, suivant certains critères définis.';

