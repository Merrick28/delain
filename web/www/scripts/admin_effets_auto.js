var EffetAuto = {num_courant: 0};
EffetAuto.Champs = [];
EffetAuto.MontreValidite = false;

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
			remarque: "<br><strong><u>ATTENTION</u></strong>: Il n'y a pas de ciblage sur  <u>le protagoniste</u> pour ce déclencheur.",
			parametres: [
				{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
				{ nom: 'trig_compteur', type: 'BMCompteur', label: 'Décencheur', description: 'Le compteur.' },
				{ nom: 'trig_sens', type: 'BMCsens', label: 'Sens de déclemement', description: 'Dépassement lorsque le Bonus/Malus dépasse le seuil ou lorsqu’il retombe en dessous.' },
				{ nom: 'trig_seuil', type: 'entier', label: 'Seuil du Bonus/Malus', description: 'Valeur de déclenement du Bonus.', ValidationTrigger:true, validation: Validation.Types.Entier },
				{ nom: 'trig_raz', type: 'checkbox', label: 'Remise à zéro du BM après déclenchement', description: 'Cocher pour remettre le BM à 0 après déclenchement'},
				{ nom: 'trig_nom', type: 'texte', longueur: 30, label: 'Changement du nom', description: 'Nouveau nom du monstre en cas de basculement de seuil, laisser vide pour ne faire aucun changement. (utiliser les tags [nom] pour le nom actuel du monstre ou [nom_generique] pour son nom de base)'},
			]
	},
	"DEP": {description: "lorsqu’il se déplace.",
		default:'deb_tour_generique',
		declencheur:'Se déplace',
		remarque: "<br><strong><u>ATTENTION</u></strong>: Il n'y a pas de ciblage sur  <u>le protagoniste</u> pour ce déclencheur.",
		parametres: [
			{ nom: 'trig_deda', type: 'entier', label: 'Délai entre 2 déclenchements', description: 'C’est le temps minimum (en minutes) entre 2 déclenchements d’actions.' , ValidationTrigger:true, validation: Validation.Types.EntierOuVide },
		]
	},
	"MAL": {description: "lorsqu’il lance un sort.",
		default:'deb_tour_generique',
		declencheur:'Lance un sort',
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
		affichage: 'Bonus / Malus standard',
		description: 'Applique un Bonus / Malus standard, à une ou plusieurs cibles.',
		parametres: [
			{ nom: 'effet', type: 'BM', label: 'Effet', description: 'Le bonus/malus qui doit être appliqué.' },
			{ nom: 'cumulatif', type: 'cumulatif', label: 'Cumulatif', description: 'Sera ignoré si le bonus/malus n’est pas [cumulable].' },
			{ nom: 'force', type: 'texte', longueur: 5, label: 'Valeur', description: 'La force du bonus / malus appliqué : valeur fixe ou de la forme 1d6+2', validation: Validation.Types.Roliste },
			{ nom: 'duree', type: 'entier', label: 'Durée', description: 'La durée de l’effet.', validation: Validation.Types.Entier },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' }
		],
	},
	{	nom: 'titre',
		debut: false,
		tueur: true,
		mort: true,
		attaque: false,
		modifiable: true,
		bm_compteur: true,
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
		affichage: 'Dégâts (ou soins)',
		description: 'Inflige des dégâts aux cibles données (pour une valeur positive ; ou des soins pour une valeur négative).',
		parametres: [
			{ nom: 'force', type: 'entier', longueur: 2, label: 'Puissance', description: 'Le nombre de PV impactés par l’effet, valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'nombre', type: 'texte', longueur: 5, label: 'Nombre de cibles', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
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
		affichage: 'Invocation sur l’étage',
		description: 'Invoque plusieurs monstres du type donné, quelque part sur le même étage. Typiquement, les golems lors de leur mort.',
		parametres: [
			{ nom: 'proba', type: 'entier', label: 'Probabilité', description: 'La probabilité, de 0 à 100.', validation: Validation.Types.Entier },
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
		affichage: 'Mélange voies magiques',
		description: 'Modifie de façon permanente la voie magique des cibles (animation halloween).',
		parametres: [
			{ nom: 'cible', type: 'lecture', valeur: 'Aventurier uniquement', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'lecture', valeur: 0, label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'entier', label: 'Nombre de cibles', description: 'Le nombre de cibles.', validation: Validation.Types.Entier },
			{ nom: 'proba', type: 'entier', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Entier },
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
		affichage: 'Usure d’objets',
		description: 'Use un objet équipé par un aventurier ou un familier.',
		parametres: [
			{ nom: 'effet', type: 'choix_rouille', label: 'Message', description: 'Le message inscrit dans les événements de la cible.' },
			{ nom: 'force', type: 'entier', longueur: 3, label: 'Usure', description: 'Le nombre de points d’usure soustraits à l’objet (100 -> l’objet, même comme neuf, est instantanément détruit).', validation: Validation.Types.Entier },
			{ nom: 'cible', type: 'lecture', valeur: 'Aventurier ou Familier', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'lecture', valeur: 0, label: 'Portée', description: 'La portée de l’effet: -1 pour tout l’étage.' },
			{ nom: 'nombre', type: 'lecture', valeur: 1, label: 'Nombre de cibles', description: 'Le nombre de cibles.' },
			{ nom: 'proba', type: 'entier', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (nombre entier).', validation: Validation.Types.Entier }
		],
	},
	{	nom: 'ea_supprime_bm',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		affichage: 'Suppression BM',
		description: 'Supprime les effets d’un Bonus/Malus.',
		parametres: [
			{ nom: 'effet', type: 'BM', label: 'Effet', description: 'Le bonus/malus qui doit être supprimé.' },
			{ nom: 'trig_supp_bm_standard', type: 'checkbox', label: 'Supprimer les BM Standards', description: 'Cocher pour supprimer les BM Standards'},
			{ nom: 'trig_supp_bm_cumulatif', type: 'checkbox', label: 'Supprimer les BM Cumulatifs', description: 'Cocher pour supprimer les BM Cumulatifs'},
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' }
		],
	},
	{	nom: 'ea_lance_sort',
		debut: true,
		tueur: true,
		mort: true,
		attaque: true,
		modifiable: true,
		bm_compteur: true,
		affichage: 'Lance un sort',
		description: 'Lance un sort sur une ou plusieurs cibles, si le sort est un sort de case, il sera lancé sur la case de la ou des cibles.',
		parametres: [
			{ nom: 'effet', type: 'Sort', label: 'Effet', description: 'Le bonus/malus qui doit être appliqué.' },
			{ nom: 'cible', type: 'cible', label: 'Ciblage', description: 'Le type de cible sur lesquelles l’effet peut s’appliquer.' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
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
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de cibles', description: 'Le nombre maximal de cibles. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
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
		affichage: 'Laisse tomber un objet',
		description: 'Laisse tomber un objet au sol.',
		parametres: [
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre d’objet', description: 'Le nombre maximal d’objet. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
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
		affichage: 'Invoque des monstres',
		description: 'Génération d’un ou plusieurs monstres dont le type est établit depuis une liste.',
		parametres: [
			{ nom: 'nombre',type: 'texte', longueur: 5, label: 'Nombre de monstre', description: 'Le nombre de monstre à invoquer. Valeur fixe ou de la forme 1d6+2.', validation: Validation.Types.Roliste },
			{ nom: 'proba', type: 'numerique', label: 'Probabilité', description: 'La probabilité, de 0 à 100, de voir l’effet se déclencher (pour l’ensemble des cibles).', validation: Validation.Types.Numerique },
			{ nom: 'cible', type: 'cible-case', label: 'Case de ciblage', description: 'La case est déterminée suivant Le type de cible sur lesquelles l’effet peut s’appliquer (case aléatoire s’il n’y a pas de cible valide au moment de l’invocation).' },
			{ nom: 'trig_races', type: 'vorpale', label: 'Ciblage Vorpale', description: 'Liste de race pour le ciblage du type Vorpale.' },
			{ nom: 'portee', type: 'entier', label: 'Portée:', paragraphe:'divd', description: 'La portée de l’effet: -1 pour tout l’étage.', validation: Validation.Types.Entier },
			{ nom: 'trig_min_portee', type: 'entier', label: 'Mini', paragraphe:'div' ,description: 'La portée minimum de l’effet, si défini la cible devra être au de-là de cette distance.', validation: Validation.Types.EntierOuVide },
			{ nom: 'trig_vue', type: 'checkbox', label: 'Limiter à la vue', paragraphe:'divf', description: 'Si coché, le ciblage/portée sera pas limité par la vue du porteur de l’EA.' },
			{ nom: 'message', type: 'texte', longueur: 40, label: 'Message', description: 'Le message apparaissant dans les événements privés (en public, on aura « X a subi un effet de Y »). [attaquant] représente le nom de le perso déclenchant l’EA, [cible] est la cible de l’EA.' },
			{ nom: 'trig_monstre', type: 'invocation', label: 'Liste de monstre', description: 'Liste de monstre generique et taux d’invoation de chacun, chaque tirage invoquera un seul monstre de cette liste.' }
		],
	},
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
		var new_elem = '<tr id="'+new_row+'" style="display: block;">'+elem.html().replace(new RegExp(row,'g'), new_row)+'</tr>';
		$(new_elem).insertAfter(elem);

		//Maintenant que l'élément est inséré, on raz les valeurs parasites qui ont été dupliquées de la précédente entrée
		$('*[id^="'+new_row+'"]').each(function( index ) {
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
		if (fct.modifiable && (fct.bm_compteur && type == 'BMC' || fct.attaque && type == 'MAL' || fct.attaque && type == 'MAC' || fct.debut && type == 'DEP' || fct.debut && type == 'D' || fct.tueur && type == 'T' || fct.mort && type == 'M' || fct.attaque && type == 'A' || fct.attaque && type == 'AE' || fct.attaque && (type == 'AT' || type == 'AC' || type == 'ACE' || type == 'ACT'))) {
			liste.options[liste.options.length] = new Option();
			liste.options[liste.options.length - 1].text = fct.affichage;
			liste.options[liste.options.length - 1].value = fct.nom;
			liste.options[liste.options.length - 1].title = fct.description;
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
	var modele = document.getElementById(listeid);
	for (var i = 0; i < modele.options.length; i++) {
		var opt_v = modele.options[i].value;
		var opt_t = modele.options[i].text;
		var selectionne = ((selection == opt_v) ? 'selected="selected"' : '' );
		options += '<option value="' + opt_v + '" ' + selectionne + '>' + opt_t + '</option>';
	}
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
	
	var resultat = '<label><strong>' + parametre.label + '</strong>&nbsp;<input type="text"' + onChange + onKeyUp + ' value="' + valeur + '" size="4" name="' + nom + '" id="' + nom + '"/></label>';
	resultat += '<select name="' + nom_select + '"><option value="1">minutes</option><option value="60">heures</option><option value="1440">jours</option><option value="43200">mois</option></select>';
	return resultat;
}

EffetAuto.ChampCache = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = "";
	return '<input type="hidden" value="' + valeur + '" name="fonc_' + parametre.nom + numero.toString() + '"/>';
}

EffetAuto.ChampLecture = function (parametre, valeur) {
	var resultat = '<strong>' + parametre.label + '</strong>';
	if (valeur != '')
		resultat += ' : ' + valeur;
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

EffetAuto.ChampChoixSensProjection = function (parametre, numero, valeur) {
	if (!valeur)
		valeur = 0;
	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<select name="fonc_' + parametre.nom + numero.toString() + '">';
	html += '<option value="1" ' + ((valeur == 1) ? 'selected="selected"' : '' ) + '>Repousse sur son passage ou éloigne les cibles.</option>';
	html += '<option value="-1" ' + ((valeur == -1) ? 'selected="selected"' : '' ) + '>Attire les cibles à lui.</option></select></label>';
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
	html += '<option value="T" ' + ((valeur == 'T') ? 'selected="selected"' : '' ) + '>Tout le monde</option></select></label>';
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

EffetAuto.ChampCumulatif = function (parametre, numero, valeur) {
	if (!valeur) valeur = 'N';

	var html = '<label><strong>' + parametre.label + '</strong>&nbsp;<input type="checkbox" '+ (valeur!='N' ? 'checked' : '') +' name="fonc_' + parametre.nom + numero.toString() + '">';

	if (valeur!='N' && valeur!='O') {
		html += '&nbsp; Progressivité: '+ valeur ;
	}
	html += '</label><br />';
	return html;
}

EffetAuto.Supprime = function (id, numero) {
	if (confirm('Êtes-vous sûr de vouloir supprimer cette fonction ?')) {
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
		case 'lecture':
			if (typeof parametre.valeur !== "undefined")
				valeur = parametre.valeur;
			html = pd + EffetAuto.ChampLecture(parametre, valeur) + pf;
			break;
		case 'sens-projection':
			html = pd + EffetAuto.ChampChoixSensProjection(parametre, numero, valeur) + pf;
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
		case 'BMCsens':
			html = pd + EffetAuto.ChampChoixBMCsens (parametre, numero, valeur) + pf;
			break;
		case 'MAGeffet':
			html = pd + EffetAuto.ChampChoixMAGeffet (parametre, numero, valeur) + pf;
			break;
		case 'drop':
			html = pd + EffetAuto.ChampDropObjet (parametre, numero, valeur) + pf;
			break;
		case 'invocation':
			html = pd + EffetAuto.ChampInvocationMonstre (parametre, numero, valeur) + pf;
			break;
		case 'checkbox':
			html = pd + EffetAuto.ChampCheckBox(parametre, numero, valeur) + pf;
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
	return '<a onclick="EffetAuto.Supprime(' + id.toString() + ', ' + numero.toString() + '); return false;">' + texte + '</a>';
}

EffetAuto.EcritEffetAutoExistant = function (declenchement, type, id, force, duree, message, effet, cumulatif, proba, cible, portee, nombre, trigger_param, validite, heritage) {
	console.log('debut function EffetAuto.EcritEffetAutoExistant');
	EffetAuto.num_courant += 1;
	EffetAuto.Champs[EffetAuto.num_courant] = [];

	var conteneur = document.getElementById("liste_fonctions");
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

	for (var i = 0; i < donnees.parametres.length; i++) {
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
				valeur = trigger_param["fonc_"+donnees.parametres[i].nom] ;
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


EffetAuto.ChangeEffetAuto = function (type, numero) {
	EffetAuto.EnleveValidation(numero, 'effet');
	document.getElementById('formulaire_fonction_' + numero).innerHTML = EffetAuto.EcritNouvelEffetAuto(type, numero);
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

EffetAuto.NouvelEffetAuto = function () {
	EffetAuto.num_courant += 1;
	var numero = EffetAuto.num_courant;
	EffetAuto.Champs[numero] = [];

	var conteneur = document.getElementById("liste_fonctions");
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
