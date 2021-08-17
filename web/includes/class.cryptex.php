<?php


class cryptex
{

    var $type;
    var $code;

    function __construct($type=0, $code="")
    {
        $this->type = $type;
        $this->code = $code;
    }

    function display()
    {
        $nbdrum = strlen($this->code);

        $display =  '<script> 
            function getCryptexValue() {
                var code = "";
                for (var d=0; d<'.$nbdrum.'; d++)
                {
                    code = code + $("#drum"+d).val();
                }
                return code;
            }
            $(document).ready(function () {';
        for ($i=0; $i<$nbdrum; $i++)
        {
            $display.=  '$("#drum'.$i.'").drum({ panelCount: 10 });';
        }
        $display.=  '});</script> <div style="margin-top:10px; display: inline-flex;"> <img style="height:122px; margin-left:20px;" src="/images/interface/left-cap-cryptex.png">';

        for ($i=0; $i<$nbdrum; $i++)
        {
            $display.=  ' <select id="drum'.$i.'" class="drum" name="drum'.$i.'">';
            for ($d=10; $d>0; $d--)
            {
                $drum = ($d == 10 ? 0 : $d );
                $display.=  '<option value="'.$drum.'">'.$drum.'</option>';
            }
            $display.=  '</select>';
        }
        $display.=  '<img style="height:122px; margin-left:3px;"  src="/images/interface/right-cap-cryptex.png"></div>';

        return $display;
    }

}