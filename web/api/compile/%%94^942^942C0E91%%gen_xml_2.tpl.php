<?php /* Smarty version 2.6.26, created on 2011-07-04 16:41:48
         compiled from gen_xml_2.tpl */ ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="UTF-8"<?php echo '?>'; ?>
 
<<?php echo $this->_tpl_vars['type']; ?>
>
<?php unset($this->_sections['detail']);
$this->_sections['detail']['name'] = 'detail';
$this->_sections['detail']['loop'] = is_array($_loop=$this->_tpl_vars['data']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<<?php echo $this->_tpl_vars['data'][$this->_sections['detail']['index']]['case']; ?>
>
<?php unset($this->_sections['detail2']);
$this->_sections['detail2']['name'] = 'detail2';
$this->_sections['detail2']['loop'] = is_array($_loop=$this->_tpl_vars['data_detail'][$this->_tpl_vars['data']][$this->_sections['detail']['index']]['numero']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['detail2']['show'] = true;
$this->_sections['detail2']['max'] = $this->_sections['detail2']['loop'];
$this->_sections['detail2']['step'] = 1;
$this->_sections['detail2']['start'] = $this->_sections['detail2']['step'] > 0 ? 0 : $this->_sections['detail2']['loop']-1;
if ($this->_sections['detail2']['show']) {
    $this->_sections['detail2']['total'] = $this->_sections['detail2']['loop'];
    if ($this->_sections['detail2']['total'] == 0)
        $this->_sections['detail2']['show'] = false;
} else
    $this->_sections['detail2']['total'] = 0;
if ($this->_sections['detail2']['show']):

            for ($this->_sections['detail2']['index'] = $this->_sections['detail2']['start'], $this->_sections['detail2']['iteration'] = 1;
                 $this->_sections['detail2']['iteration'] <= $this->_sections['detail2']['total'];
                 $this->_sections['detail2']['index'] += $this->_sections['detail2']['step'], $this->_sections['detail2']['iteration']++):
$this->_sections['detail2']['rownum'] = $this->_sections['detail2']['iteration'];
$this->_sections['detail2']['index_prev'] = $this->_sections['detail2']['index'] - $this->_sections['detail2']['step'];
$this->_sections['detail2']['index_next'] = $this->_sections['detail2']['index'] + $this->_sections['detail2']['step'];
$this->_sections['detail2']['first']      = ($this->_sections['detail2']['iteration'] == 1);
$this->_sections['detail2']['last']       = ($this->_sections['detail2']['iteration'] == $this->_sections['detail2']['total']);
?>
test


<?php endfor; endif; ?>
</<?php echo $this->_tpl_vars['data'][$this->_sections['detail']['index']]['case']; ?>
>
<?php endfor; endif; ?>
</<?php echo $this->_tpl_vars['type']; ?>
>