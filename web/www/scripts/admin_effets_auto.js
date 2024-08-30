var EffetAuto = {num_courant: 0};
EffetAuto.Champs = [];
EffetAuto.MontreValidite = false;
EffetAuto.EditionCompteur = false;
EffetAuto.EditionEAPosition = false;
EffetAuto.EditionEA = {etage_cod:0};

/*=============================== Definition des DECLENCHEURS et de leurs paramètres ===============================*/
EffetAuto.Triggers = {
	"D":   {description: "À l’activation de sa DLT.",
			default:'deb_tour_generique',
			declencheur:'Active sa DLT',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.', ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"M":   {description: "À sa mort.",
			default:'deb_tour_generique',
			declencheur:'Meurt',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"T":   {description: "lorsqu’il tue sa cible.",
			default:'deb_tour_generique',
			declencheur:'Tue sa cible',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"A":   {description: "lorsqu’il attaque sa cible.",
			default:'deb_tour_generique',
			declencheur:'Attaque sa cible',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"AE":  {description: "lorsqu’il attaque sa cible qui esquive.",
			default:'deb_tour_generique',
			declencheur:'Attaque sa cible qui esquive',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"AT":  {description: "lorsqu’il attaque et touche sa cible.",
			default:'deb_tour_generique',
			declencheur:'Attaque et touche sa cible',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"AC":  {description: "lorsqu’il est attaqué.",
			default:'deb_tour_generique',
			declencheur:'Est attaqué',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"ACE": {description: "lorsqu’il esquive.",
			default:'deb_tour_generique',
			declencheur:'Esquive une attaque',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"ACT": {description: "lorsqu’il est touché.",
			default:'deb_tour_generique',
			declencheur:'Est touché par une attaque',
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			]
	},
	"BMC": {description: "lorsque le Bonus/Malus change.",
			default:'deb_tour_generique',
			declencheur:'Subit un changement de Bonus/Malus',
			remarque: "<br><strong><u>ATTENTION</u></strong>: Il n’y a pas de ciblage sur  <u>le protagoniste</u> pour ce déclencheur.",
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
				{ nom: 'trig_compteur', type: 'BMCompteur', label: 'Décencheur', description: 'Le compteur.' },
				{ nom: 'trig_sens', type: 'BMCsens', label: 'Sens de déclemement', description: 'Déclenchement lorsque le Bonus/Malus dépasse le seuil ou lorsqu’il retombe en dessous.' },
				{ nom: 'trig_seuil', type: 'entier', label: 'Seuil du Bonus/Malus', description: 'Valeur de déclenement du Bonus.', ValidationTrigger:true, validation: Validation.Types.Entier },
				{ nom: 'trig_raz', type: 'checkbox', label: 'Remise à zéro du BM après déclenchement', description: 'Cocher pour remettre le BM à 0 après déclenchement'},
				{ nom: 'trig_nom', type: 'texte', longueur: 30, label: 'Changement du nom', description: 'Nouveau nom du monstre en cas de basculement de seuil, laisser vide pour ne faire aucun changement. (utiliser les tags [nom] pour le nom actuel du monstre ou [nom_generique] pour son nom de base)'},
			]
	},
	"DEP": {description: "lorsqu’il se déplace.",
		default:'deb_tour_generique',
		declencheur:'Se déplace',
		remarque: "<br><strong><u>ATTENTION</u></strong>: Il n’y a pas de ciblage sur  <u>le protagoniste</u> pour ce déclencheur (sauf cas d’un saut ou d'un dépacement de monture).",
		parametres: [
			{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
		]
	},
	"MAL": {description: "lorsqu’il lance un sort.",
		default:'deb_tour_generique',
		declencheur:'Lance un sort',
		remarque: "<br><strong><u>ATTENTION</u></strong>: Il n’y a pas de ciblage sur  <u>le protagoniste</u> pour ce déclencheur dans le cas ou l'on choisi un seul effet, et il y aura un effet sur chaque cible dans l'autre cas.",
		parametres: [
			{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_type_benefique', type: 'checkbox', label: 'Déclencher sur les sorts Bénéfiques?', description: 'Cocher pour déclencher l’effet sur les sorts Benefiques' },
			{ nom: 'trig_type_agressif', type: 'checkbox', label: 'Déclencher sur les sorts Agressifs?', description: 'Cocher pour déclencher l’effet sur les sorts Agressifs' },
			{ nom: 'trig_type_neutre', type: 'checkbox', label: 'Déclencher sur les sorts Neutres?', description: 'Cocher pour déclencher l’effet sur les sorts Neutres (ni agressif, ni benefique)' },
			{ nom: 'trig_effet', type: 'MAGeffet', label: 'Les effets', description: 'Y-a-t-il un effet de l’EA pour chaque cible ou juste un seul effet. ATTENTION si un effet par cible est sélectionné et qu’il n’y a aucune cible alors l’effet ne sera pas déclenché.' },
		]
	},
	"MAC": {description: "lorsqu’il est ciblé par un sort.",
		default:'deb_tour_generique',
		declencheur:'Est la cible d’un sort',
		parametres: [
			{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.', ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_type_benefique', type: 'checkbox', label: 'Déclencher sur les sorts Bénéfiques?', description: 'Cocher pour déclencher l’effet sur les sorts Benefiques' },
			{ nom: 'trig_type_agressif', type: 'checkbox', label: 'Déclencher sur les sorts Agressifs?', description: 'Cocher pour déclencher l’effet sur les sorts Agressifs' },
			{ nom: 'trig_type_neutre', type: 'checkbox', label: 'Déclencher sur les sorts Neutres?', description: 'Cocher pour déclencher l’effet sur les sorts Neutres (ni agressif, ni benefique)' },
		]
	},
	"CES": {description: "lorsqu’il change d'état de santé.",
		default:'deb_tour_generique',
		declencheur:'Change d’état de santé',
		remarque: "<br><strong><u>ATTENTION</u></strong>: Il n’y a pas toujours de ciblage sur  <u>le protagoniste</u> pour ce déclencheur.",
		parametres: [
			{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.', ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_sens', type: 'PVsens', label: 'Sens de déclemement', description: 'Déclenchement lorsque l’état de santé baisse à cet état ou au contraire lorsqu’il remonte à celui-ci.' },
			{ nom: 'trig_sante', type: 'sante', label: 'Etat de santé', description: 'L’état de santé du déclenchement.' },
		]
	},
	"OTR": {description: "lorsqu’il reçoit une transaction.",
		default:'deb_tour_generique',
		declencheur:'Reçoit un objet en transaction',
		parametres: [
			{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.', ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_dans_liste', type: 'transac', label: 'Sur objet de la liste', description: 'L’EA va être déclenché, que faire de l’objet reçu s’il fait partie de la liste des objets attendus?' },
			{ nom: 'trig_hors_liste', type: 'transac', label: 'Sur objet hors liste', description: 'L’EA ne sera pas déclenché, que faire de l’objet reçu s’il ne fait PAS partie de la liste des objets attendus?' },
			{ nom: 'trig_objet', type: 'objet', label: 'Liste d’objet attendu', description: 'Liste des objets qui déclenchent l’effet s’ils sont reçus en transaction.' },
		]
	},
	"POS": {description: "lorsqu’il arrive ou quitte une case à EA.",
		default:'deb_tour_generique',
		declencheur:'Arrive ou quitte une case à EA.',
		parametres: [
			{ nom: 'trig_nom_ea', type: 'POSNomEtage', label: 'Nom de l’EA', description: 'Nommer l’EA afin de la retrouver plus facileemnt dans l’édition de l’étage' },
			{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.', ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_pos_cods', type: 'texte', longueur: 50,  label: 'Liste de position', description: 'Liste des positions déchenchant l’EA (pos_cod séparé par des « , » ' },
			{ nom: 'trig_sens', type: 'POSsens', label: 'Sens de déplacement', description: 'L’EA va être déclenché, si le perso arrive ou quitte la case (ou dans les 2 cas)' },
			{ nom: 'trig_rearme', type: 'POSrearme', label: 'Mode de ré-armement', description: 'Comment l’EA sera-t-elle ré-armée? (Bascule = les conditions de déclenchement doivent-être retombées avant un nouvel effet sur la case de déclenchement ou sur toute la grappe/liste de position définie par l’EA)' },
			{ nom: 'trig_type', type: 'POStype', label: 'Type de déclencheur', description: 'Ce type infuencera sur l’usage des baguettes' },
			{ nom: 'trig_condition', type: 'perso-condition', label: 'Condition du perso déclencheur', description: 'L’EA va être déclenché seuelement si le perso vérifie ces conditions' },
		]
	},
}

/*=============================== Definition des EFFETS et de leurs paramètres ===============================*/
EffetAuto.Types = [
	{	nom: 'deb_tour_generique',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Bonus / Malus standard',
		description: 'Applique un Bonus / Malus standard, à une ou plusieurs cibles.',
		parametres: [
			{ nom: 'effet', type: 'BM', label: 'Effet', description: 'Le bonus/malus qui doit être appliqué.' },
			{ nom: 'cumulatif', type: 'cumulatif', label: 'Cumulatif', description: 'Sera ignoré si le bonus/malus n’est pas [cumulable].' },
			{ nom: 'trig_resistance', type: 'checkboxinv', label: 'Test de résistance', description: 'Faire un test de résistance (si décoché le bonus/malus est donné sans faire de test).' },
			{ nom: 'force', type: 'texte', longueur: 5, label: 'Valeur', description: 'La force du bonus / malus appliqué : valeur fixe ou de la forme 1d6+2', validation: Validation.Types.Roliste },
			{ nom: 'duree', type: 'entier', label: 'Durée', description: 'La durée de l’effet.', validation: Validation.Types.Entier },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' }
		],
	},
	{	nom: 'ea_ajoute_bm',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Soins/Dégâts & Multiples Bonus/Malus',
		description: 'Applique des Soins/Dégâts ainsi que des Bonus/Malus, à une ou plusieurs cibles.',
		parametres: [
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'force', type: 'entier', longueur: 2, label: 'Soins/Dégâts', description: 'Le nombre de PV impactés en plus de l’effet des BM, une valeur positive pour des soins ou négative pour des dégâts. La valeur peut être fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_resistance', type: 'checkboxinv', label: 'Test de résistance', description: 'Faire un test de résistance (si décoché les soins/dégâts et tous les bonus/malus sont donnés sans faire de test).' },
			{ nom: 'trig_effet_bm', type: 'listebm', label: 'Liste des Bonus/Malus', description: 'Liste des Bonus/Malus, ils seront tous appliqués si le jet de proba est réussi.' }

		],
	},
	{	nom: 'titre',
		debut: false,
		tueur: true,
		mort: true,
		attaque: false,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Attribution d’un titre',
		description: 'Donne un titre à l’adversaire du monstre (tueur ou tué).',
		parametres: [
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Titre', description: 'Le titre devant être attribué. [monstre] sera remplacé par le nom du monstre qui déclenche l’attribution.' }
		],
	},
	{	nom: 'trans_crap',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Crapaud',
		description: 'Transforme une cible en crapaud. Image changée, messages agrémentés de CROAAAAs. Antidote : se faire embrasser.',
		parametres: [
			{ nom: 'cible', type: 'lecture', valeur: 'Aventurier uniquement', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'lecture', valeur: 0, label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'lecture', valeur: 1, label: 'Nombre de cibles', description: 'Le nombre maximal de cibles. La valeur 0 est remplacée par 6.' },
			{ nom: 'proba', type: 'lecture', valeur: 100, label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).' },
			{ nom: 'message', type: 'lecture', valeur: '[attaquant] a transformé [cible] en crapaud !', label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »).' }
		],
	},
	{	nom: 'deb_tour_degats',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Dégâts (ou soins)',
		description: 'Inflige des dégâts aux cibles données (pour une valeur positive ; ou des soins pour une valeur négative).',
		parametres: [
			{ nom: 'force', type: 'entier', longueur: 2, label: 'Puissance', description: 'Le nombre de PV impactés par l’effet, valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'nombre', type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_cible_porteur', type: 'checkbox', label: 'Inclure le porteur d’EA', paragraphe:'divf', description: 'Si coché, le ciblage incluras le porteur de l’EA (par défaut il est exclus sauf dans le ciblage «soi-même»).' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés, suffixé de « causant X dégâts » ou « redonnant X PVs » (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de l’attaquant, [cible] celui de la cible.' }
		],
	},
	{	nom: 'deb_tour_esprit_damne',
		debut: false,
		tueur: false,
		mort: false,
		attaque: false,
		modifiable: false,
		bm_compteur: false,
		ea_etage: false,
		obsolete: true,
		affichage: 'Esprit Damné (Obsolète)',
		description: 'Applique des Malus standard de -25 de chances au toucher, -3 dégâts, +2 PA/attaque (proba 50% par cible), +3 PA/déplacements (proba 12% par cible).',
		parametres: [
			{ nom: 'cible', type: 'lecture', valeur: 'Aventurier ou Familier', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'lecture', valeur: 0, label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'lecture', valeur: 'Aucune limite', label: 'Nombre de cibles', description: 'Le nombre de cibles.' },
			{ nom: 'message', type: 'lecture', valeur: '[cible] a été ralenti par [attaquant] et/ou [cible] a été effrayé par [attaquant].', label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »).' }
		],
	},
	{	nom: 'deb_tour_invocation',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		obsolete: true,
		affichage: 'Invocation de monstre',
		description: 'Invoque un monstre d’un type donné.',
		parametres: [
			{ nom: 'effet', type: 'monstre', label: 'Monstre', description: 'Le type de monstre à invoquer.' },
			{ nom: 'nombre', type: 'lecture', valeur: 1, label: 'Nombre de cibles', description: 'Le nombre maximal de cibles. La valeur 0 est remplacée par 6.' },
			{ nom: 'proba', type: 'entier', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (nombre entier).', validation: Validation.Types.Entier },
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements. [perso_cod1] représente le nom de l’invocateur.' }
		],
	},
	{	nom: 'invoque_rejetons',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		obsolete: true,
		affichage: 'Invocations multiples',
		description: 'Invoque plusieurs monstres du type donné. Typiquement, les gelées lors de leur mort.',
		parametres: [
			{ nom: 'effet', type: 'monstre', label: 'Type de monstre', description: 'Le type de monstre à invoquer.' },
			{ nom: 'portee', type: 'lecture', valeur: 0, label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'entier', label: 'Nombre de monstres invoqués', description: 'Le nombre de monstres invoqués.', validation: Validation.Types.Entier },
		],
	},
	{	nom: 'resurrection_monstre',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		obsolete: true,
		affichage: 'Invocation sur l’étage',
		description: 'Invoque plusieurs monstres du type donné, quelque part sur le même étage. Typiquement, les golems lors de leur mort.',
		parametres: [
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100.', validation: Validation.Types.Entier },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'effet', type: 'monstre', label: 'Type de monstre', description: 'Le type de monstre à invoquer.' },
			{ nom: 'portee', type: 'lecture', valeur: 'Étage', label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'entier', label: 'Nombre de monstres invoqués', description: 'Le nombre de monstres invoqués.', validation: Validation.Types.Entier },
		],
	},
	{	nom: 'deb_tour_haloween',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: false,
		bm_compteur: false,
		ea_etage: false,
		affichage: 'Mélange voies magiques',
		description: 'Modifie de façon permanente la voie magique des cibles (animation halloween).',
		parametres: [
			{ nom: 'cible', type: 'lecture', valeur: 'Aventurier uniquement', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'lecture', valeur: 0, label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'entier', label: 'Nombre de cibles', description: 'Le nombre de cibles.', validation: Validation.Types.Entier },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Entier },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'lecture', valeur: 'La créature d’Haloween a modifié la nature magique de [cible]', label: 'Message', description: 'Le message apparaissant dans les événements.' }
		],
	},
	{	nom: 'deb_tour_necromancie',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		obsolete: true,
		affichage: 'Nécromancie',
		description: 'Crée un mort-vivant. Les probabilités sont les suivantes : \n5% Chasseur éternel, \n15% Zombie, \n10% Spectre de L’effroi, \n20% Squelette, \n10% Guerrier squelette, \n5% Poltergeist, \n30% Archer Squelette, \n5% Tourmenteur.',
		parametres: [],
	},
	{	nom: 'necromancie',
		debut: false,
		tueur: true,
		mort: false,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		obsolete: true,
		affichage: 'Nécromancie (tueur)',
		description: 'Crée un mort-vivant lorsqu’une cible est tuée, ou un clone de la victime (15% de chances).',
		parametres: [],
	},
	{	nom: 'valide_quete_avatar',
		debut: false,
		tueur: false,
		mort: true,
		attaque: false,
		modifiable: false,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Quête de l’Avatar',
		description: 'Valide ou infirme la quête liée à la mort de l’Avatar.',
		parametres: [],
	},
	{	nom: 'deb_tour_rouille',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Usure d’objets',
		description: 'Use un objet équipé par un aventurier ou un familier.',
		parametres: [
			{ nom: 'effet', type: 'choix_rouille', label: 'Message', description: 'Le message inscrit dans les événements de la cible.' },
			{ nom: 'force', type: 'entier', longueur: 3, label: 'Usure', description: 'Le nombre de points d’usure soustraits à l’objet (100 -> l’objet, même comme neuf, est instantanément détruit).', validation: Validation.Types.Entier },
			{ nom: 'cible', type: 'lecture', valeur: 'Aventurier ou Familier', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'lecture', valeur: 0, label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'lecture', valeur: 1, label: 'Nombre de cibles', description: 'Le nombre de cibles.' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (nombre entier).', validation: Validation.Types.Entier },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
		],
	},
	{	nom: 'ea_supprime_bm',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Suppression de Bonus/Malus',
		description: 'Supprime les effets d’un Bonus/Malus.',
		parametres: [
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_effet_bm', type: 'listebm2', label: 'Liste des Bonus/Malus à supprimer', description: 'Liste des Bonus/Malus, ils seront tous supprimés si le jet de proba est réussi.' }
		],
	},
	{	nom: 'ea_lance_sort',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Lance un sort',
		description: 'Lance un sort sur une ou plusieurs cibles, si le sort est un sort de case, il sera lancé sur la case de la ou des cibles.',
		parametres: [
			{ nom: 'effet', type: 'Sort', label: 'Effet', description: 'Le bonus/malus qui doit être appliqué.' },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' }
		],
	},
	{	nom: 'ea_projection',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Projette ou Attire',
		description: 'Projette ou attire à lui, le ou les cibles.',
		parametres: [
			{ nom: 'trig_sens', type: 'sens-projection', label: 'Sens de projection', description: 'Attire la cible à soit ou la repousse (une projection de distance 0 retire les locks de combats).' },
			{ nom: 'force', type: 'texte', longueur: 5, label: 'Distance de projection/attraction', description: 'C’est La force de projection/attaction: valeur fixe ou de la forme 1d2+1', validation: Validation.Types.Roliste },
			{ nom: 'trig_degats', type: 'texte', longueur: 5, label: 'Dégâts si rencontre d’un obstacle', description: 'Ce sont les dégats si la cible rencontre un obstacle: valeur fixe ou de la forme 1d2+1', validation: Validation.Types.Roliste },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'trig_cible_combat',type: 'checkbox', label: 'Sauf la cible de combat', description: 'Pour un monstre: si coché, sa cible de combat actuelle ne sera pas projetée/attirée.'},
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' }
		],
	},
	{	nom: 'ea_glissade',
		debut: true,
		tueur: false,
		mort: false,
		attaque: false,
		modifiable: true,
		bm_compteur: false,
		ea_etage: true,
		affichage: 'Glissade',
		description: 'Déplacement de la cible suivant un modèle particulier (ne fonctionne que sur les EA de déplacement)',
		parametres: [
			{ nom: 'cible', type: 'lecture', valeur: 'Le porteur de l’EA uniquement (<a target=_blank href="../images/interface/glissade.xlsx">télécharger le fichier d’aide</a>)', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_glisse', type: 'texte', longueur: 25,  label: 'Modèle de glisse', description: 'Suite de 8 valeurs (de 0% à 100%) séparées par de virgule. C’est valeurs sont les chances de glisser sur la case situé devant dans la direction du mouvement, suivi des 7 cases autour dans le sens horaire.' },
			{ nom: 'trig_direction', type: 'texte', longueur: 25,  label: 'Modèle directionnel', description: 'Suite de 8 valeurs (de 0% à 100%) séparées par de virgule. C’est valeurs sont des % à appliquer au modèle de glisse en fonction de la direction de déplacement. La première valeur est le % pour un deplacement vers le N, les 7 suivantes pour les 7 points cardinaux suivants dans le sens horaire (NE, E, SE, S, SO, O, NO)' },
			{ nom: 'trig_cardinal', type: 'point-cardinal',   label: 'Direction de pente', description: 'C’est la direction pricipale de glisse lors d’une glissage sur une pente.' },
			{ nom: 'trig_pente', type: 'texte', longueur: 5,  label: 'Pente (en %)', description: 'C’est le % de pente lors d’une glissage sur une pente.' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' }
		],
	},
	{	nom: 'ea_saut_sur_cible',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Bondi sur une cible',
		description: 'Saute directement sur une des cibles à portée. Compte comme un déplacement (déclenchement de l’EA « Se déplace »).',
		parametres: [
			{ nom: 'trig_degats', type: 'texte', longueur: 5, label: 'Dégâts fait par le saut sur la cible',  description: 'Ce sont des dégats directs fait par le bond: valeur fixe ou de la forme 1d2+1', validation: Validation.Types.Roliste },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' }
		],
	},
	{	nom: 'ea_drop_objet',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Laisse tomber des objets',
		description: 'Laisse tomber des objets au sol.',
		parametres: [
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre d’objet', description: 'Le nombre maximal d’objet. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_pos', type: 'POSDrop', label: 'Position', description: 'Indique si l’objet normalement tombe au sol, aux pieds ou dans l’invenaire de la cible.' },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_objet', type: 'drop', label: 'Liste d’objet', description: 'Liste d’objet et taux de drop de chacun, chaque tirage laissera au sol un seul objet de cette liste.' }
		],
	},
	{	nom: 'ea_invocation',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Invoque des monstres',
		description: 'Génération d’un ou plusieurs monstres dont le type est établit depuis une liste.',
		parametres: [
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de monstre', description: 'Le nombre de monstre à invoquer. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'cible', type: 'cible-case', label: 'Case de ciblage', description: 'La case est déterminée suivant Le type de cible sur lesquelles l’effet peut s’appliquer (case aléatoire s’il n’y a pas de cible valide au moment de l’invocation).' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_monstre', type: 'invocation', label: 'Liste de monstre', description: 'Liste de monstre generique et taux d’invocation de chacun, chaque tirage invoquera un seul monstre de cette liste.' }
		],
	},
	{	nom: 'ea_metamorphe',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Se metamorphose',
		description: 'Prends les caractéristiques d’un autre monstre générique.',
		parametres: [
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'trig_nom', type: 'texte', longueur: 30, label: 'Changement du nom', description: 'Nouveau nom du monstre en cas de métamorphose, laisser vide pour ne faire aucun changement. (utiliser les tags [nom] pour le nom actuel du monstre ou [nom_generique] pour son nouveau nom de base)'},
			{ nom: 'trig_ea_persistant', type: 'checkbox', label: 'EA persistant?', description: 'Si coché, cet EA sera persistant, le monstre métamorphosé conservera encore cette facultée.' },
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_monstre', type: 'invocation', label: 'Liste de monstre', description: 'Liste de monstre generique et chance de metamorphose en l’un d’eux.' }
		],
	},
	{	nom: 'ea_implantation_ea',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: false,
		affichage: 'Implante une EA',
		description: 'Implante une EA à une cible.',
		parametres: [
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_validite', type: 'entier', label: 'Validité de l’implantation (en min.):', description: 'Délai de validité (en minutes) de l’EA après implantation. Laisser vide pour un délai infini.',  validation: Validation.Types.EntierOuVide },
			{ nom: 'effet', type: 'ea', label: 'EA à implanter:', description: 'L’EA qui sera implantée sur les cibles.' }
		],
	},
	{	nom: 'ea_meca',
		debut: false,
		tueur: false,
		mort: false,
		attaque: false,
		modifiable: true,
		bm_compteur: false,
		ea_etage: true,
		affichage: 'Active/Desactive un mécanisme',
		description: 'Activation/Desactivation de mécanismes.',
		parametres: [
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des mécanismes).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_meca', type: 'meca', label: 'Liste de mécanisme', description: 'Liste de mécanisme à activer/désactiver avec chances individuels.' }
		],
	},
	{	nom: 'ea_teleportation',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Teleportation',
		description: 'Teleporte la ou les cibles sur une case donnée.',
		parametres: [
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_pos_unique',type: 'checkbox', label: 'Téléportation groupée', description: 'si cochée, et s’il y a plusieurs cibles et plusieurs positions, toutes les cibles seront téléportées à la même position.'},
			{ nom: 'trig_pos_cod', type: 'positions', label: 'Position de Téléportion', description: 'L’endroit où seront téléporté la ou les cibles.' },
		],
	},
	{	nom: 'ea_modification_ea',
		debut: false,
		tueur: false,
		mort: false,
		attaque: false,
		modifiable: true,
		bm_compteur: false,
		ea_etage: true,
		affichage: 'Active/Desactive un EA d’étage',
		description: 'Activation/Desactivation d’EA d’étage.',
		parametres: [
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des mécanismes).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_ea_etage', type: 'ea_etage', label: 'Liste des EA', description: 'Liste des EA à activer/désactiver avec chances individuels.' }
		],
	},
	{	nom: 'ea_message',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		ea_etage: true,
		affichage: 'Message',
		description: 'Envoyer un message à un ou plusieurs persos.',
		parametres: [
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', paragraphe:'divd', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'trig_exclure_porteur', type: 'checkbox', label: 'Exclure le porteur/compagnons', paragraphe:'divf', description: 'Si coché, le ciblage excluras le porteur de l’EA et ses compagnons que sont le familier et le cavalier/monture (on ne peut exclure le porteur d’un ciblage « Soi même » ).' },
			{ nom: 'proba', type: 'numerique', paragraphe:'divd', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'trig_proba_chain', type: 'proba', label: 'Chainage', paragraphe:'divf' ,description: 'Chainage des EA'},
			{ nom: 'trig_expediteur_msg', type: 'ExpMsg', label: 'Expediteur', description: 'Expediteur du message.' },
			{ nom: 'trig_titre_msg', type: 'texte', longueur: 50, label: 'Titre', description: 'Titre du message.' },
			{ nom: 'message', type: 'textearea', longueur: 4, label: 'Message', description: 'Le message qui sera envoyé par MP aux cibles. [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA (celui qui reçoit le message).' },
		],
	}
];
/*=============================== fin de défintion des EA ===============================*/

EffetAuto.addItem = function (elem, M)
{
	if ((elem.parent().find("tr[id^='row-']").length<M) || (M == 0))
	{

		var row = elem[0].id ;
		var s = row.split('-') ;
		var r = (1+1*s[2]);
		var new_row = s[0]+'-'+s[1]+'-'+r+'-';
		var new_elem = '<tr id="'+new_row+'">'+elem.html().replace(new RegExp(row,'g'), new_row)+'</tr>';
		$(new_elem).insertAfter(elem);

		//Maintenant que l'élément est inséré, on raz les valeurs parasites qui ont été dupliquées de la précédente entrée
		$('*[id$="'+new_row+'"]').each(function( index ) {
			if ($( this ).attr("data-entry"))
			{
				if ($( this ).attr("data-entry") == "val")
				{
					$( this ).val("");
				}
				else if ($( this ).attr("data-entry") == "text")
				{
					$( this ).text("");
				}
				else if ($( this ).attr("data-entry") == "checked")
				{
					$( this ).prop('checked');
				}
				else if ($( this ).attr("data-entry") == "unchecked")
				{
					$( this ).prop('checked', false);
				}
				else
				{
					$( this ).val( $( this ).attr("data-entry") );
				}
			}
		});
	}
	else
	{
		alert('Il ne doit pas y avoir plus de '+M+' valeur(s) pour ce paramètre!')
	}
}

EffetAuto.delItem = function  (elem, n)
{
	var min = (n>0 ? n : 1) ;
	if ( elem.parent().find("tr[id^='row-']").length > min  )
	{
		elem.remove();
	}
	else
	{
		alert('Il doit rester au moins '+min+' valeur(s) pour ce paramètre!')
	}
}

EffetAuto.videListe = function (numero) {
	var liste = document.getElementById('fonction_type_' + numero);
	while (liste.hasChildNodes())
		liste.removeChild(liste.firstChild);
}

EffetAuto.remplirListe = function (type, numero) {
	EffetAuto.videListe (numero);
	var liste = document.getElementById('fonction_type_' + numero);
	for (var i = 0; i < EffetAuto.Types.length; i++) {
		var fct = EffetAuto.Types[i];
		if (!fct.obsolete && fct.modifiable && (fct.nom != 'ea_implantation_ea' || (!EffetAuto.EditionCompteur && !EffetAuto.EditionEAPosition)) && ((fct.ea_etage && type == 'POS') ||(fct.bm_compteur && type == 'BMC') || (fct.attaque && type == 'MAL') || (fct.attaque && type == 'MAC') || (fct.debut && type == 'OTR') || (fct.debut && type == 'CES') || (fct.debut && type == 'DEP') || (fct.debut && type == 'D') || (fct.tueur && type == 'T') || (fct.mort && type == 'M') || (fct.attaque && type == 'A') || (fct.attaque && type == 'AE') || (fct.attaque && (type == 'AT' || type == 'AC' || type == 'ACE' || type == 'ACT')))) {
			liste.options[liste.options.length] = new Option();
			liste.options[liste.options.length - 1].text = fct.affichage;
			liste.options[liste.options.length - 1].value = fct.nom;
			liste.options[liste.options.length - 1].title = fct.description;
			if (fct.obsolete) liste.options[liste.options.length - 1].disabled = "disabled";
		}
	}

	EffetAuto.EnleveValidation (numero, 'trigger');
	document.getElementById('formulaire_declenchement_' + numero).innerHTML = EffetAuto.EcritNouveauDeclencheurAuto(type, numero);

	// on a potentiellement changé le déclencheur, il faut remettre un effet par défaut
	EffetAuto.ChangeEffetAuto(EffetAuto.Triggers[type].default, numero);
	Validation.Valide ();
}

EffetAuto.CopieListe = function (listeid, selection) {
	var options = '';
	var curGroup = '' ;
	var modele = document.getElementById(listeid);
	for (var i = 0; i < modele.options.length; i++) {
		var opt_v = modele.options[i].value;
		var opt_t = modele.options[i].text;
		var selectionne = ((selection == opt_v) ? 'selected="selected"' : '' );

		// traitement des options de groupe s'i y en a
		var optGroup =  modele.options[i].parentNode ;
		if (optGroup.nodeName == "OPTGROUP") {
			if (curGroup != optGroup.label) {
				if (curGroup != '')options +='</optgroup>';
				options += '<optgroup label="' +optGroup.label + '">';
				curGroup = optGroup.label ;
			}
		}

		options += '<option value="' + opt_v + '" ' + selectionne + '>' + opt_t + '</option>';
	}
	if (curGroup != '')options +='</optgroup>';
	return options;
}

EffetAuto.CopieListeMultiple = function (listeid, selection) {
	var options = '';
	var modele = document.getElementById(listeid);
	for (var i = 0; i < modele.options.length; i++) {
		var opt_v = modele.options[i].value;
		var opt_t = modele.options[i].text;
		var selectionne = ((selection.indexOf(opt_v) >= 0) ? 'selected="selected"' : '' );
		options += '<option value="' + opt_v + '" ' + selectionne + '>' + opt_t + '</option>';
	}
	return options;
}

EffetAuto.getDonnees = function (nom) {
	for (var i = 0; i < EffetAuto.Types.length; i++) {
		if (EffetAuto.Types[i].nom == nom)
			return EffetAuto.Types[i];
	}
	return false;
}

EffetAuto.ChampTexte = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = "";
	var nom = "fonc_" + parametre.nom + numero.toString();
	if (parametre.validation) {
		Validation.Ajoute (nom, parametre.validation, parametre.ValidationTrigger ? 'trigger' : 'effet');
		EffetAuto.Champs[numero].push(nom);
	}
	var onChange = (parametre.validation) ? " onchange='Validation.ValideParId(this.id);'" : '';
	var onKeyUp = (parametre.validation) ? " onkeyup='Validation.ValideParId(this.id);'" : '';

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<input type="text"' + onChange + onKeyUp + ' value="' + valeur + '" size="' + parametre.longueur + '" name="' + nom + '" id="' + nom + '"/>';
	if (parametre.commentaires) html += parametre.commentaires;
	html += '</label>';
	return html;
}

EffetAuto.ChampTexteArea = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = "";
	var nom = "fonc_" + parametre.nom + numero.toString();
	if (parametre.validation) {
		Validation.Ajoute (nom, parametre.validation, parametre.ValidationTrigger ? 'trigger' : 'effet');
		EffetAuto.Champs[numero].push(nom);
	}
	var onChange = (parametre.validation) ? " onchange='Validation.ValideParId(this.id);'" : '';
	var onKeyUp = (parametre.validation) ? " onkeyup='Validation.ValideParId(this.id);'" : '';

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<textarea type="text"' + onChange + onKeyUp + '" cols="60" rows="' + parametre.longueur + '" name="' + nom + '" id="' + nom + '"/>'+ valeur +'</textarea>';
	if (parametre.commentaires) html += parametre.commentaires;
	html += '</label>';
	return html;
}


EffetAuto.ChampValidite = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = "0";
	var nom = "fonc_" + parametre.nom + numero.toString();
	var nom_select = "fonc_" + parametre.nom + "_unite" + numero.toString();

	if (parametre.validation) {
		Validation.Ajoute (nom, parametre.validation, parametre.ValidationTrigger ? 'trigger' : 'effet');
		EffetAuto.Champs[numero].push(nom);
	}

	var onChange = (parametre.validation) ? " onchange='Validation.ValideParId(this.id);'" : '';
	var onKeyUp = (parametre.validation) ? " onkeyup='Validation.ValideParId(this.id);'" : '';

	var resultat =  (!EffetAuto.MontreValidite) ? '<div id="champ-validite-'+numero+'" style="display:none;">' :  '<div id="champ-validite-'+numero+'">' ;
	resultat += '<label><strong>' + parametre.label + '</strong>&nbsp;<input type="text"' + onChange + onKeyUp + ' value="' + valeur + '" size="4" name="' + nom + '" id="' + nom + '"/></label>';
	resultat += '<select name="' + nom_select + '"><option value="1">minutes</option><option value="60">heures</option><option value="1440">jours</option><option value="43200">mois</option></select>';
	resultat+= '</div>';

	return resultat;
}

EffetAuto.ChampCache = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = "";
	return '<input type="hidden" value="' + valeur + '" name="fonc_' + parametre.nom + numero.toString() + '"/>';
}

EffetAuto.ChampLecture = function (parametre, numero, valeur) {
	var resultat = '<strong>' + parametre.label + '</strong>';
	if (valeur != '')
		resultat += ' : ' + valeur;

	// cas particulier de l'EA, il faut ajouter un contenair pour afficher la definition de ce dernier
	if (parametre.type && parametre.type == "ea")
		resultat+= '<div id="ea-container-'+numero+'" data-child-id="'+valeur+'" data-child-numero=""></div>';

	return resultat;
}

EffetAuto.ChampChoixRouille = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="0" ' + ((valeur == 0) ? 'selected="selected"' : '' ) + '>(Par défaut)</option>';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Jet d’acide</option>';
	html += '<option value="2" ' + ((valeur == 2) ? 'selected="selected"' : '' ) + '>Tentacules perforants</option>';
	html += '<option value="3" ' + ((valeur == 3) ? 'selected="selected"' : '' ) + '>Jet de flammes</option>';
	html += '<option value="4" ' + ((valeur == 4) ? 'selected="selected"' : '' ) + '>Attaque éthérée</option></select></label>';
	return html;
}

EffetAuto.ChampChoixBMCsens = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Le Bonus/Malus dépasse le seuil</option>';
	html += '<option value="-1" ' + ((valeur == -1) ? 'selected="selected"' : '' ) + '>Le Bonus/Malus retombe en dessous du seuil</option></select></label>';
	return html;
}

EffetAuto.ChampChoixPVsens = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="0" ' + ((valeur == 0) ? 'selected="selected"' : '' ) + '>L’état de santé baisse ou augmente</option>';
	html += '<option value="-1" ' + ((valeur == -1) ? 'selected="selected"' : '' ) + '>L’état de santé baisse</option>';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>L’état de Santé augmente</option></select></label>';
	return html;
}

EffetAuto.ChampChoixSante = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="75-50" ' + ((valeur == "75-50") ? 'selected="selected"' : '' ) + '>Touché</option>';
	html += '<option value="50-25" ' + ((valeur == "50-25") ? 'selected="selected"' : '' ) + '>Blessé</option>';
	html += '<option value="25-15" ' + ((valeur == "25-15") ? 'selected="selected"' : '' ) + '>Gravement touché</option>';
	html += '<option value="15-0" ' + ((valeur == "15-0") ? 'selected="selected"' : '' ) + '>Presque mort</option></select></label>';
	return html;
}

EffetAuto.ChampChoixSensProjection = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Repousse sur son passage ou éloigne les cibles</option>';
	html += '<option value="-1" ' + ((valeur == -1) ? 'selected="selected"' : '' ) + '>Attire les cibles à lui</option></select></label>';
	html += "<br />";
	return html;
}

EffetAuto.ChampChoixPointCardinal = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="N" ' + ((valeur == "N") ? 'selected="selected"' : '' ) + '>Nord</option>';
	html += '<option value="NE" ' + ((valeur == "NE") ? 'selected="selected"' : '' ) + '>Nord-Est</option>';
	html += '<option value="E" ' + ((valeur == "E") ? 'selected="selected"' : '' ) + '>Est</option>';
	html += '<option value="SE" ' + ((valeur == "SE") ? 'selected="selected"' : '' ) + '>Sud-Est</option>';
	html += '<option value="S" ' + ((valeur == "S") ? 'selected="selected"' : '' ) + '>Sud</option>';
	html += '<option value="SO" ' + ((valeur == "SO") ? 'selected="selected"' : '' ) + '>Sud-Ouest</option>';
	html += '<option value="O" ' + ((valeur == "O") ? 'selected="selected"' : '' ) + '>Ouest</option>';
	html += '<option value="NO" ' + ((valeur == "NO") ? 'selected="selected"' : '' ) + '>Nord-Ouest</option>';
	html += "</select></label><br />";
	return html;
}

EffetAuto.ChampChoixNomEtage = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = "";
	var nom = "fonc_" + parametre.nom + numero.toString();
	var etage = "fonc_trig_pos_etage" + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<input type="text" value="' + valeur + '" size=50 name="' + nom + '" id="' + nom + '"/>';
	if (parametre.commentaires) html += parametre.commentaires;
	html += '</label>';
	html += '<input type="hidden" name="' + etage + '" id="' + etage + '" value="'+ EffetAuto.EditionEA.etage_cod +'">';

	return html;
}

EffetAuto.ChampChoixSensDeplacement = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="0" ' + ((valeur == 0) ? 'selected="selected"' : '' ) + '>Arrive sur la case</option>';
	html += '<option value="-1" ' + ((valeur == -1) ? 'selected="selected"' : '' ) + '>Quitte la case</option>';
	html += '<option value="2" ' + ((valeur == 2) ? 'selected="selected"' : '' ) + '>Arrive ou Quitte la case</option>';
	html += '<option value="-2" ' + ((valeur == -2) ? 'selected="selected"' : '' ) + '>Sur évenement mécanisme</option></select></label>';
	html += "<br />";
	return html;
}

EffetAuto.ChampChoixTypeEA = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="0" ' + ((valeur == 0) ? 'selected="selected"' : '' ) + '>Indétectable</option>';
	html += '<option value="-1" ' + ((valeur == -1) ? 'selected="selected"' : '' ) + '>Piège détectable</option>';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Cachette détectable</option></select></label>';
	html += "<br />";
	return html;
}

EffetAuto.ChampChoixRearmement = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="0" ' + ((valeur == 0) ? 'selected="selected"' : '' ) + '>Toujours</option>';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Une seule fois</option>';
	html += '<option value="2" ' + ((valeur == 2) ? 'selected="selected"' : '' ) + '>Bascule (case)</option>';
	html += '<option value="3" ' + ((valeur == 3) ? 'selected="selected"' : '' ) + '>Bascule (grappe)</option>';
	html += '<option value="-1" ' + ((valeur == -1) ? 'selected="selected"' : '' ) + '>Jamais</option></select></label>';
	html += "<br />";
	return html;
}

EffetAuto.ChampChoixDrop = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="0" ' + ((valeur == 0) ? 'selected="selected"' : '' ) + '>Au sol (ignore la cible)</option>';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Aux pieds de la cible</option>';
	html += '<option value="2" ' + ((valeur == 2) ? 'selected="selected"' : '' ) + '>Dans l’inventaire de la cible</option></select></label>';
	html += "<br />";
	return html;
}

EffetAuto.ChampChoixMAGeffet = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="1" ' + ((valeur == "1") ? 'selected="selected"' : '' ) + '>Déclencher UN SEUL effet au lancement</option>';
	html += '<option value="N" ' + ((valeur == "N") ? 'selected="selected"' : '' ) + '>Déclencher un effet POUR CHAQUE cible</option></select></label>';
	return html;
}

EffetAuto.ChangeCiblage = function (select) {
	var numero = select.name.substr(10) ;
	if ($(select).val()=="V") {
		$('#div_trig_races'+numero).css("display", "");
	} else {
		$('#div_trig_races'+numero).css("display", "none");
		$("select[id^='multi-select-'] option:selected").each(function () {
			$(this).prop("selected", false);
		});
	}
}

EffetAuto.ChampCible = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select onchange="EffetAuto.ChangeCiblage(this);" name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="S" ' + ((valeur == 'S') ? 'selected="selected"' : '' ) + '>Soi-même</option>';
	html += '<option value="A" ' + ((valeur == 'A') ? 'selected="selected"' : '' ) + '>Les Amis (Familier / Aventurier vs Monstre)</option>';
	html += '<option value="E" ' + ((valeur == 'E') ? 'selected="selected"' : '' ) + '>Les Ennemis (Familier / Aventurier vs Monstre)</option>';
	html += '<option value="R" ' + ((valeur == 'R') ? 'selected="selected"' : '' ) + '>Même Race</option>';
	html += '<option value="V" ' + ((valeur == 'V') ? 'selected="selected"' : '' ) + '>Vorpale (Races Spécifiques)</option>';
	html += '<option value="P" ' + ((valeur == 'P') ? 'selected="selected"' : '' ) + '>Aventuriers et Familiers (y compris sur un refuge)</option>';
	html += '<option value="J" ' + ((valeur == 'J') ? 'selected="selected"' : '' ) + '>Aventuriers</option>';
	html += '<option value="L" ' + ((valeur == 'L') ? 'selected="selected"' : '' ) + '>Son Compagnon (Familier si Aventurier et inversement)</option>';
	html += '<option value="C" ' + ((valeur == 'C') ? 'selected="selected"' : '' ) + '>La cible actuelle du monstre</option>';
	html += '<option value="O" ' + ((valeur == 'O') ? 'selected="selected"' : '' ) + '>Le protagoniste (tueur, tué, attaquant...)</option>';
	html += '<option value="M" ' + ((valeur == 'M') ? 'selected="selected"' : '' ) + '>Monture/Cavalier du protagoniste</option>';
	html += '<option value="T" ' + ((valeur == 'T') ? 'selected="selected"' : '' ) + '>Tout le monde</option></select>';
	if (parametre.commentaires) html += parametre.commentaires;
	html += '</label>';
	return html;
}

EffetAuto.ChampCibleCase = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select onchange="EffetAuto.ChangeCiblage(this);" name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="X" ' + ((valeur == 'X') ? 'selected="selected"' : '' ) + '>Une case aléatoire à portée</option>';
	html += '<option value="S" ' + ((valeur == 'S') ? 'selected="selected"' : '' ) + '>Soi-même</option>';
	html += '<option value="A" ' + ((valeur == 'A') ? 'selected="selected"' : '' ) + '>Les Amis (Familier / Aventurier vs Monstre)</option>';
	html += '<option value="E" ' + ((valeur == 'E') ? 'selected="selected"' : '' ) + '>Les Ennemis (Familier / Aventurier vs Monstre)</option>';
	html += '<option value="R" ' + ((valeur == 'R') ? 'selected="selected"' : '' ) + '>Même Race</option>';
	html += '<option value="V" ' + ((valeur == 'V') ? 'selected="selected"' : '' ) + '>Vorpale (Races Spécifiques)</option>';
	html += '<option value="P" ' + ((valeur == 'P') ? 'selected="selected"' : '' ) + '>Aventuriers et Familiers (y compris sur un refuge)</option>';
	html += '<option value="J" ' + ((valeur == 'J') ? 'selected="selected"' : '' ) + '>Aventuriers</option>';
	html += '<option value="L" ' + ((valeur == 'L') ? 'selected="selected"' : '' ) + '>Son Compagnon (Familier si Aventurier et inversement)</option>';
	html += '<option value="C" ' + ((valeur == 'C') ? 'selected="selected"' : '' ) + '>La cible actuelle du monstre</option>';
	html += '<option value="O" ' + ((valeur == 'O') ? 'selected="selected"' : '' ) + '>Le protagoniste (tueur, tué, attaquant...)</option>';
	html += '<option value="T" ' + ((valeur == 'T') ? 'selected="selected"' : '' ) + '>Tout le monde</option></select></label>';
	return html;
}


EffetAuto.ChampCibleVorpale = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var nom = "fonc_" + parametre.nom + numero.toString();
	var label = "div_" + parametre.nom + numero.toString();
	var style = valeur.length == 0 ? ' style="display:none;"' : '';

	var html = '<label id="' + label +'"'+style+'><strong>' + parametre.label + '</strong>&nbsp;<select id="multi-select-' + nom + '" multiple name="' + nom + '[]">';
	html += EffetAuto.CopieListeMultiple ('liste_race_modele', valeur);
	html += '</select></label>';

	return html;
}

EffetAuto.ChampListeObjet = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomObjet = "obj_fonc_" + parametre.nom + numero.toString()+"_gobj_cod";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';
		html += '<select style="max-width: 200px;" name="' + nomObjet + '[]">';
		html += EffetAuto.CopieListe ('liste_objet_modele',  valeur.length ? valeur[i].gobj_cod : "");
		html += '</select>&nbsp';
		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}

EffetAuto.ChampTransac = function (parametre, numero, valeur) {
	if (!valeur) valeur = "S";
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="S" ' + ((valeur == 'S') ? 'selected="selected"' : '') + '>Supprimer l’objet reçu</option>';
	html += '<option value="I" ' + ((valeur == 'I') ? 'selected="selected"' : '') + '>Mettre l’objet reçu dans l’inventaire</option>';
	html += '<option value="L" ' + ((valeur == 'L') ? 'selected="selected"' : '') + '>Laisser en transaction</option>';
	html += '<option value="R" ' + ((valeur == 'R') ? 'selected="selected"' : '') + '>Refuser la transaction</option></select>';
	if (parametre.commentaires) html += parametre.commentaires;
	html += '</label>';
	return html;
}

EffetAuto.ChampExpediteurMessage = function (parametre, numero, valeur) {
	if (!valeur) valeur = "I";
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="I" ' + ((valeur == 'I') ? 'selected="selected"' : '') + '>L’Inconscience</option>';
	html += '<option value="M" ' + ((valeur == 'M') ? 'selected="selected"' : '') + '>Malkiar</option>';
	html += '<option value="S" ' + ((valeur == 'S') ? 'selected="selected"' : '') + '>Le declencheur d’EA</option></select>';
	if (parametre.commentaires) html += parametre.commentaires;
	html += '</label>';
	return html;
}

EffetAuto.ChampDropObjet = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomObjet = "obj_fonc_" + parametre.nom + numero.toString()+"_gobj_cod";
	var nomTaux = "obj_fonc_" + parametre.nom + numero.toString()+"_taux";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';
		html += '<select style="max-width: 200px;" name="' + nomObjet + '[]">';
		html += EffetAuto.CopieListe ('liste_objet_modele',  valeur.length ? valeur[i].gobj_cod : "");
		html += '</select>&nbsp;<strong>Taux:<strong>&nbsp;<input name="'+nomTaux+'[]" type="text" size="4" value="'+( valeur.length>0 ? valeur[i].taux : "")+'">%&nbsp';
		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}

EffetAuto.ChampInvocationMonstre = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomObjet = "obj_fonc_" + parametre.nom + numero.toString()+"_gmon_cod";
	var nomTaux = "obj_fonc_" + parametre.nom + numero.toString()+"_taux";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';
		html += '<select style="max-width: 200px;" name="' + nomObjet + '[]">';
		html += EffetAuto.CopieListe ('liste_monstre_modele',  valeur.length ? valeur[i].gmon_cod : "");
		html += '</select>&nbsp;<strong>Taux:<strong>&nbsp;<input name="'+nomTaux+'[]" type="text" size="4" value="'+( valeur.length>0 ? valeur[i].taux : "")+'">%&nbsp';
		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}

EffetAuto.CheckBoxChange = function (checkbox, inputbox) {
	if ($("#" + checkbox).prop('checked')) {
		$("#" + inputbox).val("O");
	} else {
		$("#" + inputbox).val("N");
	}
}

EffetAuto.ChampListeBM = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomBM = "obj_fonc_" + parametre.nom + numero.toString()+"_tbonus_libc";
	var nomForce = "obj_fonc_" + parametre.nom + numero.toString()+"_force";
	var nomDuree = "obj_fonc_" + parametre.nom + numero.toString()+"_duree";
	var nomCumulatif = "obj_fonc_" + parametre.nom + numero.toString()+"_cumulatif";
	var checkboxCumulatif = "obj_check_" + parametre.nom + numero.toString()+"_cumulatif";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		var row='-row-'+numero+'-'+i+'-';
		var rowCumulatifNom = nomCumulatif+row;
		var rowCumulatifCheck = checkboxCumulatif+row;
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';
		html += '<select style="max-width: 200px;" name="' + nomBM + '[]">';
		html += EffetAuto.CopieListe ('liste_bm_modele',  valeur.length ? valeur[i].tbonus_libc : "");
		html += '</select>&nbsp;' ;
		html += '<input data-entry="N" id="'+rowCumulatifNom+'"  name="'+nomCumulatif+'[]" type="hidden" value="'+( valeur.length>0 ? valeur[i].cumulatif : "N")+'">';
		html += 'Cumulatif: <input data-entry="unckecked" onchange="EffetAuto.CheckBoxChange(\''+rowCumulatifCheck+'\', \''+rowCumulatifNom+'\');" id="'+rowCumulatifCheck+'" name="'+checkboxCumulatif+'[]" type="checkbox" '+( valeur.length>0 ? (valeur[i].cumulatif =='O' ? ' checked' : '') : '')+'><br>';
		html += '&nbsp;<strong>Valeur:<strong>&nbsp;<input id="force'+row+'" data-entry="val" name="'+nomForce+'[]" type="text" size="2" value="'+( valeur.length>0 ? valeur[i].force : "")+'">';
		html += '&nbsp;<strong>Durée:<strong>&nbsp;<input id="durre'+row+'" data-entry="val" name="'+nomDuree+'[]" type="text" size="2" value="'+( valeur.length>0 ? valeur[i].duree : "")+'"> tour(s)&nbsp';
		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}

EffetAuto.ChampListePersoCondition = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomConj = "obj_fonc_" + parametre.nom + numero.toString()+"_conj";
	var nomCond = "obj_fonc_" + parametre.nom + numero.toString()+"_cond";
	var nomSigne = "obj_fonc_" + parametre.nom + numero.toString()+"_signe";
	var nomVal1 = "obj_fonc_" + parametre.nom + numero.toString()+"_val1";
	var nomVal2 = "obj_fonc_" + parametre.nom + numero.toString()+"_val2";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		var row='-row-'+numero+'-'+i+'-';
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';

		html += '<select style="max-width: 45px;" name="' + nomConj + '[]">';
		var selectionne = ((valeur.length && valeur[i].conj == "ET") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="ET">ET</option>';
		var selectionne = ((valeur.length && valeur[i].conj == "OU") ? 'selected="selected"' : '' );  html += '<option ' + selectionne + ' value="OU">OU</option>';
		html += '</select>&nbsp;'

		html += '<select style="max-width: 150px;" name="' + nomCond + '[]">';
		html += EffetAuto.CopieListe ('liste_perso_condition_modele',  valeur.length ? valeur[i].cond : "");
		html += '</select>&nbsp;' ;

		html += '<select style="max-width: 45px;" name="' + nomSigne + '[]">';
		var selectionne = ((valeur.length && valeur[i].signe == "=") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="=">=</option>';
		var selectionne = ((valeur.length && valeur[i].signe == "!=") ? 'selected="selected"' : '' );  html += '<option ' + selectionne + ' value="!=">!=</option>';
		var selectionne = ((valeur.length && valeur[i].signe == "<") ? 'selected="selected"' : '' );  html += '<option ' + selectionne + ' value="<"><</option>';
		var selectionne = ((valeur.length && valeur[i].signe == "<=") ? 'selected="selected"' : '' );  html += '<option ' + selectionne + ' value="<="><=</option>';
		var selectionne = ((valeur.length && valeur[i].signe == "entre") ? 'selected="selected"' : '' );  html += '<option ' + selectionne + ' value="entre">entre</option>';
		var selectionne = ((valeur.length && valeur[i].signe == ">") ? 'selected="selected"' : '' );  html += '<option ' + selectionne + ' value=">">></option>';
		var selectionne = ((valeur.length && valeur[i].signe == ">=") ? 'selected="selected"' : '' );  html += '<option ' + selectionne + ' value=">=">>=</option>';
		html += '</select>' ;

		html += '&nbsp;<input id="val1'+row+'" data-entry="val" name="'+nomVal1+'[]" type="text" size="2" value="'+( valeur.length>0 ? valeur[i].val1 : "")+'">';
		html += '&nbsp;et&nbsp;<input id="val2'+row+'" data-entry="val" name="'+nomVal2+'[]" type="text" size="2" value="'+( valeur.length>0 ? valeur[i].val2 : "")+'">';
		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}


EffetAuto.ChampMeca = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomMecaCod = "obj_fonc_" + parametre.nom + numero.toString()+"_meca_cod";
	var nomPosCod = "obj_fonc_" + parametre.nom + numero.toString()+"_pos_cod";
	var nomSens = "obj_fonc_" + parametre.nom + numero.toString()+"_sens";
	var nomTaux = "obj_fonc_" + parametre.nom + numero.toString()+"_taux";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';
		html += '<select style="max-width: 200px;" name="' + nomMecaCod + '[]">';
		html += EffetAuto.CopieListe ('liste_meca_modele',  valeur.length ? valeur[i].meca_cod : "");
		html += '</select>';

		html += '<select style="max-width: 80px;" name="' + nomSens + '[]">';
		var selectionne = ((valeur.length && valeur[i].sens == "0") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="0">Active</option>';
		var selectionne = ((valeur.length && valeur[i].sens == "-1") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="-1">Désactive</option>';
		var selectionne = ((valeur.length && valeur[i].sens == "2") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="2">Inverse</option>';
		html += '</select>' ;

		html += '&nbsp;<strong>Chance:</strong>&nbsp;<input name="'+nomTaux+'[]" type="text" size="3" value="'+( valeur.length>0 ? valeur[i].taux : "")+'">%<br>';

		html += '<strong>Position:</strong>&nbsp;<span title="Position ciblée pour les mecanismes individuels (facultatif, mettre -1 pour cibler toutes les positions).">';
		html += '<input data-entry="val" id="row-'+numero+'-'+i+'-pos_cod" name="'+nomPosCod+'[]" type="text" size="4" value="'+( valeur.length>0 ? valeur[i].pos_cod : "")+'">';
		html += '</span>&nbsp';
		html += '<span style="display:none;" data-entry="text" id="row-'+numero+'-'+i+'-pos_nom"></span>&nbsp';
		html += '<input type="button" class="test" value="rechercher" onclick="getTableCod(\'row-'+numero+'-'+i+'-pos\',\'position\',\'Rechercher une position\',[\'\',\'\',\'\']);">';

		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';

		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}

EffetAuto.ChampEAEtage = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomEACod = "obj_fonc_" + parametre.nom + numero.toString()+"_ea_cod";
	var nomRearm = "obj_fonc_" + parametre.nom + numero.toString()+"_rearme";
	var nomTaux = "obj_fonc_" + parametre.nom + numero.toString()+"_taux";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';
		html += '<select style="max-width: 200px;" name="' + nomEACod + '[]">';
		html += EffetAuto.CopieListe ('liste_ea_modele',  valeur.length ? valeur[i].ea_cod : "");
		html += '</select>';

		html += '&nbsp;ré-arm.:<select style="max-width: 80px;" name="' + nomRearm + '[]">';
		var selectionne = ((valeur.length && valeur[i].rearme == "0") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="0">Toujours</option>';
		var selectionne = ((valeur.length && valeur[i].rearme == "1") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="1">Une seule fois</option>';
		var selectionne = ((valeur.length && valeur[i].rearme == "2") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="2">Bascule (case)</option>';
		var selectionne = ((valeur.length && valeur[i].rearme == "3") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="3">Bascule (grappe)</option>';
		var selectionne = ((valeur.length && valeur[i].rearme == "-1") ? 'selected="selected"' : '' ); html += '<option ' + selectionne + ' value="-1">Jamais</option>';
		html += '</select>' ;


		html += '&nbsp;<strong>Chance:</strong>&nbsp;<input name="'+nomTaux+'[]" type="text" size="3" value="'+( valeur.length>0 ? valeur[i].taux : "")+'">%<br>';

		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';

		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}


EffetAuto.ChampPositions = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomPosCod = "obj_fonc_" + parametre.nom + numero.toString()+"_pos_cod";
	var nomTaux = "obj_fonc_" + parametre.nom + numero.toString()+"_taux";
	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';

		html += '&nbsp;<strong>Chance:</strong>&nbsp;<input name="'+nomTaux+'[]" type="text" size="3" value="'+( valeur.length>0 ? valeur[i].taux : "")+'">% ';

		html += '<strong>Position:</strong>&nbsp;<span title="Position ciblée pour les mecanismes individuels (facultatif, mettre -1 pour cibler toutes les positions)">';
		html += '<input data-entry="val" id="row-'+numero+'-'+i+'-pos_cod" name="'+nomPosCod+'[]" type="text" size="4" value="'+( valeur.length>0 ? valeur[i].pos_cod : "")+'">';
		html += '</span>&nbsp';
		html += '<span style="display:none;" data-entry="text" id="row-'+numero+'-'+i+'-pos_nom"></span>&nbsp';
		html += '<input type="button" class="test" value="rechercher" onclick="getTableCod(\'row-'+numero+'-'+i+'-pos\',\'position\',\'Rechercher une position\',[\'\',\'\',\'\']);">';

		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';

		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}

EffetAuto.ChampListeBM2 = function (parametre, numero, valeur) {
	if (!valeur) valeur = []; else if (typeof valeur == "string") valeur=JSON.parse(valeur);
	var base = "fonc_" + parametre.nom + numero.toString();
	var nomBM = "obj_fonc_" + parametre.nom + numero.toString()+"_tbonus_libc";
	var nomStandard = "obj_fonc_" + parametre.nom + numero.toString()+"_standard";
	var checkboxStandard = "obj_check_" + parametre.nom + numero.toString()+"_standard";
	var nomCumulatif = "obj_fonc_" + parametre.nom + numero.toString()+"_cumulatif";
	var checkboxCumulatif = "obj_check_" + parametre.nom + numero.toString()+"_cumulatif";
	var nomBonus = "obj_fonc_" + parametre.nom + numero.toString()+"_bonus";
	var checkboxBonus = "obj_check_" + parametre.nom + numero.toString()+"_bonus";
	var nomMalus = "obj_fonc_" + parametre.nom + numero.toString()+"_malus";
	var checkboxMalus = "obj_check_" + parametre.nom + numero.toString()+"_malus";

	var label = "div_" + parametre.nom + numero.toString();

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;:</label><table>' ;

	for (var i=0; i < valeur.length || i==0 ; i++)
	{
		var row='-row-'+numero+'-'+i+'-';
		var rowStandardNom = nomStandard+row;
		var rowStandardCheck = checkboxStandard+row;
		var rowCumulatifNom = nomCumulatif+row;
		var rowCumulatifCheck = checkboxCumulatif+row;
		var rowBonusNom = nomBonus+row;
		var rowBonusCheck = checkboxBonus+row;
		var rowMalusNom = nomMalus+row;
		var rowMalusCheck = checkboxMalus+row;
		html +=  '<tr  id="row-'+numero+'-'+i+'-"><td>';
		html += '<input type="hidden" name="' + base + '[]">';
		html += '<select style="max-width: 200px;" name="' + nomBM + '[]"><optgroup label="Massivement">';

		// Option Spécifique : tous les BM / nettoyables ou non-nettoyables
		var selectionne = ((valeur.length && valeur[i].tbonus_libc == "--0") ? 'selected="selected"' : '' );
		html += '<option value="--0" ' + selectionne + '>Tous les Bonus et Malus</option>';
		var selectionne = ((valeur.length && valeur[i].tbonus_libc == "--1") ? 'selected="selected"' : '' );
		html += '<option value="--1" ' + selectionne + '>Bonus et Malus Nettoyables</option>';
		var selectionne = ((valeur.length && valeur[i].tbonus_libc == "--2") ? 'selected="selected"' : '' );
		html += '<option value="--2" ' + selectionne + '>Bonus et Malus Non-Nettoyables</option>';
		var selectionne = ((valeur.length && valeur[i].tbonus_libc == "--3") ? 'selected="selected"' : '' );
		html += '<option value="--3" ' + selectionne + '>Bonus et Malus Compteurs</option></optgroup><optgroup label="Standards">';


		html += EffetAuto.CopieListe ('liste_bm_modele',  valeur.length ? valeur[i].tbonus_libc : "");
		html += '</optgroup></select>&nbsp;<br>' ;
		html += '<input data-entry="O" id="'+rowBonusNom+'"  name="'+nomBonus+'[]" type="hidden" value="'+( valeur.length>0 ? valeur[i].bonus : "O")+'">';
		html += 'Bonus:<input data-entry="checked" onchange="EffetAuto.CheckBoxChange(\''+rowBonusCheck+'\', \''+rowBonusNom+'\');" id="'+rowBonusCheck+'" name="'+checkboxBonus+'[]" type="checkbox" '+( valeur.length>0 ? (valeur[i].bonus =='O' ? ' checked' : '') : 'checked')+'>&nbsp;';
		html += '<input data-entry="O" id="'+rowMalusNom+'"  name="'+nomMalus+'[]" type="hidden" value="'+( valeur.length>0 ? valeur[i].malus : "O")+'">';
		html += 'Malus:<input data-entry="checked" onchange="EffetAuto.CheckBoxChange(\''+rowMalusCheck+'\', \''+rowMalusNom+'\');" id="'+rowMalusCheck+'" name="'+checkboxMalus+'[]" type="checkbox" '+( valeur.length>0 ? (valeur[i].malus =='O' ? ' checked' : '') : 'checked')+'>&nbsp;';
		html += '<input data-entry="O" id="'+rowStandardNom+'"  name="'+nomStandard+'[]" type="hidden" value="'+( valeur.length>0 ? valeur[i].standard : "O")+'">';
		html += 'Standard:<input data-entry="checked" onchange="EffetAuto.CheckBoxChange(\''+rowStandardCheck+'\', \''+rowStandardNom+'\');" id="'+rowStandardCheck+'" name="'+checkboxStandard+'[]" type="checkbox" '+( valeur.length>0 ? (valeur[i].standard =='O' ? ' checked' : '') : 'checked')+'>&nbsp;';
		html += '<input data-entry="N" id="'+rowCumulatifNom+'"  name="'+nomCumulatif+'[]" type="hidden" value="'+( valeur.length>0 ? valeur[i].cumulatif : "N")+'">';
		html += 'Cumulatif:<input data-entry="unchecked" onchange="EffetAuto.CheckBoxChange(\''+rowCumulatifCheck+'\', \''+rowCumulatifNom+'\');" id="'+rowCumulatifCheck+'" name="'+checkboxCumulatif+'[]" type="checkbox" '+( valeur.length>0 ? (valeur[i].cumulatif =='O' ? ' checked' : '') : '')+'><br>';

		html +=  '</td><td><input type="button" class="test" value="Supprimer" onclick="EffetAuto.delItem($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
		html += '</tr>';
	}
	html += '<tr id="add-row-'+numero+'-0-" style="display: block;"><td><input type="button" class="test" value="Nouveau" onclick="EffetAuto.addItem($(this).parent(\'td\').parent(\'tr\').prev(), 0);"></td></tr>';
	html += '</table>';
	return html;
}

EffetAuto.ChampMonstre = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += EffetAuto.CopieListe ('liste_monstre_modele', valeur);
	html += '</select></label>';
	return html;
}

EffetAuto.ChampBM = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += EffetAuto.CopieListe ('liste_bm_modele', valeur);
	html += '</select></label><br />';
	html += "(+) Une valeur <strong>positive</strong> est <strong>bénéfique</strong>, et une valeur <strong>négative</strong> est <strong>délétère</strong><br />";
	html += "(-) Une valeur <strong>positive</strong> est <strong>délétère</strong>, et une valeur <strong>négative</strong> est <strong>bénéfique</strong>";
	return html;
}

EffetAuto.ChampBMCompteur = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += EffetAuto.CopieListe ('liste_bmc_modele', valeur);
	html += '</select></label><br />';
	return html;
}

EffetAuto.ChampSort = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += EffetAuto.CopieListe ('liste_sort_modele', valeur);
	html += '</select></label><br />';
	return html;
}


EffetAuto.ChampCheckBox = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 'N';
	var html = 	'<label><strong>' + parametre.label + '</strong>&nbsp;' +
		       	'<input type="hidden" valeur="'+valeur+'" name="checkbox_fonc_' + parametre.nom + numero.toString() + '">' +
		       	'<input type="checkbox" '+ (valeur=='O' ? 'checked' : '') +' name="fonc_' + parametre.nom + numero.toString() + '">' +
				'</label><br />';
	return html;
}

EffetAuto.ChampCheckBoxInv = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 'O';
	var html = 	'<label><strong>' + parametre.label + '</strong>&nbsp;' +
		       	'<input type="hidden" valeur="'+valeur+'" name="checkbox_fonc_' + parametre.nom + numero.toString() + '">' +
		       	'<input type="checkbox" '+ (valeur=='O' ? 'checked' : '') +' name="fonc_' + parametre.nom + numero.toString() + '">' +
				'</label><br />';
	return html;
}

EffetAuto.ChampCumulatif = function (parametre, numero, valeur) {
	if (!valeur) valeur = 'N';

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<input type="checkbox" '+ (valeur!='N' ? 'checked' : '') +' name="fonc_' + parametre.nom + numero.toString() + '">';

	if (valeur!='N' && valeur!='O') {
		html += '&nbsp; Progressivité: '+ valeur ;
	}
	html += '</label><br />';
	return html;
}


EffetAuto.ChampEA = function (parametre, numero, valeur) {
	if (!valeur) valeur = -1;		// par défaut pas de fonc_cod associé

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;';
	html+= '<input type="hidden" value="'+valeur+'" name="fonc_' + parametre.nom + numero.toString() + '">' ;
	html+= '<input id="ea_implantation'+numero+'" type="hidden" value="" name="ea_implantation' + numero.toString() + '">' ;
	html+= '<div id="ea-container-'+numero+'" data-child-id="'+valeur+'" data-child-numero=""></div>';
	html += '</label><br />';
	return html;
}


EffetAuto.ChampProba = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = "";

	var html = '<strong>&nbsp; Chainage</strong>&nbsp;<span title="Chainage des EA: un EA sans chainage ou avec un chainage d’ordre 1 sera toujours traité (suivant sa proba), un EA avec un chainage d’ordre 2 ne sera traité (avec sa proba) que si au moins un chainage d’ordre 1 s’est déclenché sur un même évènement. Et ainsi de suite...">';
	html += '<select name="fonc_' + parametre.nom + numero.toString() +'">';
	html += '<option value="0" ' + ((valeur == 0) ? 'selected="selected"' : '' ) + '>Sans Chainage</option>';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Chainage ordre 1</option>';
	html += '<option value="2" ' + ((valeur == 2) ? 'selected="selected"' : '' ) + '>Chainage ordre 2</option>';
	html += '<option value="3" ' + ((valeur == 3) ? 'selected="selected"' : '' ) + '>Chainage ordre 3</option></select></span>';

	return html;

}



EffetAuto.Supprime = function (id, numero, silence) {

	var ok = silence ? true : confirm('Êtes-vous sûr de vouloir supprimer cette fonction ?');
	if (ok) {

		// S'il s'agit d'un EA d'implantation on supprime aussi le ou les fils.
		if ($('#ea-container-' + numero).length>0) {
			EffetAuto.SupprimeEAChild('#ea-container-' + numero);
		}

		if (id != -1) {
			document.getElementById('fonctions_supprimees').value += ',' + id.toString();
			document.getElementById('fonction_id_' + id).style.display = 'none';
		}
		else {
			document.getElementById('fonctions_annulees').value += ',' + numero.toString();
			document.getElementById('fonction_num_' + numero).style.display = 'none';
		}
		EffetAuto.EnleveValidation (numero, 'trigger');
		EffetAuto.EnleveValidation (numero, 'effet');
	}
}

EffetAuto.EcritLigneFormulaire = function (parametre, numero, valeur, modifiable) {

	if (parametre.paragraphe && parametre.paragraphe=="divd") {
		var pd = '<p style="padding: 1px; margin: 1px;"><span title="' + parametre.description + '">';
		var pf = '</span>';
	} else 	if (parametre.paragraphe && parametre.paragraphe=="div") {
		var pd = '&nbsp;<span title="' + parametre.description + '">';
		var pf = '</span>';
	} else 	if (parametre.paragraphe && parametre.paragraphe=="divf") {
		var pd = '&nbsp;<span title="' + parametre.description + '">';
		var pf = '</span></p>';
	} else {
		var pd = '<p style="padding: 1px; margin: 1px;" title="' + parametre.description + '">';
		var pf = '</p>';
	}

	var html = '';
	var type = parametre.type;
	if (typeof modifiable === "undefined")
		modifiable = true;
	if (!modifiable && type != 'hidden')
		type = 'lecture';
	switch (type) {
		case 'entier':
		case 'numerique':
			parametre.longueur = 4;
			html = pd + EffetAuto.ChampTexte(parametre, numero, valeur) + pf;
			break;
		case 'texte':
			html = pd + EffetAuto.ChampTexte(parametre, numero, valeur) + pf;
			break;
		case 'textearea':
			html = pd + EffetAuto.ChampTexteArea(parametre, numero, valeur) + pf;
			break;
		case 'lecture':
			if (typeof parametre.valeur !== "undefined")
				valeur = parametre.valeur;
			html = pd + EffetAuto.ChampLecture(parametre, numero, valeur) + pf;
			break;
		case 'ea':
			html = pd + EffetAuto.ChampEA(parametre, numero, valeur) + pf;
			break;
		case 'proba':
			html = pd + EffetAuto.ChampProba(parametre, numero, valeur) + pf;
			break;
		case 'sens-projection':
			html = pd + EffetAuto.ChampChoixSensProjection(parametre, numero, valeur) + pf;
			break;
		case 'point-cardinal':
			html = pd + EffetAuto.ChampChoixPointCardinal(parametre, numero, valeur) + pf;
			break;
		case 'BM':
			html = pd + EffetAuto.ChampBM(parametre, numero, valeur) + pf;
			break;
		case 'BMCompteur':
			html = pd + EffetAuto.ChampBMCompteur(parametre, numero, valeur) + pf;
			break;
		case 'Sort':
			html = pd + EffetAuto.ChampSort(parametre, numero, valeur) + pf;
			break;
		case 'perso-condition':
			html = pd + EffetAuto.ChampListePersoCondition (parametre, numero, valeur) + pf;
			break;
		case 'BMCsens':
			html = pd + EffetAuto.ChampChoixBMCsens (parametre, numero, valeur) + pf;
			break;
		case 'POSsens':
			html = pd + EffetAuto.ChampChoixSensDeplacement (parametre, numero, valeur) + pf;
			break;
		case 'POSNomEtage':
			html = pd + EffetAuto.ChampChoixNomEtage (parametre, numero, valeur) + pf;
			break;
		case 'POStype':
			html = pd + EffetAuto.ChampChoixTypeEA (parametre, numero, valeur) + pf;
			break;
		case 'POSrearme':
			html = pd + EffetAuto.ChampChoixRearmement (parametre, numero, valeur) + pf;
			break;
		case 'POSDrop':
			html = pd + EffetAuto.ChampChoixDrop (parametre, numero, valeur) + pf;
			break;
		case 'ExpMsg':
			html = pd + EffetAuto.ChampExpediteurMessage (parametre, numero, valeur) + pf;
			break;
		case 'meca':
			html = pd + EffetAuto.ChampMeca (parametre, numero, valeur) + pf;
			break;
		case 'ea_etage':
			html = pd + EffetAuto.ChampEAEtage (parametre, numero, valeur) + pf;
			break;
		case 'positions':
			html = pd + EffetAuto.ChampPositions (parametre, numero, valeur) + pf;
			break;
		case 'PVsens':
			html = pd + EffetAuto.ChampChoixPVsens (parametre, numero, valeur) + pf;
			break;
		case 'sante':
			html = pd + EffetAuto.ChampChoixSante (parametre, numero, valeur) + pf;
			break;
		case 'MAGeffet':
			html = pd + EffetAuto.ChampChoixMAGeffet (parametre, numero, valeur) + pf;
			break;
		case 'objet':
			html = pd + EffetAuto.ChampListeObjet (parametre, numero, valeur) + pf;
			break;
		case 'transac':
			html = pd + EffetAuto.ChampTransac (parametre, numero, valeur) + pf;
			break;
		case 'drop':
			html = pd + EffetAuto.ChampDropObjet (parametre, numero, valeur) + pf;
			break;
		case 'invocation':
			html = pd + EffetAuto.ChampInvocationMonstre (parametre, numero, valeur) + pf;
			break;
		case 'listebm':
			html = pd + EffetAuto.ChampListeBM (parametre, numero, valeur) + pf;
			break;
		case 'listebm2':
			html = pd + EffetAuto.ChampListeBM2 (parametre, numero, valeur) + pf;
			break;
		case 'checkbox':
			html = pd + EffetAuto.ChampCheckBox(parametre, numero, valeur) + pf;
			break;
		case 'checkboxinv':
			html = pd + EffetAuto.ChampCheckBoxInv(parametre, numero, valeur) + pf;
			break;
		case 'cumulatif':
			html = pd + EffetAuto.ChampCumulatif(parametre, numero, valeur) + pf;
			break;
		case 'monstre':
			html = pd + EffetAuto.ChampMonstre(parametre, numero, valeur) + pf;
			break;
		case 'cible':
			html = pd + EffetAuto.ChampCible(parametre, numero, valeur) + pf;
			break;
		case 'cible-case':
			html = pd + EffetAuto.ChampCibleCase(parametre, numero, valeur) + pf;
			break;
		case 'vorpale':
			html = pd + EffetAuto.ChampCibleVorpale(parametre, numero, valeur) + pf;
			break;
		case 'choix_rouille':
			html = pd + EffetAuto.ChampChoixRouille(parametre, numero, valeur) + pf;
			break;
		case 'validite':
			html = pd + EffetAuto.ChampValidite(parametre, numero, valeur) + pf;
			break;
		case 'hidden':
			html = EffetAuto.ChampCache(parametre, numero, valeur);
			break;
		default:
			html = 'Type de données ' + type + ' inconnu !';
			break;
	}

	return html;
}

EffetAuto.EcritBoutonSupprimer = function (id, numero) {
	var texte = (id == -1) ? 'Annuler' : 'Supprimer';
	return '<a id="del-button-'+numero+'" onclick="EffetAuto.Supprime(' + id.toString() + ', ' + numero.toString() + '); return false;">' + texte + '</a>';
}

EffetAuto.EcritEffetAutoExistant = function (declenchement, type, id, force, duree, message, effet, cumulatif, proba, cible, portee, nombre, trigger_param, validite, heritage, implantation) {
	console.log('debut function EffetAuto.EcritEffetAutoExistant');
	EffetAuto.num_courant += 1;
	EffetAuto.Champs[EffetAuto.num_courant] = [];

	var conteneur = document.getElementById("liste_fonctions");
	if (implantation) {
		// en cas d'un EA implanté on va chercher le conteneur cible
		var container = $("div[id^='ea-container-'][data-child-id="+id+"]") ;
		conteneur = document.getElementById( container.attr("id") );
		container.data("child-numero", EffetAuto.num_courant) ; //memoriser l'id du fils dans le pere
		var numero_pere = container.attr("id").substr(13);
		$('#ea_implantation' + numero_pere).val(EffetAuto.num_courant) ; //memoriser l'id du fils dans le pere
	}

	var divEA = document.createElement("div");
	divEA.id = 'fonction_id_' + id;
	divEA.className = 'bordiv';
	divEA.style.cssFloat = 'left';
	divEA.style.maxWidth = '500px';
	divEA.style.overflow = 'auto';

	var donnees = EffetAuto.getDonnees(type);
	donnees.declenchement = EffetAuto.Triggers[declenchement] ?  EffetAuto.Triggers[declenchement].description : 'Déclenchement inconnu...';

	var html = '';
	if (heritage)
		html += EffetAuto.EcritLigneFormulaire({ label: 'Hérité de son type de monstre', type: 'lecture', valeur: '', description: '' }, EffetAuto.num_courant);
	html += EffetAuto.EcritLigneFormulaire({ label: 'Déclenchement', type: 'lecture', valeur: donnees.declenchement, description: '' }, EffetAuto.num_courant);

	// Paramètre du déclencheur
	var trig = EffetAuto.Triggers[declenchement];
	if ( trig.parametres ) {
		for (var i = 0; i < trig.parametres.length; i++) {
			html += EffetAuto.EcritLigneFormulaire(trig.parametres[i], EffetAuto.num_courant, trigger_param["fonc_"+trig.parametres[i].nom] ? trigger_param["fonc_"+trig.parametres[i].nom] : '', !heritage);
		}
	}
	if ( trig.remarque ) {
		html += trig.remarque ;
	}

	if (EffetAuto.MontreValidite && !heritage) {
		var desc = 'Durée pendant laquelle cet effet automatique peut être déclenché. 0 si aucune limite.';
		var texte = (validite == 0) ? 'illimitée' : '' + validite + ' minutes';
		var obj_validite = { nom: 'validite', label: 'Validité', type: 'validite', valeur: texte, validation: Validation.Types.Entier, description: desc };
		html += EffetAuto.EcritLigneFormulaire(obj_validite, EffetAuto.num_courant, validite, donnees.modifiable);
	}
	html += "<hr>";

    html += EffetAuto.EcritLigneFormulaire({ label: 'EffetAuto', type: 'lecture', valeur: donnees.affichage, description: donnees.description }, EffetAuto.num_courant);
    html += EffetAuto.EcritLigneFormulaire({ nom: 'id', type: 'hidden', description: '' }, EffetAuto.num_courant, id);

	if (donnees.parametres) for (var i = 0; i < donnees.parametres.length; i++) {
		var valeur;
		switch (donnees.parametres[i].nom) {
			case 'force': valeur = force; break;
			case 'duree': valeur = duree; break;
			case 'effet': valeur = effet; break;
			case 'cumulatif': valeur = cumulatif; break;
			case 'proba': valeur = proba; break;
			case 'cible': valeur = cible; break;
			case 'portee': valeur = portee; break;
			case 'nombre': valeur = nombre; break;
			case 'message': valeur = message; break;
			default:
				valeur = trigger_param["fonc_"+donnees.parametres[i].nom] ? trigger_param["fonc_"+donnees.parametres[i].nom] : '' ;
				break;
		}
		html += EffetAuto.EcritLigneFormulaire(donnees.parametres[i], EffetAuto.num_courant, valeur, donnees.modifiable && !heritage);
	}
	if (donnees.modifiable && !heritage) {
		html += EffetAuto.EcritBoutonSupprimer(id, EffetAuto.num_courant);
		document.getElementById('fonctions_existantes').value += ',' + EffetAuto.num_courant.toString();
	}
	divEA.innerHTML = html;
	conteneur.appendChild (divEA);
	if (implantation) {
		// en cas d'un EA implanté on supprime le boutton suppression (elle est supprimé à l'aide du pere)
		$('#del-button-' + EffetAuto.num_courant).hide();
		$('#champ-validite-' + EffetAuto.num_courant).hide();
	}

	EffetAuto.setMultiSelect();
}

EffetAuto.EcritNouvelEffetAuto = function (type, numero) {
	var donnees = EffetAuto.getDonnees(type);
	var html = '';

	for (var i = 0; i < donnees.parametres.length; i++) {
		html += EffetAuto.EcritLigneFormulaire(donnees.parametres[i], numero);
	}
	html += EffetAuto.EcritBoutonSupprimer(-1, numero);
	return html;
}


EffetAuto.EcritNouveauDeclencheurAuto = function (type, numero) {
	var donnees = EffetAuto.Triggers[type];
	var html = '';
	var desc = 'Durée pendant laquelle cet effet automatique peut être déclenché. 0 si aucune limite.';
	var texte = 'illimitée';
	var obj_validite = { nom: 'validite', label: 'Validité', type: 'validite', valeur: texte, validation: Validation.Types.Entier, description: desc };
	html += EffetAuto.EcritLigneFormulaire(obj_validite, numero);

	if ( donnees.parametres ) {
		for (var i = 0; i < donnees.parametres.length; i++) {
			html += EffetAuto.EcritLigneFormulaire(donnees.parametres[i], numero);
		}
	}
	if ( donnees.remarque ) {
		html += donnees.remarque ;
	}

	html += '<hr>'
	return html;
}

EffetAuto.SupprimeEAChild = function (container) {
	if ($('#ea-container-' + $(container).data("child-numero")).length>0) { // s'il avait un enfant on le supprime d'abord
		EffetAuto.SupprimeEAChild('#ea-container-' + $(container).data("child-numero"));
	}
	EffetAuto.Supprime( $(container).data("child-id"), $(container).data("child-numero"), true );
}

EffetAuto.ChangeEffetAuto = function (type, numero) {
	EffetAuto.EnleveValidation(numero, 'effet');

	// S'il s'agissait d'une implantation d'EA on lui supprime son EA attachée (et toute la descendance s'il y en a)
	if ($('#ea-container-' + numero).length>0) {
		EffetAuto.SupprimeEAChild('#ea-container-' + numero);
	}

	document.getElementById('formulaire_fonction_' + numero).innerHTML = EffetAuto.EcritNouvelEffetAuto(type, numero);

	$("div[id^='ea-container-']").each(function() {
		// si on a changé l'effet d'une EA implanté, il faut supprimer son bouton annulé
		if ( $(this).data("child-numero") == numero) {
			$('#del-button-' + numero).hide();
			$('#champ-validite-' + numero).hide();
		}
	});

	// capture et alimentation des EA définie dans une EA
	if ($('#ea-container-' + numero).length>0) {
		EffetAuto.NouvelEffetAuto('ea-container-' + numero);
		$('#del-button-' + EffetAuto.num_courant).hide();
		$('#champ-validite-' + EffetAuto.num_courant).hide();
		$('#ea-container-' + numero).data("child-numero", EffetAuto.num_courant) ; //memoriser l'id du fils dans le pere
		$('#ea_implantation' + numero).val(EffetAuto.num_courant) ; //memoriser l'id du fils dans le pere
	}

	EffetAuto.setMultiSelect ();
	Validation.Valide ();
}

EffetAuto.setMultiSelect = function () {
	//Enjoliver le selecteur de race multiple
	$("select[id^='multi-select-']").each(function () {
		if (! $(this).hasClass("ms-offscreen") ) {
			$(this).multipleSelect({
				width: '350px',
				multiple: true,
				filter: true,
				multipleWidth: 150,
				minimumCountSelected: 6,
				countSelected: '&nbsp;&nbsp;# races sélectionnées',
				animate: "slide",
				selectAll: false,
				maxHeight: 65,
				showClear: true,
			});
		}
	});
}

EffetAuto.EnleveValidation = function (numero, type) {
	for (var i = 0; i < EffetAuto.Champs[numero].length; i++) {
		Validation.Enleve (EffetAuto.Champs[numero][i], type);
	}
};

EffetAuto.NouvelEffetAuto = function (container) {
	EffetAuto.num_courant += 1;
	var numero = EffetAuto.num_courant;
	EffetAuto.Champs[numero] = [];

	var conteneur = container ?  document.getElementById(container)  : document.getElementById("liste_fonctions");
	var divEA = document.createElement("div");
	divEA.id = 'fonction_num_' + numero;
	divEA.className = 'bordiv';
	divEA.style.cssFloat = 'left';
	divEA.style.maxWidth = '500px';
	divEA.style.overflow = 'auto';

	document.getElementById('fonctions_ajoutees').value += ',' + numero.toString();
	var html = '';

	var declenchement = '<select name="declenchement_' + numero + '" onchange="EffetAuto.remplirListe(this.value, ' + numero + ');">';
	$.each(EffetAuto.Triggers, function( d ) {
		declenchement += '<option value="'+d+'">'+EffetAuto.Triggers[d].declencheur+'</option>' ;
	});
	declenchement += '</select>';

	var liste = '<select name="fonction_type_' + numero + '" id="fonction_type_' + numero + '" onchange="EffetAuto.ChangeEffetAuto(this.value, ' + numero + ');"></select>';
	html += 'Se déclenche lorsque le perso... ' + declenchement ;
	html += '<div id="formulaire_declenchement_' + numero + '"></div>'  + liste;
	html += '<div id="formulaire_fonction_' + numero + '"></div>';

	divEA.innerHTML = html;
	conteneur.appendChild (divEA);

	// récupérer le premier élément de la liste des triggers par défaut (sur nouvel EA)
	EffetAuto.remplirListe(Object.keys(EffetAuto.Triggers)[0], numero);
	EffetAuto.ChangeEffetAuto('deb_tour_generique', numero);
}
