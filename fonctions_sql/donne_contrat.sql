CREATE OR REPLACE FUNCTION public.donne_contrat(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* donne_contrat : permet de savoir si on donne*/
/* un contrat à la cible                       */
/*  $1 = perso_cod                             */
/*  code_retour = 'N' -> pas de contrat        */
/*  code_retour = 'O' -> contrat               */
/***********************************************/
declare 
	code_retour text;
	v_perso alias for $1;
        dernier_resultat text;
        tirage_des integer;
        chance_contrat integer;
        v_pp integer;
       	
begin
	code_retour := 'O';
        -- on regarde si le test n a pas déjà été fait pour la journée présente
        
        select into dernier_resultat ptctr_resultat from perso_tentative_contrat
        where ptctr_perso_cod = v_perso
        and ptctr_date = current_date;

        if found then            
             return dernier_resultat;
        end if;

        -- premiere tentative de la journée
        
        -- calcul de la chance
        select into v_pp perso_prestige from perso
        where perso_cod = v_perso;

        chance_contrat := min((20 + v_pp), 50);
        tirage_des := lancer_des(1,100);
        
        if (tirage_des > chance_contrat) then
            code_retour = 'N';
        end if;


        -- on insère dans la table des tentatives
        insert into perso_tentative_contrat (ptctr_perso_cod, ptctr_date, ptctr_resultat) values (v_perso, current_date, code_retour);
         
	return code_retour;
end;$function$

