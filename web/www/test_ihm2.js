var bouton_x = '720px';
var bouton_l = '120px';
var bouton_h = '19px';

var fenetres = new Array();

// Classe représentant une fenêtre.
function Fenetre (bouton_y, fenetre_id, ouvert_x, ouvert_y, ouvert_l, ouvert_h)
{
	this.bouton_y = bouton_y;
	this.ouvert_y = ouvert_y;
	this.ouvert_x = ouvert_x;
	this.ouvert_l = ouvert_l;
	this.ouvert_h = ouvert_h;
	this.fenetre_id = fenetre_id;
	this.ouvert = false;

	// Ferme la fenêtre
	this.fermer = function ()
	{
		if (this.ouvert)
		{
			var fenetre = document.getElementById(this.fenetre_id);
			fenetre.style.maxWidth = bouton_l;
			fenetre.style.minWidth = bouton_l;
			fenetre.style.maxHeight = bouton_h;
			fenetre.style.minHeight = bouton_h;
			fenetre.style.top = this.bouton_y;
			fenetre.style.left = bouton_x;

			this.ouvert = false;
		}
	};

	// Ouvre la fenêtre
	this.ouvrir = function ()
	{
		fermer_toutes();
		if (!this.ouvert)
		{
			var fenetre = document.getElementById(this.fenetre_id);
			fenetre.style.maxWidth = this.ouvert_l;
			fenetre.style.minWidth = this.ouvert_l;
			fenetre.style.maxHeight = this.ouvert_h;
			fenetre.style.minHeight = '';
			fenetre.style.top = this.ouvert_y;
			fenetre.style.left = this.ouvert_x;

			this.ouvert = true;
		}
	};
}

// Ferme toutes les fenêtres
function fermer_toutes()
{
	for (var i = 0; i < fenetres.length; i++)
	{
		var f = fenetres[i];
		if (f.ouvert)
			f.fermer();
	}
}

var fenetre_inventaire = new Fenetre("100px", "fenetre_inventaire", "100px", "150px", "200px", "300px");
var fenetre_perso = new Fenetre("150px", "fenetre_perso", "100px", "150px", "200px", "300px");
var fenetre_switch = new Fenetre("200px", "fenetre_switch", "200px", "200px", "220px", "200px");

fenetres[0] = fenetre_inventaire;
fenetres[1] = fenetre_perso;
fenetres[2] = fenetre_switch;