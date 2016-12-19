<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html"/>
<xsl:variable name="site" select="menu/site"/>
<xsl:template match="/">
	<html>
	<head><title>Delain, test de feuille de style</title>
	<link rel="StyleSheet" href="../style.css" type="text/css" media="screen"/>
	<link rel="StyleSheet" href="../style.php" type="text/css" media="screen"/>
	</head>
	<body style="margin:0px;padding:0px;">
	<div id="blockMenu">
	<div class="barrL"><div class="barrR"><p class="barrC"></p></div></div>
	test pour voir <xsl:value-of select="$site"/>
	<table>
	<xsl:call-template name="menu"></xsl:call-template>
	<div class="barrL"><div class="barrR"><p class="barrC"></p></div></div>	
	</table>
	</div>
	</body>
	</html>
 </xsl:template>
<xsl:template name="menu">
		<xsl:for-each select="menu/rubrique">
			<tr><td class="titre">Rubrique : <xsl:value-of select="titre_rub"/></td></tr>
			<xsl:for-each select="lien">
				<xsl:variable name="url" select="url_lien"/>
				<tr><td class="soustitre2"><i><a href="{$url}"><xsl:value-of select="nom_lien"/></a></i></td></tr>
			</xsl:for-each>
		</xsl:for-each>
</xsl:template>
 
 </xsl:stylesheet>