<?php /* Smarty version 2.6.26, created on 2011-06-20 11:06:18
         compiled from menu_json.tpl */ ?>
{
<?php echo $this->_tpl_vars['type']; ?>
,
[
<?php unset($this->_sections['detail']);
$this->_sections['detail']['name'] = 'detail';
$this->_sections['detail']['loop'] = is_array($_loop=$this->_tpl_vars['perso']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['detail']['show'] = true;
$this->_sections['detail']['max'] = $this->_sections['detail']['loop'];
$this->_sections['detail']['step'] = 1;
$this->_sections['detail']['start'] = $this->_sections['detail']['step'] > 0 ? 0 : $this->_sections['detail']['loop']-1;
if ($this->_sections['detail']['show']) {
    $this->_sections['detail']['total'] = $this->_sections['detail']['loop'];
    if ($this->_sections['detail']['total'] == 0)
        $this->_sections['detail']['show'] = false;
} else
    $this->_sections['detail']['total'] = 0;
if ($this->_sections['detail']['show']):

            for ($this->_sections['detail']['index'] = $this->_sections['detail']['start'], $this->_sections['detail']['iteration'] = 1;
                 $this->_sections['detail']['iteration'] <= $this->_sections['detail']['total'];
                 $this->_sections['detail']['index'] += $this->_sections['detail']['step'], $this->_sections['detail']['iteration']++):
$this->_sections['detail']['rownum'] = $this->_sections['detail']['iteration'];
$this->_sections['detail']['index_prev'] = $this->_sections['detail']['index'] - $this->_sections['detail']['step'];
$this->_sections['detail']['index_next'] = $this->_sections['detail']['index'] + $this->_sections['detail']['step'];
$this->_sections['detail']['first']      = ($this->_sections['detail']['iteration'] == 1);
$this->_sections['detail']['last']       = ($this->_sections['detail']['iteration'] == $this->_sections['detail']['total']);
?>
	{<?php echo $this->_tpl_vars['perso'][$this->_sections['detail']['index']]['name']; ?>
:"<?php echo $this->_tpl_vars['perso'][$this->_sections['detail']['index']]['valeur']; ?>
"},
	<?php endfor; endif; ?>
]
}