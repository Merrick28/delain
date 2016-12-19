<?xml version="1.0" encoding="UTF-8"?> 
<{$type}>
{section name=detail loop=$data}
<{$data[detail].case}>
{section name=detail2 loop=$data_detail.$data[detail].numero}
test


{/section}
</{$data[detail].case}>
{/section}
</{$type}>
