<?php
/**
 * Ce script gère le fonctionnement des lieux du type "relais poste":
 *
 * L’envoyeur:
 * 1) Il se rend sur un relais des postes (il y en aurait 2 ou 3 relais par étage).
 * 2) Il dépose son colis à l’attention d’un individu (possibilité de mettre un prix comme pour une transaction)
 * 3) Il paye les frais de port (proportionnel au poids du colis)
 *
 * Le réceptionneur:
 * 4) Il se rend sur un (autres) relais des postes
 * 5) Il paye le prix de la transaction (si payant) et récupère le colis
 *
 * Les contraintes:
 * les colis ne peuvent contenir que de l'équipement (casque, armure, etc...): $objets_poste->getObjetsDeposableRelaisPoste()
 * un colis ne peut contenir qu'un seul élément
 * On ne peut pas envoyer de colis à un membre de sa propre triplette
 * il ne peut y avoir qu'un colis à destination d'un même aventurier
 * le colis ne peut être retiré que par son destinataire.
 * il y a un délai de 5 jours avant de pouvoir retirer un colis: $objets_poste->estLivrable()
 * il y a un délai de 2 mois pour retirer le colis avant confiscation par la poste: $objets_poste->estConfiscable()
 * les colis ne peuvent pas être envoyés aux 4ème persos, ni aux familiers
 * les 4ème persos et les familiers ne peuvent envoyer des colis
 */


include_once "../includes/constantes.php";
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

//$perso = new perso;
//$perso      = $verif_connexion->perso;
//echo "<pre>"; print_r($perso); echo "</pre>";
//$perso->perso_tuteur = false;
//$perso->stocke();
//die();

//-----------------------------------------------------------------------
// on regarde si le joueur est bien sur le lieu souhaité ----------------

$type_lieu = 39;
$nom_lieu = 'un relais de poste';

define('APPEL', 1);
include "blocks/_test_lieu.php";


//===========================================================================================
// details du perso
$perso         = new perso;
$perso         = $verif_connexion->perso;
$perso_pos_cod = $perso->get_position()['pos']->pos_cod;

//===========================================================================================
// ----- Traitement du lieu ---------------------------------------------
if ($erreur != 0)
{

    //cas d'un erreur de lieu (dans la cas par exemple d'un perso qui aurait changer de lieu avec une autre page web)
    $template = $twig->load('lieu_anomalie.twig');
    echo $template->render(array_merge($options_twig_defaut, array('LIEU' => "un relais poste")));

} else if ($perso->is_4eme_perso() || $perso->is_fam_4eme_perso())
{

    $template = $twig->load('lieu_relais_poste.twig');
    echo $template->render(array_merge($options_twig_defaut, array('INTERDIT' => "quatrième personnage")));

} else if ($perso->is_monstre())
{

    $template = $twig->load('lieu_relais_poste.twig');
    echo $template->render(array_merge($options_twig_defaut, array('INTERDIT' => "monstre")));

} else
{
    //===========================================================================================
    // Gestion du coffre avec les relais
    $cc = new compte_coffre();
    $cc->loadBy_ccompt_compt_cod($compt_cod);

    //===========================================================================================
    // details de l'objet poste
    $objets_poste = new objets_poste;
    $options_twig = array(); // on affiche rien par defaut!

    //===========================================================================================
    $zone_couverture = $objets_poste->getTexteZoneCouverture($perso_pos_cod);
    $options_twig_colis = array(
        "perso_po" => $perso->perso_po,
        "zone_couverture" => $zone_couverture);    //la fortune dont on dispose

    // On commence par regarder s'il y a déjà des objets envoyés --------------------------------
    $objets_poste_emet = $objets_poste->getBy_opost_emet_perso_cod($perso_cod);
    if ($objets_poste_emet)
    {
        $objets_emet = array();
        $perso_desc = new perso;
        $objet_desc = new objets;
        foreach ($objets_poste_emet as $k => $objet)
        {
            if (!$objet->Confisque($perso_cod))
            {
                // Si l'objet n'a pas été confisqué! (à cause d'un retrait trop long)
                $objet_desc->charge($objet->opost_obj_cod);
                $perso_desc->charge($objet->opost_dest_perso_cod);
                $objets_emet[] = array(
                    'opost_cod' => $objet->opost_cod,
                    'obj_cod' => $objet->opost_obj_cod,
                    'date_poste' => $objet->opost_date_poste,
                    'est_livrable' => $objet->estLivrable($perso_pos_cod),
                    'est_date_livrable' => $objet->estDateLivrable(),
                    'est_lieu_livrable' => $objet->estLieuLivrable($perso_pos_cod),
                    'zone_livraison' => $objet->getTexteZoneCouverture($objet->opost_emet_pos_cod, true),
                    'date_livraison' => $objet->getDateLivraison(),
                    'date_confiscation' => $objet->getDateConfiscation(),
                    'perso_cod_dest' => $objet->opost_dest_perso_cod,
                    'perso_nom_dest' => $perso_desc->perso_nom,
                    'obj_nom' => $objet_desc->obj_nom,
                    'obj_poids' => $objet_desc->obj_poids,
                    'prix_demande' => $objet->opost_prix_demande
                );
            }
        }
        $options_twig_colis['objets_poste_emet'] = $objets_emet;
        unset($perso_desc);
        unset($objet_desc);
    }
    //echo "<pre>"; print_r($objets_emet); echo "</pre>";

    //===========================================================================================
    // On regarde ensuite s'il y a des objets pour nous -----------------------------------------
    $objets_poste_dest = $objets_poste->getBy_opost_dest_perso_cod($perso_cod);
    if ($objets_poste_dest)
    {
        $objets_dest = array();
        $perso_desc = new perso;
        $objet_desc = new objets;
        foreach ($objets_poste_dest as $k => $objet)
        {
            if (!$objet->Confisque($perso_cod))
            {
                // Si l'objet n'a pas été confisqué! (à cause d'un retrait trop long)
                $objet_desc->charge($objet->opost_obj_cod);
                $perso_desc->charge($objet->opost_emet_perso_cod);
                $objets_dest[] = array(
                    'opost_cod' => $objet->opost_cod,
                    'obj_cod' => $objet->opost_obj_cod,
                    'date_poste' => $objet->opost_date_poste,
                    'est_livrable' => $objet->estLivrable($perso_pos_cod),
                    'est_date_livrable' => $objet->estDateLivrable(),
                    'est_lieu_livrable' => $objet->estLieuLivrable($perso_pos_cod),
                    'zone_livraison' => $objet->getTexteZoneCouverture($objet->opost_emet_pos_cod, true),
                    'date_livraison' => $objet->getDateLivraison(),
                    'date_confiscation' => $objet->getDateConfiscation(),
                    'perso_cod_emet' => $objet->opost_emet_perso_cod,
                    'perso_nom_emet' => $perso_desc->perso_nom,
                    'obj_nom' => $objet_desc->obj_nom,
                    'obj_poids' => $objet_desc->obj_poids,
                    'prix_demande' => $objet->opost_prix_demande
                );
            }

        }
        $options_twig_colis['objets_poste_dest'] = $objets_dest;
        unset($perso_desc);
        unset($objet_desc);
    }
    //echo "<pre>"; print_r($objets_poste_dest); echo "</pre>";

    //===========================================================================================
    // Initialisation des variables
    $retrait = array();            // Pour l'instant il n'y a qu'un seul objet dans le colis, mais on prevoit pour plusieurs
    $poids_retrait = 0;
    $prix_retrait = 0;

    // Récupération des objets de colis
    if (isset($_POST["r_obj"]) && $objets_poste_dest)
    {
        $obj_cod = $_POST["r_obj"];    // liste des objets à retirer!
        foreach ($objets_dest as $k => $objet)
        {
            if (($objet['obj_cod'] == $obj_cod[$objet['obj_cod']]) && $objet['est_livrable'])
            {
                $poids_retrait += 1 * $objet["obj_poids"];
                $prix_retrait += 1 * $objet["prix_demande"];
                $retrait[] = $objet;        // objet a retirer de la poste
            }
        }
    }
    //echo "<pre>"; print_r($retrait); echo "</pre>";

    //===========================================================================================
    // Objets "postable" dans l'inventaire du perso ---------------------------------------------
    $objets = $objets_poste->getObjetsDeposableRelaisPoste($perso_cod);

    // Initialisation des variables
    $colis = array();            // Pour l'instant il n'y a qu'un seul objet dans le colis, mais on prevoit pour plusieurs
    $poids_colis = 0;
    $prix_demande = 0;

    // Préparation du colis si le/les objets ont été selectionnés
    if (isset($_POST["obj"]))
    {
        $obj_cod = $_POST["obj"];    // objet envoyé!
        foreach ($objets as $k => $objet)
        {
            if ($objet["obj_cod"] == $obj_cod)
            {
                $poids_colis += 1 * $objet["obj_poids"];
                $objet["prix_demande"] = floor(abs(1 * $_POST["prix"][$obj_cod]));    // en valeur absolue (au cas ou des malins mettraient un montant négatif), et pas de 1/2 brousoufs
                $prix_demande += $objet["prix_demande"];
                $colis[] = $objet;        // objet a mettre dans le colis
            }
        }
        $frais_port = $objets_poste->getFraisDePort($poids_colis);
    }

    // recherche de l'adresse de livraison
    $destinataire = isset($_POST["destinataire"]) ? $_POST["destinataire"] : "";
    $destinataire_list = array();
    if (isset($_POST["destinataire"]))
    {
        $perso_dest = new perso;
        $destinataire_list = $perso_dest->getPersosByNameLike($_POST["destinataire"]); // si l'on veut aussi les familiers:, array(1,3));

        // S'il n'y a qu'un seul destinataire
        if (count($destinataire_list) == 1)
        {
            //on cherche son proprietaire
            $perso_compte = new perso_compte;
            $perso_compte = $perso_compte->getBy_pcompt_perso_cod($destinataire_list[0]->perso_cod)[0];

            // On va supprimer d'eventuelle vieux colis que le perso ciblé n'aurait pas receptionné à temps!
            $perso_attend_colis = $objets_poste->getBy_opost_dest_perso_cod($destinataire_list[0]->perso_cod);
            if ($perso_attend_colis)
            {
                foreach ($perso_attend_colis as $k => $objet)
                {
                        $objet->Confisque($destinataire_list[0]->perso_cod) ;
                }
            }
            // Recharger après nettoyage
            $perso_attend_colis = $objets_poste->getBy_opost_dest_perso_cod($destinataire_list[0]->perso_cod);

            //on cherche aussi s'il n'y a pas déjà des colis pour lui
            $perso_attend_colis = $objets_poste->getBy_opost_dest_perso_cod($destinataire_list[0]->perso_cod);
        }
    }

    // Debugage
    //print_r($_POST);
    //echo "<pre>"; print_r($objets); echo "</pre>";
    //echo "<pre>"; print_r($colis); echo "</pre>";
    //echo "<pre>"; print_r($retrait); echo "</pre>";
    //echo "<pre>"; print_r($destinataire_list); echo "</pre>";

    //------------------------------------------------------------------------------------------------------------------------------------
    // Mecanique de saisie des formulaires
    //------------------------------------------------------------------------------------------------------------------------------------	

    //--------------------------------------- payer, oter l'objet de l'inventaire et envoyer le colis ------------------------------------
    if (($_POST["action"] == "retrait1") && ($_POST["next"] != ""))
    {
        // revérifier le retrait complètement, faire le paiement et récupérer
        if ((count($retrait) > 0)                                    // il doit y avoir au moins un objet a retirer
            && ($perso->perso_po >= $prix_retrait)                        // et assez de bz pour payer le prix demandé
        )
        {
            //********************************************************************************************************************************
            // boucle sur tous les objets a récupérer, faire des paiement/récuperation unitaire .
            foreach ($retrait as $k => $objet)
            {
                //chargement de l'obet à la poste
                if ($objets_poste->charge($objet['opost_cod']))
                {

                    if ($objets_poste->opost_prix_demande > 0)    //seulement s'il y avait un prix!
                    {
                        //débiter les bz du perso
                        $perso->perso_po = $perso->perso_po - $objets_poste->opost_prix_demande;
                        $perso->stocke();

                        //crediter sur le compte en banque de l'emetteur du colis avec le prix demandé
                        $perso_banque_emet = new perso_banque;
                        $perso_banque_emet = $perso_banque_emet->getBy_pbank_perso_cod($objets_poste->opost_emet_perso_cod)[0];
                        if (!$perso_banque_emet)
                        {
                            // l'emeteur n'a pas de compte bancaire on va lui en créer un
                            $perso_banque_emet = new perso_banque;
                            $perso_banque_emet->pbank_perso_cod = $objets_poste->opost_emet_perso_cod;
                            $perso_banque_emet->pbank_or = $objets_poste->opost_prix_demande;
                            $perso_banque_emet->stocke(true);
                        } else
                        {
                            // le compte en banque existe on augmente le solde
                            $perso_banque_emet->pbank_or += $objets_poste->opost_prix_demande;
                            $perso_banque_emet->stocke();
                        }
                    }

                    // mettre l'objet dans l'inventaire du perso et supprimer de la poste
                    $objets_poste->retraitObjetRelaisPoste();
                }
            }

            // Retirer l'objet de l'inventaire du perso (traite l'événement)
            //********************************************************************************************************************************

            $options_twig = array(
                'titre' => "Le colis a bien été réceptioné:",
                'action' => "retrait2",
                'objets' => $retrait,
                'poids' => $poids_retrait,
                'prix_retrait' => $prix_retrait
            );
        } else
        {
            $options_twig = array(
                'titre' => "Vous souhaitez réceptionner:",
                'action' => "retrait1",
                'objets' => $retrait,
                'poids' => $poids_retrait,
                'prix_retrait' => $prix_retrait,
                'message' => "Une erreur c'est produite, nous ne pouvons pas récupérer votre colis. Veuillez nous excuser pour cet impondérable!"
            );
        }
    } else if (($_POST["action"] == "retrait0") && ($_POST["next"] != ""))
    {
        if (count($retrait) == 0)
        {
            $options_twig = array(
                'titre' => "Sélectionner l'équipement à envoyer:",
                'action' => "poste1",
                'objets' => $objets,
                'message_retrait' => "Selectionner au moins un objet à réceptionner!"
            );
        } else if ($perso->perso_po < $prix_retrait)
        {
            $options_twig = array(
                'titre' => "Sélectionner l'équipement à envoyer:",
                'action' => "poste1",
                'objets' => $objets,
                'message_retrait' => "Vous n'avez pas assez pour payer les $prix_retrait brouzoufs demandés!"
            );
        } else
        {
            $options_twig = array(
                'titre' => "Vous souhaitez réceptionner:",
                'action' => "retrait1",
                'objets' => $retrait,
                'poids' => $poids_retrait,
                'prix_retrait' => $prix_retrait
            );
        }
    } else if (($_POST["action"] == "poste4") && ($_POST["next"] != ""))
    {
        // revérifier la demande complète, faire le paiement et envoyer
        if ((count($destinataire_list) == 1)                            // il doit y avoir un seul destinataire
            && ($destinataire_list[0]->perso_cod != $perso_cod)            // mais pas à soit même
            && ($perso_compte->pcompt_compt_cod != $compt_cod)            // ni à un autre perso de la triplette/quadruplette
            && (count($colis) > 0)                                        // il y a moins un objet dans le colis
            && (!$perso_attend_colis)                                    // le destinataire n'attend pas déjà un autre colis
            && ($perso->perso_po >= $frais_port)                            // on a assez de bz pour payer les frais de port
        )
        {
            //********************************************************************************************************************************
            // Dans cette version on ne traite qu'un seul colis: $colis[0]
            $objets_poste->opost_obj_cod = $colis[0]['obj_cod'];
            $objets_poste->opost_prix_demande = 1 * $colis[0]['prix_demande'];
            $objets_poste->opost_emet_perso_cod = $perso_cod;
            $objets_poste->opost_emet_pos_cod = $perso->get_position()['pos']->pos_cod;
            $objets_poste->opost_dest_perso_cod = $destinataire_list[0]->perso_cod;

            //faire le paiement
            $perso->perso_po = $perso->perso_po - $frais_port;
            $perso->stocke();

            // Retirer l'objet de l'inventaire du perso (traite l'événement)
            $objets_poste->deposeObjetRelaisPoste();

            // le cod du premier objet du colis c'est le n° de colis			$objets_poste->opost_colis_cod		   = $objets_poste->opost_cod ;
            $objets_poste->stocke();               // Update

            //********************************************************************************************************************************

            $options_twig = array(
                'titre' => "Le colis a bien été envoyé:",
                'action' => "poste4",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire_list[0]->perso_nom,
                'date_livraison' => $objets_poste->getDateLivraison(),
                'date_confiscation' => $objets_poste->getDateConfiscation()
            );
        } else
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste3",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire_list[0]->perso_nom,
                'date_livraison' => $objets_poste->getDateLivraison(),
                'date_confiscation' => $objets_poste->getDateConfiscation(),
                'message' => "Une erreur c'est produite, nous ne pouvons pas envoyer votre colis. Veuillez nous excuser pour cet impondérable!"
            );
        }
    } //--------------------------------------- valider l'adresse de livraison (nom du destinataire) ---------------------------------------
    else if (($_POST["action"] == "poste3") && ($_POST["next"] != ""))
    {
        if (count($destinataire_list) <= 0)
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste2",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire,
                'message' => "Aucun aventuriers ne correspond à ce nom, veuillez corriger:"
            );
        } else if (count($destinataire_list) > 1)
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste2",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire,
                'message' => "Trop d'aventuriers correspondent à ce nom, veuillez corriger:"
            );
        } else if ((count($destinataire_list) == 1) && ($destinataire_list[0]->perso_cod == $perso_cod))
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste2",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire,
                'message' => "Il est impossible de s'envoyer un colis à soit même, veuillez corriger:"
            );
        } else if ((count($destinataire_list) == 1) && ($destinataire_list[0]->is_4eme_perso()))
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste2",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire,
                'message' => "Il est impossible d'envoyer un colis à un \"4ème perso\" ({$destinataire_list[0]->perso_nom}), veuillez corriger:"
            );
        } else if ((count($destinataire_list) == 1) && ($perso_compte->pcompt_compt_cod == $compt_cod))
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste2",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire,
                'message' => "Il est impossible d'envoyer un colis à un autre membre de sa triplette ({$destinataire_list[0]->perso_nom}), veuillez corriger:"
            );
        } else if ((count($destinataire_list) == 1) && ($perso_attend_colis))
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste2",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire,
                'message' => "Il est impossible d'envoyer le colis à un aventurier ({$destinataire_list[0]->perso_nom}) qui en attend déjà un autre, veuillez corriger:"
            );
        } else
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste3",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port,
                'destinataire' => $destinataire_list[0]->perso_nom,
                'date_livraison' => $objets_poste->getDateLivraison(),
                'date_confiscation' => $objets_poste->getDateConfiscation()
            );
        }
    } //--------------------------------------- peser le colis, afficher les frais de port et demander le destinataire ---------------------------------------
    else if (($_POST["action"] == "poste2") && ($_POST["next"] != ""))
    {
        if ($_POST["obj"] == "")
        {
            $options_twig = array(
                'titre' => "Il faut sélectionner au moins un équipement à envoyer:",
                'action' => "poste1",
                'objets' => $objets
            );
        } else if ($perso->perso_po < $frais_port)
        {
            $options_twig = array(
                'titre' => "Essayez de sélectionner un objet moins lourd:",
                'action' => "poste1",
                'objets' => $objets,
                'message' => "Vous ne disposez pas d'assez de brouzoufs pour payer les frais de port ($frais_port brouzoufs)"
            );
        } else
        {
            $options_twig = array(
                'titre' => "Le colis à envoyer contient:",
                'action' => "poste2",
                'objets' => $colis,
                'poids' => $poids_colis,
                'prix_demande' => $prix_demande,
                'frais_port' => $frais_port
            );

        }
    } //--------------------------------------- Saisie de/des éléments à mettre dans le colis ---------------------------------------
    else
    {
        $options_twig = array(
            'titre' => "Sélectionner l'équipement à envoyer:",
            'action' => "poste1",
            'objets' => $objets
        );
    }

    //---------------------------------------rendering ! ---------------------------------------
    $template = $twig->load('lieu_relais_poste.twig');
    echo $template->render(array_merge($options_twig_defaut, $options_twig_colis, $options_twig, ["COFFRE" => $cc]));
}