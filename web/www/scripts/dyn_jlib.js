// TODO : Browser à remplacer par du code maison.

function Browser() {

  var ua, s, i;

  this.isIE    = false;  // Internet Explorer
  this.isNS    = false;  // Netscape
  this.version = null;

  ua = navigator.userAgent;
  
  s = "MSIE";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isIE = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  s = "Netscape6/";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  // Treat any other "Gecko" browser as NS 6.1.

  s = "Gecko";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = 6.1;
    return;
  }
  //alert("UA="+ua);
}

var browser = new Browser();
/*
*
* Objet box: utilisé pour redimentionner ou déplacer des divs
*
*/
function AjBox(aSrcDiv){

	this.boxTop    = parseInt(aSrcDiv.style.top,  10);
	this.boxLeft   = parseInt(aSrcDiv.style.left,  10);
	this.boxWidth  = parseInt(aSrcDiv.style.width,  10);
	this.boxHeight = parseInt(aSrcDiv.style.height,  10);
	this.srcDiv    = aSrcDiv;

}
function applyBoxValues(){
	this.srcDiv.style.top    = this.boxTop + "px";
	this.srcDiv.style.left   = this.boxLeft + "px";
	this.srcDiv.style.width  = this.boxWidth + "px";
	this.srcDiv.style.height = this.boxHeight + "px";
}
AjBox.prototype.applyBoxValues = applyBoxValues;


function showDiv(srcDiv){
  srcDiv.style.visibility = "visible";
  srcDiv.style.display = "block";
}
function hideDiv(srcDiv){
  srcDiv.style.visibility = "hidden";
  srcDiv.style.display = "none";
}
/*

Fonction pour écrire une ligne de débug.

*/

function printDebug(text){
	if(document.getElementById('debug')){
		document.getElementById('debug').value += text + '\n';
	} else {
		alert('DEBUG:'+text);
	}
}

/*
	Sets the cursor for the given div with CursorName
*/
function setCursor(div, cursorName){
	if(div.style.cursor){
		// NETSCAPE
		div.style.cursor = cursorName;
	} else {
		// IE
		document.body.style.cursor = cursorName;
	}
	resizer.currentCursor = cursorName;
}
/*
	Returns the cursor X position for the given event
*/
function getCursorPosX(event){
	if (browser.isIE) {
		return window.event.x;
	} else if (browser.isNS) {
		return event.pageX;
	}
}
/*
	Returns the cursor Y position for the given event
*/
function getCursorPosY(event){
	if (browser.isIE) {
		return window.event.y;
	} else if (browser.isNS) {
		return event.pageY;
	}
}
/*
	Returns the cursor X position for the given event
*/
function getCursorPosXLayer(event){
	if (browser.isIE) {
		return window.event.offsetX;
	} else if (browser.isNS) {
		return event.layerX;
	}
}
/*
	Returns the cursor Y position for the given event
*/
function getCursorPosYLayer(event){
	if (browser.isIE) {
		return window.event.offsetY;
	} else if (browser.isNS) {
		return event.layerY;
	}
}
/*
	Returns true if div is the target of the event.
*/
function checkEventTarget(div,event){
	var target;
	if (browser.isIE) {
		target = window.event.srcElement;
	} else if (browser.isNS) {
		target = event.target;
	}
	//printDebug('INFO: TARGET='+target);
	return (target == div);
}
function getEventTarget(div,event){
	var target;
	if(div != window){
		return div;
	}
	if (browser.isIE) {
		target = window.event.srcElement;
	} else if (browser.isNS) {
		target = event.target;
	}	
	return target;
}
function initStylePositionFromClassPosition(aDiv){
	if(!aDiv.style.top) {
		aDiv.style.top = parseIntGen(getCssValue(aDiv.id,'top'))+"px";
	}
	if(!aDiv.style.left) {
		aDiv.style.left = parseIntGen(getCssValue(aDiv.id,'left'))+"px";
	}
	if(!aDiv.style.width) {
		aDiv.style.width = parseIntGen(getCssValue(aDiv.id,'width'))+"px";
	}
	if(!aDiv.style.height) {
		aDiv.style.height = parseIntGen(getCssValue(aDiv.id,'height'))+"px";
	}
}
function parseIntGen(obj) {
	if(isUndefined(obj)){
		return 0;
	}
	return parseInt(obj);

}
function parseFloatGen(obj) {
	if(isUndefined(obj)){
		return 0.0;
	}
	var res = parseFloat(obj);
	return (res == NaN)?0:res;
}

function getCssValue(tagRef,element) {
	var tag = document.getElementById(tagRef);
	return getElementCssValue(tag,element);
}
function getElementCssValue(tag,element) {
	//var tag = document.getElementById(tagRef);
	var value= tag.style[element];	
	if(isUndefined(value)){
		
		var clNames = tag.className;
		var clArray =  clNames.split(" ");
		var res;
		for (var i=0; i< clArray.length; i++) {
			res = getCssValueBySelector( clArray[i],element);
			//printDebug("getCssValue  res=**"+res+"**");
			if(!isUndefined(res)){
				return res;
			}
		}
		return null;
	}
	return  value;
}
function getCssValueBySelector(selector,element){
	var style = getStyleBySelector(selector);
	if( isUndefined(style)){
		return null;
	}
	return style[element]
}
function getStyleBySelector( selector )
{
       var sheetList = document.styleSheets;
       var ruleList;
       var i, j;
   
       /* look through stylesheets in reverse order that
          they appear in the document */
       var ruleType;
       // ça ne marche pas cette fois ????
       /*if(CSSRule) {
	       // NETSCAPE
	       ruleType = CSSRule.STYLE_RULE
       } else {
	       // IE
	       ruleType = 1;
       }*/
       if(browser.isNS){
	       ruleType = CSSRule.STYLE_RULE
       }
       else if(browser.isIE){
	       ruleType = 1;   
       }
       for (i=sheetList.length-1; i >= 0; i--)
       {	   
	   if(sheetList[i].cssRules){
		   // NETSCAPE
		   ruleList = sheetList[i].cssRules;
	   } else {
		   // IE
		   ruleList = sheetList[i].rules;
	   }
           for (j=0; j<ruleList.length; j++)
           {
              if (ruleList[j].selectorText != null) {
                if ((ruleList[j].selectorText == selector) ||
	       	       (ruleList[j].selectorText.substring(1) == selector))
                {
                   return ruleList[j].style;
                }
              }
           }
       }
       return null;
}
function isDefined( obj ) {
  return (typeof obj != 'undefined' && typeof obj != '' && obj != null);
}

function isUndefined(aObj){	
   return (aObj==null || aObj=='' || aObj=='undefined');
}
function divHasClass(aDiv, aClassName){
	return (aDiv.className.indexOf(aClassName) >= 0);
}

function top(aDiv){
	return parseInt(aDiv.srcDiv.style.top);
}
function left(aDiv){
	return parseInt(aDiv.srcDiv.style.left);
}
function bottom(aDiv){
	return parseInt(aDiv.srcDiv.style.top) + parseInt(aDiv.srcDiv.style.height);
}
function right(aDiv){
	return parseInt(aDiv.srcDiv.style.left) + parseInt(aDiv.srcDiv.style.width);
}

function addEventListenerGen(element,eventName,functionRef){
	if(element.addEventListener){
		// NETSCAPE
		element.addEventListener(eventName, functionRef, true);
	} else {
		// IE
		element.attachEvent("on"+eventName, functionRef);
        }
}
function removeEventListenerGen(element,eventName,functionRef){
	if(element.removeEventListener){
		// NETSCAPE
		element.removeEventListener(eventName, functionRef, true);
	} else {
		// IE
		element.detachEvent("on"+eventName, functionRef);
        }
}
function describeElement(el){
	var res = "[";
	for (var propriete in el) {
		res += propriete+"="+el[propriete]+ " ; ";
	}
	res +="]";
	return res;
}

function addClass(divId, clName) {
   var div = document.getElementById( divId );
   if ( !divHasClass( div, clName ) ) {
       div.className =  div.className + ' ' + clName;
   }
}
function removeClass(divId, clName) {
   var div = document.getElementById( divId );
   if ( divHasClass( div, clName ) ) {
       index = div.className.indexOf( clName );
       div.className =
              div.className.substr( 0, index )
            + div.className.substr( index + clName.length );
   }
}
function replaceClass(divId, oldClassName, newClassName) {
   var div = document.getElementById( divId );
   if ( divHasClass( div, oldClassName ) ) {
       index = div.className.indexOf( oldClassName );
       div.className =
              div.className.substr( 0, index )
            + div.className.substr( index + oldClassName.length )
            + ' ' + newClassName;
   }
}

