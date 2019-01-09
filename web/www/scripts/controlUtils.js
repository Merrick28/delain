function addToOptions(selectSrc, selectDst, nombre, compiledInput) {
    for (var i = 0; i < selectSrc.options.length; i++) {
        var optSrc = selectSrc.options[i];
        if (optSrc.selected) {
            var opt = findOption(selectDst, optSrc.value);
            if (opt) {
                var num = opt.text.substring(opt.text.indexOf('(') + 1, opt.text.indexOf(')'));
                updOptionSel(opt, optSrc.value, '(' + (num * 1 + nombre) + ') ' + optSrc.text);
            } else {
                addOptionSel(selectDst, optSrc.value, '(' + nombre + ') ' + optSrc.text);
            }
        }
    }
    compileAccumulatorCounter(selectDst, compiledInput);
}

function substractToOptions(selectSrc, nombre, compiledInput) {
    for (var i = selectSrc.options.length - 1; i >= 0; i--) {
        var optSrc = selectSrc.options[i];
        if (optSrc.selected) {
            var opt = findOption(selectSrc, optSrc.value);
            if (opt) {
                var num = opt.text.substring(opt.text.indexOf('(') + 1, opt.text.indexOf(')'));
                var textafter = opt.text.substring(opt.text.indexOf(')') + 1);
                var numAfter = (num * 1 - nombre);
                if (numAfter > 0) {
                    updOptionSel(opt, optSrc.value, '(' + numAfter + ') ' + textafter)
                } else {
                    removeOption(selectSrc, optSrc.value);
                }
            }
        }
    }
    compileAccumulatorCounter(selectSrc, compiledInput);
}

function updOptionSel(opt, value, text) {
    opt.text = text;
    opt.value = value;
    opt.selected = true;
}

function addOption(control, value, text) {
    control.options[control.options.length] = new Option();
    control.options[control.options.length - 1].text = text;
    control.options[control.options.length - 1].value = value;
    //control.options[control.options.length-1].selected = true;
}

function addOptGroup(control, text) {
    var groupe = document.createElement("optgroup");
    groupe.label = text;
    control.appendChild(groupe);
    return groupe;
}

function cleanOption(control) {
    while (control.hasChildNodes())
        control.removeChild(control.firstChild);
}

function addOptionSel(control, value, text) {
    control.options[control.options.length] = new Option();
    control.options[control.options.length - 1].text = text;
    control.options[control.options.length - 1].value = value;
    control.options[control.options.length - 1].selected = true;
}

function findOption(control, val) {
    for (var i = 0; i < control.options.length; i++) {
        if (control.options[i].value == val) {
            return control.options[i];
        }
    }
}

function removeOption(control, val) {
    for (var i = 0; i < control.options.length; i++) {
        if (control.options[i].value == val) {
            control.options[i] = null;
        }
    }
}

function addOptionArray(control, liste, filtretype, filtrevaleur) {
    var groupeEnCours = "";
    var valeurMin;
    var valeurMax;

    if (filtrevaleur != '') {
        var valeurs = filtrevaleur.split(";");
        valeurMin = parseInt(valeurs[0]);
        valeurMax = parseInt(valeurs[1]);
    }
    for (var i = 0; i < liste.length; i++) {
        if (liste[i][2] && liste[i][2] != groupeEnCours.label
            && (filtretype == '' || filtretype == liste[i][2])
            && (filtrevaleur == '' || (liste[i][3] >= valeurMin && liste[i][3] <= valeurMax))) {
            groupeEnCours = addOptGroup(control, liste[i][2]);
        }
        if ((filtretype == '' || filtretype == liste[i][2])
            && (filtrevaleur == '' || (liste[i][3] >= valeurMin && liste[i][3] <= valeurMax)))
            addOption(control, liste[i][0], liste[i][1] + ' (valeur ' + liste[i][3] + ' br)');
    }
}

function fillOptions(controlSrc, controlDst, liste) {
    for (var i = 0; i < liste.length; i++) {
        var opt = findOption(controlSrc, liste[i][0]);
        addOption(controlDst, opt.value, '(' + liste[i][1] + ') ' + opt.text);
    }
}

function compileAccumulatorCounter(selectSrc, inputDst) {
    var result = "";
    for (var i = 0; i < selectSrc.options.length; i++) {
        var opt = selectSrc.options[i];
        var num = opt.text.substring(opt.text.indexOf('(') + 1, opt.text.indexOf(')'));
        result += opt.value + ":" + num + ";";
    }
    inputDst.value = result;
}

$("#tobj").change(function () {
    $('#gobj').empty();
    var tobj_val = $(this).val();
    $.ajax({
        url: "ajax/get_objets_by_tobj.php",
        method: "POST",
        data: {tobj_cod: tobj_val},
        dataType: "json"
    }).done(function (data) {
        $.each(data, function (index, element) {
            $("#gobj").append($('<option>', {
                    value: element.gobj_cod,
                    text: element.gobj_nom
                })
            );
        })
    }).fail(function () {
        alert("error");
    });
});

$("#gobj_valeur").change(function () {
    $('#gobj').empty();
    var valeur = $(this).val();
    $.ajax({
        url: "ajax/get_objets_by_valeur.php",
        method: "POST",
        data: {valeur: valeur},
        dataType: "json",
    }).done(function (data) {
        //console.log(data);
        $.each(data, function (index, element) {
            $("#gobj").append($('<option>', {
                    value: element.gobj_cod,
                    text: element.gobj_nom_generique
                })
            );
        })
    }).fail(function () {
        alert("error");
    });
});

