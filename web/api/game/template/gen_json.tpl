{ldelim}
"type":"{$type}"
{section name=detail loop=$data}
	,"{$data[detail].name}":"{$data[detail].valeur}"
{/section}
{rdelim}
