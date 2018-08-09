CREATE OR REPLACE FUNCTION public.ajouter_comp_mon(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ajouter_comp_mon : Ajouter les competences           */
/* à partir de monstre_generique_comp                            */
/*                                                               */
/* On passe en paramètre le perso_cod et le gmon_cod             */
/* Le code sortie est :                                          */
/*    0 = Tout s est bien passé                                  */
/*    1 = la comp existe déjà                                    */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
  personnage alias for $1;
  gmon alias for $2;
  code_erreur integer;
  ligne_competences record;
  v_temp integer;

begin
  code_erreur := 0; -- par défaut, tout se passe bien

  for ligne_competences in select * from  monstre_generique_comp where gmoncomp_gmon_cod = gmon loop
    select into v_temp pcomp_modificateur from perso_competences
    where pcomp_perso_cod = personnage
          and pcomp_pcomp_cod = ligne_competences.gmoncomp_comp_cod;
    if not found then
      insert into perso_competences values
        (nextval('seq_pcomp'),personnage,ligne_competences.gmoncomp_comp_cod,ligne_competences.gmoncomp_valeur);
    else
      code_erreur := 1;
    end if;
  end loop;

  return code_erreur;
end;
$function$

