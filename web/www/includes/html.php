<?php 
// Gère l’affichage standardisé d’éléments HTML.
class html
{
	var $db;
	var $cache = array();

	function html()
	{
		$this->db = new base_delain;
	}

	// Renvoie le code HTML du contenu d’un <select> (donc, les balises <option>, à partir d’une requête passée.
	// Gère un cache permettant de générer plusieurs résultats identiques sans repasser par la base à chaque fois.
	function select_from_query($req, $champ_code, $champ_texte, $selected = '', $optgroup = '')
	{
		$retour = '';
		// crc32 est plus rapide que md5, même si le risque de collisions est plus élevé.
		// On ne met pas en cache le selected, car il ne change pas la requête effectuée.
		// on va plutôt mettre à jour après coup le résultat. Ça évite de trop nombreux appels à la base.
		$cache_id = crc32("select_from_query($req, $champ_code, $champ_texte, $optgroup)");
		$cache_id_sel = crc32("select_from_query($req, $champ_code, $champ_texte, $selected, $optgroup)");
		if (isset($this->cache[$cache_id_sel]))
		{
			// On a en cache tout ce qu’il faut, y compris avec la bonne valeur sélectionnée
			$retour = $this->cache[$cache_id_sel];
			$selected = '';
		}
		else if (isset($this->cache[$cache_id]))
		{
			// On a en cache le SELECT sans élément pré-sélectionné
			$retour = $this->cache[$cache_id];
		}
		else
		{
			// On n’a rien de correspondant en cache. On construit le select.
			$inclure_optgroupe = $optgroup != '';
			$this->db->query($req);
			$optgroup_courant = '';
			$optgroup_ouvert = false;
			while ($this->db->next_record())
			{
				if ($inclure_optgroupe && $this->db->f($optgroup) != $optgroup_courant)
				{
					$fermeture = ($optgroup_courant != '') ? '</optgroup>' : '';
					$retour .= $fermeture . '<optgroup label="' . $this->db->f($optgroup) . '">';
					$optgroup_ouvert = true;
					$optgroup_courant = $this->db->f($optgroup);
				}
				$retour .= '<option value="' . $this->db->f($champ_code) . '">' . $this->db->f($champ_texte) . '</option>';
			}
			if ($optgroup_ouvert) $retour .= '</optgroup>';
			$this->cache[$cache_id] = $retour;
		}
		if ($selected != '')
		{
			// On a demandé une pré-sélection d’un élément.
			$retour = $this->pre_selectionne($retour, $selected, '"');
			$this->cache[$cache_id_sel] = $retour;
		}
		return $retour;
	}

	// Sélectionne un élément dans un <select> pré-généré (typiquement, en cache)
	function pre_selectionne($select, $value, $quotes = '')
	{
		$resultat = $select;
		if ($quotes != '')
			$resultat = str_replace(" value=$quotes" . $value . $quotes, " value=$quotes" . $value . $quotes . " selected='selected'", $resultat);
		else
		{
			$resultat = str_replace(" value='$value'", " value='$value' selected='selected'", $resultat);
			$resultat = str_replace(' value="' . $value . '"', ' value="' . $value . '"' . " selected='selected'", $resultat);
		}
		return $resultat;
	}

	function etage_select($selected = 9999, $restriction = '')
	{
		$req = "select case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle, etage_numero
			from etage
			$restriction
			order by etage_reference desc, etage_numero";

		return $this->select_from_query($req, 'etage_numero', 'etage_libelle', $selected);
	}
    
    // Renvoie un menu déroulant SELECT préselectionné.
    // L’argument $O_N indique si les valeurs du menu sont O et N, ou 1 et 0.
    function oui_non_select($selected, $O_N = true)
    {
        $o = ($O_N) ? 'O' : '1';
        $n = ($O_N) ? 'N' : '0';
        $sel_o = ($selected == $o) ? 'selected="selected"' : '';
        $sel_n = ($selected == $n) ? 'selected="selected"' : '';
        return "<option value='$o' $sel_o>Oui</option><option value='$n' $sel_n>Non</option>";
    }
}
?>