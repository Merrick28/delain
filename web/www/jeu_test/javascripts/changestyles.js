ns4 = document.layers;
ie = document.all;
ns6 = document.getElementById && !document.all;

function changeStyles(id, mouse) {
    if (ns4) {
        alert("Sorry, but NS4 does not allow font changes.");
        return false;
    }
    else if (ie) {
        obj = document.all[id];
    }
    else if (ns6) {
        obj = document.getElementById(id);
    }
    if (!obj) {
        alert("unrecognized ID");
        return false;
    }

    if (mouse == 1) {
        obj.className = "navon";
    }

    if (mouse == 0) {
        obj.className = "navoff";
    }
    return true;
}