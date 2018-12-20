function blocking(nr) {
    if (document.layers) {
        current = (document.layers[nr].display == 'none') ? 'block' : 'none';
        document.layers[nr].display = current;
    }
    else if (document.all) {
        current = (document.all[nr].style.display == 'none') ? 'block' : 'none';
        document.all[nr].style.display = current;
    }
    else if (document.getElementById) {
        vista = (document.getElementById(nr).style.display == 'none') ? 'block' : 'none';
        document.getElementById(nr).style.display = vista;
    }
}
