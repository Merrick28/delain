--
-- Name: compte_inactif(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION compte_inactif(integer) RETURNS text
LANGUAGE plpgsql
AS $$/********************************************/
/* Fonction joueur_inactif                  */
/*   transforme les joueurs inactifs en     */
/*   monstres                               */
/********************************************/
/* On passe en paramètre un entier          */
/********************************************/
/* le code retour est un texte              */
/********************************************/
declare
  code_retour text;
  ligne record;
  compt integer;
  delai interval;
  texte_admin text;
  guilde_morte integer;
  groupe_morte integer;
  perso_elu integer;	--Nouvel administrateur de guilde
  v_perso_nom text;
  num integer;				--Variable de calcul
  nombre integer;		--Variable de calcul
  corps_message text;
  titre_message text;
  code_message integer;
  liste_membres record;
  code_cible integer;

begin
  compt := 0;
  code_retour := '';
  delai := trim(to_char(getparm_n(14),'9999'))||' days';
  for ligne in select * from perso,perso_position,positions
  where perso_type_perso = 1
        and perso_actif = 'O'
        and perso_der_connex + delai < now()
        and perso_cod = ppos_perso_cod
        and ppos_pos_cod = pos_cod
        and perso_pnj != 1
        and pos_etage != 0 loop
    update perso set perso_type_perso = 2,perso_dirige_admin = 'N', perso_sta_combat = 'N',perso_sta_hors_combat = 'N',perso_tangible = 'O' where perso_cod = ligne.perso_cod;
    code_cible := ligne.perso_cod;
    delete from perso_compte where pcompt_perso_cod = ligne.perso_cod;
    /*Bloc pour modification d'administrateur de guilde*/
    select into nombre,guilde_morte count(guilde_cod),rguilde_guilde_cod from guilde,guilde_perso,guilde_rang,perso
    where  pguilde_guilde_cod = guilde_cod
           and rguilde_guilde_cod = guilde_cod
           and rguilde_rang_cod = pguilde_rang_cod
           and pguilde_valide = 'O'
           and pguilde_perso_cod = perso_cod
           and perso_actif = 'O'
           and rguilde_admin = 'O'
           and pguilde_perso_cod = ligne.perso_cod
    group by rguilde_guilde_cod;
    if nombre = 0 then
      --on sélectionne un autre membre au hasard si il existe
      select into perso_elu,v_perso_nom,num pguilde_perso_cod,perso_nom,lancer_des(1,1000) from guilde,guilde_perso,guilde_rang,perso
      where  pguilde_guilde_cod = guilde_cod
             and rguilde_guilde_cod = guilde_cod
             and rguilde_rang_cod = pguilde_rang_cod
             and pguilde_valide = 'O'
             and guilde_cod = guilde_morte
             and pguilde_perso_cod = perso_cod
      order by num limit 1;
      if found then
        -- on le met en admin
        update guilde_perso
        set pguilde_rang_cod = 0
        where pguilde_perso_cod = perso_elu;
        -- on prévient quand même par acquis de conscience tous les membres de la guilde
        code_message := nextval('seq_msg_cod');
        titre_message := 'Monstrification';
        corps_message := 'L''ancien dernier administrateur de votre guilde a été monstrifié<br>';
        corps_message := corps_message||v_perso_nom||' a été arbitrairement nommé administrateur à sa place.';
        insert into messages
        (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
        values
          (code_message,now(),titre_message,corps_message,now());
        insert into messages_exp
        (emsg_msg_cod,emsg_perso_cod)
        values
          (code_message,code_cible);
        for liste_membres in
        select perso_cod
        from guilde_perso,perso
        where pguilde_guilde_cod = v_guilde
              and pguilde_perso_cod = perso_cod
              and pguilde_valide = 'O'
              and perso_actif = 'O'
              and perso_type_perso = 1 loop
          insert into messages_dest
          (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
          values
            (code_message,liste_membres.perso_cod,'N','N');
        end loop;
      end if;
    end if;
    /*Fin Bloc pour modification d'administrateur de guilde*/

    /*Bloc pour monstrification du membre d'une guilde*/
    select into nombre count(pguilde_perso_cod) from guilde_perso,perso
    where pguilde_valide = 'O'
          and pguilde_perso_cod = perso_cod
          and perso_actif = 'O'
          and pguilde_perso_cod = ligne.perso_cod;
    if nombre != 0 then
      --on essaye de prévenir le chef de guilde si il existe
      code_message := nextval('seq_msg_cod');
      titre_message := 'Monstrification de '||v_perso_nom;
      corps_message := 'Un des membres de votre guilde s''est monstrifié. Il s''agit de <br>'||v_perso_nom;
      insert into messages
      (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
      values
        (code_message,now(),titre_message,corps_message,now());
      insert into messages_exp
      (emsg_msg_cod,emsg_perso_cod)
      values
        (code_message,code_cible);
      for liste_membres in
      select perso_cod
      from guilde,guilde_perso,guilde_rang,perso
      where pguilde_guilde_cod = v_guilde
            and pguilde_perso_cod = perso_cod
            and pguilde_valide = 'O'
            and perso_actif = 'O'
            and perso_type_perso = 1
            and pguilde_guilde_cod = guilde_cod
            and rguilde_guilde_cod = guilde_cod
            and rguilde_rang_cod = pguilde_rang_cod
            and rguilde_admin = 'O'	loop
        insert into messages_dest
        (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
        values
          (code_message,liste_membres.perso_cod,'N','N');
      end loop;
    end if;
    /*Fin Bloc pour perso d'une guilde*/

    /*Bloc pour modification de chef de coterie*/
    select into nombre,groupe_morte count(groupe_cod),groupe_cod from groupe,groupe_perso,perso
    where pgroupe_groupe_cod = groupe_cod
          and pgroupe_statut = 1
          and pgroupe_perso_cod = perso_cod
          and perso_actif = 'O'
          and pgroupe_perso_cod = ligne.perso_cod
    group by groupe_cod;
    if nombre = 0 then
      --on sélectionne un autre membre au hasard si il existe
      select into perso_elu,v_perso_nom,num pgroupe_perso_cod,perso_nom,lancer_des(1,1000) from groupe,groupe_perso,perso
      where  pgroupe_groupe_cod = groupe_cod
             and pgroupe_valide = 'O'
             and groupe_cod = groupe_morte
             and pgroupe_perso_cod = perso_cod
      order by num limit 1;
      if found then
        -- on le met en admin y compris dans la table groupe
        update groupe_perso
        set pgroupe_chef = 1
        where pgroupe_perso_cod = perso_elu
              and pgroupe_groupe_cod = groupe_morte;
        update groupe
        set groupe_chef = perso_elu
        where groupe_cod = groupe_morte;
        -- on prévient quand même par acquis de conscience tous les membres de la groupe
        code_message := nextval('seq_msg_cod');
        titre_message := 'Monstrification';
        corps_message := 'L''ancien dernier chef de votre coterie a été monstrifié<br>';
        corps_message := corps_message||v_perso_nom||' a été arbitrairement nommé chef à sa place.';
        insert into messages
        (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
        values
          (code_message,now(),titre_message,corps_message,now());
        insert into messages_exp
        (emsg_msg_cod,emsg_perso_cod)
        values
          (code_message,code_cible);
        for liste_membres in
        select perso_cod
        from groupe_perso,perso
        where pgroupe_groupe_cod = v_groupe
              and pgroupe_perso_cod = perso_cod
              and pgroupe_valide = 'O'
              and perso_actif = 'O'
              and perso_type_perso = 1 loop
          insert into messages_dest
          (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
          values
            (code_message,liste_membres.perso_cod,'N','N');
        end loop;
      end if;
    end if;
    /*Fin Bloc pour modification de chef de coterie*/

    /*Bloc pour monstrification d'un membre d'une coterie*/
    select into nombre count(groupe_cod) from groupe,groupe_perso,perso
    where  pgroupe_groupe_cod = groupe_cod
           and pgroupe_perso_cod = perso_cod
           and perso_actif = 'O'
           and pgroupe_perso_cod = ligne.perso_cod
    group by groupe_cod;
    if nombre != 0 then
      --Le membre appartient à une coterie et on va prévenir le chef
      code_message := nextval('seq_msg_cod');
      titre_message := 'Monstrification de '||v_perso_nom;
      corps_message := 'L''un des membres de votre coterie a été monstrifié. Il s''agit de '||v_perso_nom;
      insert into messages
      (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
      values
        (code_message,now(),titre_message,corps_message,now());
      insert into messages_exp
      (emsg_msg_cod,emsg_perso_cod)
      values
        (code_message,code_cible);
      for liste_membres in
      select perso_cod
      from groupe_perso,perso
      where pgroupe_groupe_cod = v_groupe
            and pgroupe_perso_cod = perso_cod
            and pgroupe_valide = 'O'
            and perso_actif = 'O'
            and perso_actif = 'O'
            and pgroupe_statut = 1
            and perso_type_perso = 1 loop
        insert into messages_dest
        (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
        values
          (code_message,liste_membres.perso_cod,'N','N');
      end loop;
    end if;
    /*Fin Bloc pour monstrification d'un membre d'une coterie*/


    delete from messages_dest where dmsg_perso_cod = ligne.perso_cod;
    delete from groupe_perso where pgroupe_perso_cod = ligne.perso_cod;
    compt := compt + 1;
  end loop;


  code_retour := trim(to_char(compt,'999999'))||' persos transformés en monstres';
  compt := 0;
  code_retour := code_retour||trim(to_char(compt,'999999'))||' persos supprimés.';
  return code_retour;

end;$$;


ALTER FUNCTION public.compte_inactif(integer) OWNER TO delain;
