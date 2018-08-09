CREATE OR REPLACE FUNCTION public.accepte_invitation(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* accepte_invitation                          */
/*  $1 = perso_cod                             */
/*  $2 = groupe_cod                            */
/***********************************************/
declare
  code_retour text;
  v_perso alias for $1;
  v_groupe_cod alias for $2;
  v_chef integer;
  v_num_mes integer;
  v_texte_mes text;
  v_nom_invite text;
  v_nom_groupe text;
  invitation integer;
begin
  select into v_num_mes
    pgroupe_perso_cod
  from groupe_perso
  where pgroupe_perso_cod = v_perso
        and pgroupe_statut = 1;
  if found then
    return ' Erreur ! Vous faites déjà partie d’une coterie.';
  end if;
  /*Controle de la bonne réception d’une invitation */
  select into invitation pgroupe_statut from groupe_perso
  where pgroupe_perso_cod = v_perso
        and pgroupe_groupe_cod = v_groupe_cod;
  if not found then
    return 'Erreur ! Vous n’avez pas été invité à faire partie de cette coterie.';
  end if;
  if invitation = 1 then
    return 'Erreur ! Vous n’avez pas été invité à faire partie de cette coterie.';
  end if;
  -- on commence par valider l'invitation
  update groupe_perso
  set pgroupe_statut = 1
  where pgroupe_perso_cod = v_perso
        and pgroupe_groupe_cod = v_groupe_cod;

  -- on supprime les appartenances suspendues (suite à une mort)
  delete from groupe_perso where pgroupe_perso_cod = v_perso and pgroupe_statut = 2;

  -- on prépare un message
  select into v_chef groupe_chef
  from groupe
  where groupe_cod = v_groupe_cod;
  select into 	v_nom_invite
    perso_nom from perso
  where perso_cod = v_perso;

  v_num_mes := nextval('seq_msg_cod');

  insert into messages
  (msg_cod,msg_titre,msg_corps)
  values
    (v_num_mes,'Invitation acceptée à une coterie',v_nom_invite||' a accepté l’invitation à votre coterie.');
  insert into messages_exp (emsg_msg_cod,emsg_perso_cod)
  values (v_num_mes,v_perso);
  insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
  values (v_num_mes,v_chef,'N','N');
  return 'Vous faites maintenant partie d’une coterie.';
end;$function$

