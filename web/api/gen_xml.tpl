<?xml version="1.0" encoding="UTF-8"?> 
<{$type}>
{section name=detail loop=$data}
<{$data[detail].name} valeur="{$data[detail].valeur}" />
{/section}
</{$type}>
