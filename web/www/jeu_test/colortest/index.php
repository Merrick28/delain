<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<html>
<head>
    <title>Colortest</title>
    <link rel="stylesheet" href="js/jquery/css/ui-lightness/jquery-ui-1.8rc1.custom.css" type="text/css" media="all"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/jquery/jquery-ui-1.8rc1.custom.min.js" type="text/javascript"></script>

    <style type="text/css">

        #equipement, #avatar {
            float: left;
            margin: 10px;
        }

        #tete {
            width: 100px;
            height: 100px;
            padding: 0.5em;
            margin: 10px;

        }

        #main_droite {
            width: 100px;
            height: 150px;
            padding: 0.5em;
            margin: 10px;
        }

        #main_gauche {
            width: 100px;
            height: 150px;
            padding: 0.5em;
            margin: 10px;
        }

        #corps {
            width: 150px;
            height: 200px;
            padding: 0.5em;
            margin: 10px;
        }

        #inventaire {
            width: 500px;
            height: 200px;
            padding: 0.5em;
            margin: 10px;
        }

        .draggable {
            width: 50px;
            height: 50px;
            padding: 0.5em;
            float: left;
            margin: 10px;
            cursor: pointer;
        }

        span, a {
            font-size: 12px;
        }

        .square {
            width: 10px;
            height: 10px;
            border: 1px solid black;
            margin: 5px;
            float: left;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

    </style>
    <script type="text/javascript">
        $(function () {
            $("#sortable").sortable({
                revert: true
            });

            $(".draggable").draggable({
                connectToSortable: '#sortable',
                revert: 'invalid',
                containment: '#equipement',
                helper: 'clone',
            });

            $("ul, li").disableSelection();

            $("#tete").droppable({
                accept: '.eq_tete',
                activeClass: 'ui-state-hover',
                hoverClass: 'ui-state-active',
                tolerance: 'fit',
                drop: function (event, ui) {
                    //$(this).addClass('ui-state-highlight').find('p').html('Dropped!');
                    $("#tete_ul").find("li").appendTo("#inventaire_ul");
                    ui.draggable.appendTo("#tete_ul");
                }
            });
            $("#corps").droppable({
                accept: '.eq_corps',
                activeClass: 'ui-state-hover',
                hoverClass: 'ui-state-active',
                tolerance: 'fit',
                drop: function (event, ui) {
                    $("#corps_ul li").appendTo("#inventaire_ul");
                    ui.draggable.appendTo("#corps_ul");
                    //$(this).addClass('ui-state-highlight').find('p').html('Dropped!');
                }
            });
            $("#main_droite").droppable({
                accept: '.eq_1main,.eq_2main',
                activeClass: 'ui-state-hover',
                hoverClass: 'ui-state-active',
                tolerance: 'fit',
                drop: function (event, ui) {
                    $("#main_droite_ul li").appendTo("#inventaire_ul");
                    if (ui.draggable.hasClass("eq_2main") || $("#main_gauche_ul li").hasClass("eq_2main")) {
                        $("#main_gauche_ul li").appendTo("#inventaire_ul");
                    }
                    ui.draggable.appendTo("#main_droite_ul");
                    //$(this).addClass('ui-state-highlight').find('p').html('Dropped:');
                }
            });
            $("#main_gauche").droppable({
                accept: '.eq_1main,.eq_2main',
                activeClass: 'ui-state-hover',
                hoverClass: 'ui-state-active',
                tolerance: 'fit',
                drop: function (event, ui) {
                    $("#main_gauche_ul li").appendTo("#inventaire_ul");
                    if (ui.draggable.hasClass("eq_2main") || $("#main_droite_ul li").hasClass("eq_2main")) {
                        $("#main_droite_ul li").appendTo("#inventaire_ul");
                    }
                    ui.draggable.appendTo("#main_gauche_ul");
                    //$(this).addClass('ui-state-highlight').find('p').html('Dropped:');
                }
            });
            $("#inventaire").droppable({
                activeClass: 'ui-state-hover',
                hoverClass: 'ui-state-active',
                drop: function (event, ui) {
                    ui.draggable.appendTo("#inventaire_ul");
                    //$(this).addClass('ui-state-highlight').find('p').html('Dropped!');
                }
            });


            changeColor1('redsquare', 'FF0000');
            changeColor1('rouxsquare', 'FF6633');
            changeColor1('blondsquare', 'FFFF22');
            changeColor1('brunsquare', '775500');
            changeColor2('sq-blue', '0000FF');
            changeColor2('sq-green', '00FF00');
            changeColor2('sq-0', 'AAFFAA');
            changeColor2('sq-1', '00FFFF');
            changeColor2('sq-2', 'AAFF00');
            changeColor2('sq-3', 'FF00FF');
            changeColor2('sq-4', 'FF0000');
        });

        function refreshImg() {
            document.getElementById('test').src = 'images/colorize.php?col=' + $("input[name='color1']").val() + "&col2=" + $("input[name='color2']").val();
        }

        function changeColor1(divName, color) {
            $("#" + divName).css("background-color", "#" + color).click(function () {
                $("input[name='color1']").val(color);
                refreshImg();
            });
        }

        function changeColor2(divName, color) {
            $("#" + divName).css("background-color", "#" + color).click(function () {
                $("input[name='color2']").val(color);
                refreshImg();
            });
        }
    </script>


</head>
<body>
<div class="ui-widget-content" id="avatar">
    <img alt="Elfe F" src="images/elfe-F.png"/>
    <img id="test" src="images/colorize.php?col=FFAA00"/>
    <BR/>
    <input type="hidden" name="color1" value="FFAAFF"/>
    <input type="hidden" name="color2" value="FFAAFF"/>
    <div class="square" id="redsquare"></div>
    <div class="square" id="rouxsquare"></div>
    <div class="square" id="blondsquare"></div>
    <div class="square" id="brunsquare"></div>
    <div style="clear:both"></div>
    <div class="square" id="sq-blue"></div>
    <div class="square" id="sq-green"></div>
    <div class="square" id="sq-0"></div>
    <div class="square" id="sq-1"></div>
    <div class="square" id="sq-2"></div>
    <div class="square" id="sq-3"></div>
    <div class="square" id="sq-4"></div>
</div>

<div class="ui-widget-content" id="equipement">


    <table>
        <tr>
            <td></td>
            <td>
                <div id="tete" class="ui-widget-header">
                    <span>Tete</span>
                    <ul id="tete_ul">

                    </ul>
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                <div id="main_gauche" class="ui-widget-header">
                    <span>Main Gauche</span>
                    <ul id="main_gauche_ul">

                    </ul>
                </div>
            </td>
            <td>
                <div id="corps" class="ui-widget-header">
                    <span>Corps</span>
                    <ul id="corps_ul">

                    </ul>
                </div>
            </td>
            <td>
                <div id="main_droite" class="ui-widget-header">
                    <span>Main Droite</span>
                    <ul id="main_droite_ul">

                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div id="inventaire" class="ui-widget-header">
                    <span>Inventaire</span>
                    <ul id="inventaire_ul">
                        <li id="obj1" class="ui-widget-content draggable eq_tete">
                            <span>Casque</span>
                        </li>
                        <li id="obj2" class="ui-widget-content draggable eq_1main">
                            <span>Ep&eacute;e 1 main</span>
                        </li>
                        <li id="obj3" class="ui-widget-content draggable eq_1main">
                            <span>Marteau 1 main</span>
                        </li>
                        <li id="obj4" class="ui-widget-content draggable eq_1main">
                            <span>Bouclier</span>
                        </li>
                        <li id="obj5" class="ui-widget-content draggable eq_2main">
                            <span>Ep&eacute;e 2 mains</span>
                        </li>
                        <li id="obj6" class="ui-widget-content draggable eq_corps">
                            <span>Armure de cuir</span>
                        </li>
                        <li id="obj6" class="ui-widget-content draggable eq_corps">
                            <span>Cotte de mailles</span>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>


</div>


</body>
</html>

