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

        //==============================================================================================================
        // préparer les valeurs à appliquer au drum!
        $drum = [] ;

        // Les lettres
        if ($this->type == 1 || $this->type >= 2)
        {
            for ($d=0; $d<26; $d++)
            {
                $drum[] = chr($d+65) ;
            }
        }

        // Les chiffres
        if ($this->type == 0 || $this->type >= 2)
        {
            for ($d=0; $d<10; $d++)
            {
                $drum[] = $d ;
            }
        }

        // ajouter le 1er rouleau à la fin !
        $nbc = sizeof($drum);
        $drum[$nbc] = $drum[0];
        $dail_step = round($nbc / 5 );

        //==============================================================================================================
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
            $display.=  '$("#drum'.$i.'").drum({ dail_step: '.$dail_step.',panelCount: '.$nbc.' });';
        }
        $display.=  '});</script> <div style="margin-top:10px; display: inline-flex;"> <img style="height:122px; margin-left:20px;" src="/images/interface/left-cap-cryptex.png">';



        for ($i=0; $i<$nbdrum; $i++)
        {
            $display.=  ' <select id="drum'.$i.'" class="drum" name="drum'.$i.'">';

            // Les chiffres
            for ($d=$nbc; $d>0; $d--)
            {
                $display.=  '<option value="'.$drum[$d].'">'.$drum[$d].'</option>';
            }
            $display.=  '</select>';
        }
        $display.=  '<img style="height:122px; margin-left:3px;"  src="/images/interface/right-cap-cryptex.png"></div>';

        return $display;
    }

}