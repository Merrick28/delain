
  function verifform()
  {
	if(document.formuperso1.nom.value == "")
    {
    	alert("Etes vous sur de na pas vouloir donner de nom a votre aventurier ???\n");
    	document.formuperso1.nom.focus();
    	return false;
    }
   
	total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
	if (total < 45)
	{
		alert("Vous avez encore des points a repartir !\n");
		document.formuperso1.force.focus();
		return false;
	}
  }
function verif_carac(carac)
{
	if (carac == 1)
	{
		if (document.formuperso1.force.value<6)
		{
			alert("Vous ne pouvez pas descendre en dessous de 6 !\n");
			document.formuperso1.force.value = 6;
			total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
			document.formuperso1.points.value=(45 - total);
			document.formuperso1.force.focus();
			return false;
		}
		if (document.formuperso1.force.value>16)
		{
			alert("Vous ne pouvez pas aller au dessus de 16 !\n");
			document.formuperso1.force.value = 16;
			total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
			document.formuperso1.points.value=(45 - total);
			document.formuperso1.force.focus();
			return false;
		}
		total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
		if (total > 45)
		{
			alert("Vous n'avez pas assez de points a repartir !\n");
			document.formuperso1.force.value = (45-(parseInt(document.formuperso1.dex.value))-(parseInt(document.formuperso1.con.value))-(parseInt(document.formuperso1.intel.value)));
			document.formuperso1.points.value = 0;
			document.formuperso1.force.focus();
			return false;
		}
		else
		{
			document.formuperso1.points.value=(45 - total);
			return false;
		}
	}
	if (carac == 2)
	{
		if (document.formuperso1.dex.value<6)
		{
			alert("Vous ne pouvez pas descendre en dessous de 6 !\n");
			document.formuperso1.dex.value = 6;
			total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
			document.formuperso1.points.value=(45 - total);
			document.formuperso1.dex.focus();
			return false;
		}
		if (document.formuperso1.dex.value>16)
		{
			alert("Vous ne pouvez pas aller au dessus de 16 !\n");
			document.formuperso1.dex.value = 16;
			document.formuperso1.force.value = (45-(parseInt(document.formuperso1.dex.value))-(parseInt(document.formuperso1.con.value))-(parseInt(document.formuperso1.intel.value)));
			document.formuperso1.points.value = 0;
			document.formuperso1.dex.focus();
			return false;
		}
		total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
		if (total > 45)
		{
			alert("Vous n'avez pas assez de points a repartir !\n");
			document.formuperso1.dex.value = (45-(parseInt(document.formuperso1.force.value))-(parseInt(document.formuperso1.con.value))-(parseInt(document.formuperso1.intel.value)));
			document.formuperso1.points.value = 0;
			document.formuperso1.dex.focus();
			return false;
		}
		else
		{
			document.formuperso1.points.value=(45 - total);
		}
		return false;
	}
	if (carac == 3)
	{
		if (document.formuperso1.con.value<6)
		{
			alert("Vous ne pouvez pas descendre en dessous de 6 !\n");
			document.formuperso1.con.value = 6;
			total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
			document.formuperso1.points.value=(45 - total);
			document.formuperso1.con.focus();
			return false;
		}
		if (document.formuperso1.con.value>16)
		{
			alert("Vous ne pouvez pas aller au dessus de 16 !\n");
			document.formuperso1.con.value = 16;
			document.formuperso1.force.value = (45-(parseInt(document.formuperso1.dex.value))-(parseInt(document.formuperso1.con.value))-(parseInt(document.formuperso1.intel.value)));
			document.formuperso1.points.value = 0;
			document.formuperso1.con.focus();
			return false;
		}
		total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
		if (total > 45)
		{
			alert("Vous n'avez pas assez de points a repartir !\n");
			document.formuperso1.con.value = (45-(parseInt(document.formuperso1.dex.value))-(parseInt(document.formuperso1.force.value))-(parseInt(document.formuperso1.intel.value)));
			document.formuperso1.points.value = 0;
			document.formuperso1.con.focus();
			return false;
		}
		else
		{
			document.formuperso1.points.value=(45 - total);
		}
		return false;
	}
	if (carac == 4)
	{
		if (document.formuperso1.intel.value<6)
		{
			alert("Vous ne pouvez pas descendre en dessous de 6 !\n");
			document.formuperso1.intel.value = 6;
			total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
			document.formuperso1.points.value=(45 - total);
			document.formuperso1.intel.focus();
			return false;
		}
		if (document.formuperso1.intel.value>16)
		{
			alert("Vous ne pouvez pas aller au dessus de 16 !\n");
			document.formuperso1.intel.value = 16;
			document.formuperso1.force.value = (45-(parseInt(document.formuperso1.dex.value))-(parseInt(document.formuperso1.con.value))-(parseInt(document.formuperso1.intel.value)));
			document.formuperso1.points.value = 0;
			document.formuperso1.intel.focus();
			return false;
		}
		total = parseInt(document.formuperso1.dex.value) +  parseInt(document.formuperso1.force.value) +  parseInt(document.formuperso1.con.value) +  parseInt(document.formuperso1.intel.value);
		if (total > 45)
		{
			alert("Vous n'avez pas assez de points a repartir !\n");
			document.formuperso1.intel.value = (45-(parseInt(document.formuperso1.dex.value))-(parseInt(document.formuperso1.con.value))-(parseInt(document.formuperso1.force.value)));
			document.formuperso1.points.value = 0;
			document.formuperso1.intel.focus();
			return false;
		}
		else
		{
			document.formuperso1.points.value=(45 - total);
		}
		return false;
	}
	if (carac == 5)
	{
		document.formuperso1.points.value=(45 - total);
		return false;
	}
	return false;
}
