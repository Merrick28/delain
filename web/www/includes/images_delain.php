<?php class images_delain
{
	public static function Decors()
	{
		$prefixe = "dec_";
		$suffixe = ".gif";
		return self::parcourt($prefixe, $suffixe);
	}

	public static function Fonds($style)
	{
		$prefixe = "f_{$style}_";
		$suffixe = ".png";
		return self::parcourt($prefixe, $suffixe);
	}

	public static function Murs($style)
	{
		$prefixe = "t_{$style}_mur_";
		$suffixe = ".png";
		return self::parcourt($prefixe, $suffixe);
	}

	private static function parcourt($prefixe, $suffixe)
	{
		$rep = '../../images/';
		$tableau = array();
		for ($i = 0; $i < 1000; $i++)
		{
			$nom = $rep . $prefixe . $i . $suffixe;
			if (is_file($nom))
			{
				$tableau[] = array($i, $prefixe . $i . $suffixe);
			}
		}
		return $tableau;
	}
}
?>