function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor)
{
    var theCells = null;

    // 1. Pointer and mark feature are disabled or the browser can't get the
    //    row -> exits
    if ((thePointerColor == '' && theMarkColor == '')
        || typeof(theRow.style) == 'undefined') {
        return false;
    }

    // 2. Gets the current row and exits if the browser can't get it
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }

    // 3. Gets the current color...
    var rowCellsCnt  = theCells.length;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    // 3.1 ... with DOM compatible browsers except Opera that does not return
    //         valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        domDetect    = true;
    }
    // 3.2 ... with other browsers
    else {
        currentColor = theCells[0].style.backgroundColor;
        domDetect    = false;
    } // end 3

    // 4. Defines the new color
    // 4.1 Current color is the default one
    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.2 Current color is the pointer one
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.3 Current color is the marker one
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
        }
    } // end 4

    // 5. Sets the new color...
    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].setAttribute('bgcolor', newColor, 0);
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
} // end of the 'setPointer()' function

function affiche(x, y) {
	var isNS = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
	var isGecko = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 5);
	var cible_vue;
	var tab_vue;
	if (isGecko) {
   	var lacible = document.getElementById("lacible");
   	tab_vue = document.getElementById("tab_vue");
   	cible_vue = lacible.style;
	}
	else
	{
   	cible_vue= (isNS) ? document.objets.lacible : document.all.lacible.style;
   	tab_vue= (isNS) ? document.tab_vue : document.all.tab_vue;
   	var lacible= (isNS) ? document.objets.lacible : document.all.lacible;
	}
	if (!cible_vue) return;
	cible_vue.left= (isNS) ? tab_vue.rows[y].cells[x].left : tab_vue.rows[y].cells[x].offsetLeft;
	cible_vue.top= (isNS) ? tab_vue.rows[y].cells[x].top : tab_vue.rows[y].cells[x].offsetTop;
	cible_vue.visibility= (isNS)? "show" : "visible";
}
function cache() {
	var isNS = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
	var isGecko = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 5);
	var tab_vue;
	var cible_vue;
	if (isGecko) {
   	tab_vue = document.getElementById("tab_vue");
   	var lacible = document.getElementById("lacible");
   	cible_vue = lacible.style;
	}
	else
	{
   	 tab_vue= (isNS) ? document.tab_vue : document.all.tab_vue;
   	cible_vue= (isNS) ? document.objets.lacible : document.all.lacible.style;
   	var lacible= (isNS) ? document.objets.lacible : document.all.lacible;
	}
	if (!cible_vue) return;
	cible_vue.visibility="hidden";
}