/*
	CONSTANTES
*/
/*
// const uniquement g�r� par Moz :-(

const STICKY = "sticky";

const TOP_TO_TOP = "TopToTop";
const TOP_TO_BOTTOM = "TopToBottom";
const BOTTOM_TO_TOP = "BottomToTop";
const BOTTOM_TO_BOTTOM = "BottomToBottom";
const LEFT_TO_LEFT = "LeftToLeft";
const LEFT_TO_RIGHT = "LeftToRight";
const RIGHT_TO_LEFT = "RightToLeft";
const RIGHT_TO_RIGHT = "RightToRight";

const MINIMUM_WIDTH = 25;
const MINIMUM_HEIGHT = 25;
const CURSOR_BORDER_WIDTH = 10;
*/
var STICKY = "sticky";

var TOP_TO_TOP = "TopToTop";
var TOP_TO_BOTTOM = "TopToBottom";
var BOTTOM_TO_TOP = "BottomToTop";
var BOTTOM_TO_BOTTOM = "BottomToBottom";
var LEFT_TO_LEFT = "LeftToLeft";
var LEFT_TO_RIGHT = "LeftToRight";
var RIGHT_TO_LEFT = "RightToLeft";
var RIGHT_TO_RIGHT = "RightToRight";

var MINIMUM_WIDTH = 25;
var MINIMUM_HEIGHT = 25;
var CURSOR_BORDER_WIDTH = 10;

// Objet DjResizeable
function DjResizeable(aSrcDiv) {
	this.srcDiv = aSrcDiv;
	this.srcId  = aSrcDiv.id;
	//addEventListenerGen(this.srcDiv,"mousemove",setCursorResize);
	//addEventListenerGen(this.srcDiv,"mouseout",unsetCursorResize);
	//addEventListenerGen(this.srcDiv,"mousedown",startResizing);
	this.srcDiv.onmousemove  = setCursorResize;
	this.srcDiv.onmouseout   = unsetCursorResize;
	this.srcDiv.onmousedown  = startResizing;
	this.sticky = divHasClass(this.srcDiv, STICKY);
	// Lorsque les positions sont d�finies dans class et pas style il faut les initialiser
	initStylePositionFromClassPosition(this.srcDiv);
	// Propri�t�s utilis�es pour les sticky
	if(this.sticky) {
		this.jointures = new Array();
	}
}
function addJointure(aTypeJointure, aObjetJointure){
	var arr = this.jointures[aTypeJointure];
	if(arr == null){
		this.jointures[aTypeJointure] = new Array();
		arr = this.jointures[aTypeJointure];
	}
	arr[arr.length] = aObjetJointure;
}
DjResizeable.prototype.addJointure = addJointure;

function DjResizer(){
	// Variables
	this.cursorPosX = null;
	this.cursorPosY = null;
	this.targetBox = null;
	this.isResizing = false;
	this.currentCursor = "";
	this.targetDiv = null;
}
var resizer = new DjResizer();
function setCursorResize(event) {
	if(!checkEventTarget(this,event)){
		return;
	}
	if(resizer.isResizing){
		return;
	}
	// determination du curseur � afficher
	var xPos = getCursorPosXLayer(event);
	var yPos = getCursorPosYLayer(event);

	var cursorName = "";
	if (yPos <= CURSOR_BORDER_WIDTH) {
		cursorName += "n";
	} else if (yPos >= this.offsetHeight - CURSOR_BORDER_WIDTH) {
		cursorName += "s";
	}
	if (xPos <= CURSOR_BORDER_WIDTH) {
		cursorName += "w";
	} else if (xPos >= this.offsetWidth - CURSOR_BORDER_WIDTH) {
		cursorName += "e";
	}

	if (cursorName == "") {
		setCursor(this, "");
		return;
	}
	cursorName += "-resize";
	setCursor(this, cursorName);
}
function unsetCursorResize(event) {  
	if(!resizer.isResizing){
		setCursor(this, "");
	}
}
function startResizing(event){
	if(!checkEventTarget(this, event)){
		return;
	}
	if(resizer.currentCursor == "") {
		return;
	}
	resizer.cursorPosX = getCursorPosX(event);
	resizer.cursorPosY = getCursorPosY(event);
	resizer.targetBox  = new AjBox(this);
	
	addEventListenerGen(document,"mousemove",performResizing);
	addEventListenerGen(document,"mouseup",stopResizing);
	
	resizer.isResizing = true;
	resizer.targetDiv = this;
}
function performResizing(event){
	var dx = getCursorPosX(event) - resizer.cursorPosX;
	var dy = getCursorPosY(event) - resizer.cursorPosY;
	var moveTopBorder    = (resizer.currentCursor.charAt(0) == 'n');
	var moveBottomBorder = (resizer.currentCursor.charAt(0) == 's');	
	var moveLeftBorder   = (resizer.currentCursor.charAt(0) == 'w' || resizer.currentCursor.charAt(1) == 'w');
	var moveRightBorder  = (resizer.currentCursor.charAt(0) == 'e' || resizer.currentCursor.charAt(1) == 'e');
	
	if(moveTopBorder){
		var newTop = resizer.targetBox.boxTop + dy;
		moveTopBorderTo(resizer.targetDiv, newTop);
		moveTopStickyBordersTo(resizer.targetDiv, resizer.targetBox.boxTop + dy);
	}
	if(moveBottomBorder){
		moveBottomBorderTo(resizer.targetDiv, resizer.targetBox.boxTop + resizer.targetBox.boxHeight + dy);
		moveBottomStickyBordersTo(resizer.targetDiv, resizer.targetBox.boxTop + resizer.targetBox.boxHeight + dy);
	}
	if(moveLeftBorder){
		moveLeftBorderTo(resizer.targetDiv, resizer.targetBox.boxLeft  + dx);
		moveLeftStickyBordersTo(resizer.targetDiv, resizer.targetBox.boxLeft  + dx);
	}
	if(moveRightBorder){
		moveRightBorderTo(resizer.targetDiv, resizer.targetBox.boxLeft + resizer.targetBox.boxWidth + dx);
		moveRightStickyBordersTo(resizer.targetDiv, resizer.targetBox.boxLeft + resizer.targetBox.boxWidth + dx);
	}
	
}
function moveTopBorderTo(aDstDiv, aY){
	var dstBox = new AjBox(aDstDiv);
	var newTop = aY;
	newTop =  Math.max(newTop, getTopBorderMinLimit(aDstDiv));
	newTop =  Math.min(newTop, getTopBorderMaxLimit(aDstDiv));
	var newHeight = dstBox.boxHeight + dstBox.boxTop - newTop;
	newHeight =  Math.max(newHeight, MINIMUM_HEIGHT);
	dstBox.boxTop = newTop;
	dstBox.boxHeight = newHeight;
	dstBox.applyBoxValues();
}
function moveBottomBorderTo(aDstDiv, aY){
	var dstBox = new AjBox(aDstDiv);
	var newBottom = aY;
	newBottom =  Math.max(newBottom, getBottomBorderMinLimit(aDstDiv));
	newBottom =  Math.min(newBottom, getBottomBorderMaxLimit(aDstDiv));	
	var newHeight =  newBottom - dstBox.boxTop;	
	newHeight =  Math.max(newHeight, MINIMUM_HEIGHT);
	dstBox.boxHeight = newHeight;
	dstBox.applyBoxValues();
}
function moveLeftBorderTo(aDstDiv, aX){
	var dstBox = new AjBox(aDstDiv);
	var newLeft = aX;
	newLeft =  Math.max(newLeft, getLeftBorderMinLimit(aDstDiv));
	newLeft =  Math.min(newLeft, getLeftBorderMaxLimit(aDstDiv));
	var newWidth = dstBox.boxWidth + dstBox.boxLeft - newLeft;
	newWidth =  Math.max(newWidth, MINIMUM_WIDTH);
	dstBox.boxLeft = newLeft;
	dstBox.boxWidth = newWidth;
	dstBox.applyBoxValues();
}
function moveRightBorderTo(aDstDiv, aX){
	var dstBox = new AjBox(aDstDiv);
	var newRight = aX;
	newRight =  Math.max(newRight, getRightBorderMinLimit(aDstDiv));
	newRight =  Math.min(newRight, getRightBorderMaxLimit(aDstDiv));
	var newWidth =  newRight - dstBox.boxLeft;
	newWidth =  Math.max(newWidth, MINIMUM_WIDTH);
	dstBox.boxWidth = newWidth;
	dstBox.applyBoxValues();
}
function getTopBorderMaxLimit(aDiv){
	var limit =  parseInt(aDiv.style.top) +  parseInt(aDiv.style.height) -  MINIMUM_HEIGHT;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[TOP_TO_TOP];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.min(limit, parseInt(arr[i].srcDiv.style.top)+ parseInt(arr[i].srcDiv.style.height) - MINIMUM_HEIGHT);
			}
		}
	}
	return limit;
}
function getTopBorderMinLimit(aDiv){
	var limit = 0;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[TOP_TO_BOTTOM];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.max(limit, parseInt(arr[i].srcDiv.style.top) + MINIMUM_HEIGHT);
			}
		}
	}
	return limit;
}
function getBottomBorderMaxLimit(aDiv){
	var limit =  99999;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[BOTTOM_TO_TOP];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.min(limit, parseInt(arr[i].srcDiv.style.top)+ parseInt(arr[i].srcDiv.style.height) - MINIMUM_HEIGHT);
			}
		}
	}
	return limit;
}
function getBottomBorderMinLimit(aDiv){
	var limit = parseInt(aDiv.style.top) + MINIMUM_HEIGHT;;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[BOTTOM_TO_BOTTOM];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.max(limit, parseInt(arr[i].srcDiv.style.top) + MINIMUM_HEIGHT);
			}
		}
	}
	return limit;
}
function getLeftBorderMaxLimit(aDiv){
	var limit =  parseInt(aDiv.style.left) +  parseInt(aDiv.style.width) -  MINIMUM_WIDTH;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[LEFT_TO_LEFT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.min(limit, parseInt(arr[i].srcDiv.style.left)+ parseInt(arr[i].srcDiv.style.width) - MINIMUM_WIDTH);
			}
		}
	}
	return limit;
}
function getLeftBorderMinLimit(aDiv){
	var limit = 0;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[LEFT_TO_RIGHT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.max(limit, parseInt(arr[i].srcDiv.style.left) + MINIMUM_WIDTH);
			}
		}
	}
	return limit;
}
function getRightBorderMaxLimit(aDiv){
	var limit =  99999;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[RIGHT_TO_LEFT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.min(limit, parseInt(arr[i].srcDiv.style.left)+ parseInt(arr[i].srcDiv.style.width) - MINIMUM_WIDTH);
			}
		}
	}
	return limit;
}
function getRightBorderMinLimit(aDiv){
	var limit = parseInt(aDiv.style.left) + MINIMUM_WIDTH;;
	var resizer = resizableList[aDiv.id];
	if(resizer && resizer.sticky) {
		var arr = resizer.jointures[RIGHT_TO_RIGHT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {		
				limit = Math.max(limit, parseInt(arr[i].srcDiv.style.left) + MINIMUM_WIDTH);
			}
		}
	}
	return limit;
}
function moveTopStickyBordersTo(aDstDiv, aY){
	var resizer = resizableList[aDstDiv.id];
	if(resizer && resizer.sticky){
		var arr = resizer.jointures[TOP_TO_TOP];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveTopBorderTo(arr[i].srcDiv, aY);
			}
		}
		var arr = resizer.jointures[TOP_TO_BOTTOM];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveBottomBorderTo(arr[i].srcDiv, aY);
			}
		}		
	}
}
function moveBottomStickyBordersTo(aDstDiv, aY){
	var resizer = resizableList[aDstDiv.id];
	if(resizer && resizer.sticky){
		var arr = resizer.jointures[BOTTOM_TO_TOP];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveTopBorderTo(arr[i].srcDiv, aY);
			}
		}
		var arr = resizer.jointures[BOTTOM_TO_BOTTOM];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveBottomBorderTo(arr[i].srcDiv, aY);
			}
		}		
	}
}
function moveLeftStickyBordersTo(aDstDiv, aX){
	var resizer = resizableList[aDstDiv.id];
	if(resizer && resizer.sticky){
		var arr = resizer.jointures[LEFT_TO_LEFT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveLeftBorderTo(arr[i].srcDiv, aX);
			}
		}
		var arr = resizer.jointures[LEFT_TO_RIGHT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveRightBorderTo(arr[i].srcDiv, aX);
			}
		}		
	}
}
function moveRightStickyBordersTo(aDstDiv, aX){
	var resizer = resizableList[aDstDiv.id];
	if(resizer && resizer.sticky){
		var arr = resizer.jointures[RIGHT_TO_LEFT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveLeftBorderTo(arr[i].srcDiv, aX);
			}
		}
		var arr = resizer.jointures[RIGHT_TO_RIGHT];
		if(arr != null) {
			for (var i=0; i< arr.length; i++) {			
				moveRightBorderTo(arr[i].srcDiv, aX);
			}
		}		
	}
}
function stopResizing(event){
	resizer.isResizing = false;
	removeEventListenerGen(document,"mousemove",performResizing);
	removeEventListenerGen(document,"mouseup",stopResizing);
	resizer.targetDiv = null;
}

function initSticky(){
	var stickyList = new Array();
	for (var key in resizableList){
		var value = resizableList[key];
		if(value.sticky){			
			stickyList[stickyList.length] = value;
		} 
	}
	for ( var i = 0; i < stickyList.length; i++ ) {
		for ( var j = i+1; j < stickyList.length; j++ ) {
			findCommonBorders(stickyList[i],stickyList[j]);
		}		
	}
}
function findCommonBorders(firstObj, secondObj){
	if(top(firstObj) == top(secondObj)){	
		firstObj.addJointure(TOP_TO_TOP, secondObj);
		secondObj.addJointure(TOP_TO_TOP, firstObj);
	} else if(top(firstObj) == bottom(secondObj)){
		firstObj.addJointure(TOP_TO_BOTTOM, secondObj);
		secondObj.addJointure(BOTTOM_TO_TOP, firstObj);
	}
	if(bottom(firstObj) == bottom(secondObj)){
		firstObj.addJointure(BOTTOM_TO_BOTTOM, secondObj);
		secondObj.addJointure(BOTTOM_TO_BOTTOM, firstObj);
	} else if(bottom(firstObj) == top(secondObj) ){
		firstObj.addJointure(BOTTOM_TO_TOP, secondObj);
		secondObj.addJointure(TOP_TO_BOTTOM, firstObj);
	} 	
	if(left(firstObj) == left(secondObj)){
		firstObj.addJointure(LEFT_TO_LEFT, secondObj);
		secondObj.addJointure(LEFT_TO_LEFT, firstObj);
	} else if(left(firstObj) == right(secondObj)){
		firstObj.addJointure(LEFT_TO_RIGHT, secondObj);
		secondObj.addJointure(RIGHT_TO_LEFT, firstObj);
	}
	if(right(firstObj) == left(secondObj)){
		firstObj.addJointure(RIGHT_TO_LEFT, secondObj);
		secondObj.addJointure(LEFT_TO_RIGHT, firstObj);
	} else if(right(firstObj) == right(secondObj)){
		firstObj.addJointure(RIGHT_TO_RIGHT, secondObj);
		secondObj.addJointure(RIGHT_TO_RIGHT, firstObj);
	}
}

/*
*
* 	Initialize all lists.
*
*/

var resizableList = new Array();

function initAll() {
  var divList = document.getElementsByTagName( "div" );
  for ( var i = 0; i < divList.length; i++ ) {
	var curDiv = divList[i];
	
	if( divHasClass( curDiv,"resizeable" ) ) {
		// add to resizeable list
		resizableList[curDiv.id] = new DjResizeable( curDiv );
		//printDebug('RESIZEABLE DIV:'+curDiv.id+' CLASS='+curDiv.className);
	}
  }
  initSticky();
}

addEventListenerGen(window,"load",initAll);

//printDebug('INFO: Resizable Library loaded successfully');

