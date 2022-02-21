var HOOK_PREFIX = 'hook:';
// Objet DjMobile
function DjMobile(aSrcDiv) {
	this.srcDiv = aSrcDiv;
	this.srcHookDiv = aSrcDiv;
	// Recherche d'une zone d'accorche	
	var hook = document.getElementById(HOOK_PREFIX + aSrcDiv.id);
	if(hook){
		this.srcHookDiv = hook;
	}
	// Association zone d'accorche/zone cible
	hookToTarget[this.srcHookDiv.id] = this.srcDiv;
	initStylePositionFromClassPosition(this.srcDiv);
	addEventListenerGen(this.srcHookDiv,"mousedown",startMoving);
}
var activeMobile = null;
var hookToTarget = new Array();
// Objet DjMover
function DjMover(){
	// Variables
	this.cursorPosX = null;
	this.cursorPosY = null;
	this.targetBox = null;
	this.isMoving = false;
	this.targetDiv = null;
}
var mover = new DjMover();

function startMoving(event) {
	var evtTarget = getEventTarget(this,event);
	if(!evtTarget){		
		return;
	}
	// cas: mobile && resizeable
	var resizer;
	if(resizer != undefined && resizer.isResizing) {
		return;
	}
	
	mover.cursorPosX = getCursorPosX(event);
	mover.cursorPosY = getCursorPosY(event);
	setActive(evtTarget);
	var target = hookToTarget[evtTarget.id];
	mover.targetBox  = new AjBox(target);
	mover.isMoving = true;
	mover.targetDiv = target;
	
	addEventListenerGen(document,"mousemove",performMoving);
	addEventListenerGen(document,"mouseup",stopMoving);
}
function performMoving(event) {
	
	var dx = getCursorPosX(event) - mover.cursorPosX;
	var dy = getCursorPosY(event) - mover.cursorPosY;
	var dstBox = new AjBox(mover.targetDiv);
	dstBox.boxTop = mover.targetBox.boxTop  + dy;
	dstBox.boxLeft = mover.targetBox.boxLeft  + dx;
	dstBox.applyBoxValues();
}
function stopMoving(event) {
	removeEventListenerGen(document,"mousemove",performMoving);
	removeEventListenerGen(document,"mouseup",stopMoving);
	mover.isMoving = false;
	mover.targetDiv = null;
}

function setActive(hookDiv){
  if ( activeMobile != null
      && activeMobile.id != hookDiv.id
      && divHasClass( activeMobile, "isactive" ) ) {
    replaceClass(activeMobile.id, "isactive", "inactive");
    //printDebug('Hook ID='+ activeMobile.id);
    hookToTarget[activeMobile.id].style.zIndex = "1";
  }
  if ( divHasClass( hookDiv, "inactive" ) ) {
    replaceClass(hookDiv.id, "inactive", "isactive");
  } else {
    addClass(hookDiv.id, "isactive");
  }
  hookToTarget[hookDiv.id].style.zIndex = "100";
  activeMobile = hookDiv;
}
/*
*
* 	Initialize mobile list.
*
*/

var mobileList = new Array();

function initAllDjMobile() {
  mobileList = new Array();
  var divList = document.getElementsByTagName( "div" );
  for ( var i = 0; i < divList.length; i++ ) {
	var curDiv = divList[i];	
	if( divHasClass( curDiv,"mobile" ) ) {
		// add to mobile list
		mobileList[curDiv.id] = new DjMobile( curDiv );
	}
  }
}

addEventListenerGen(window,"load",initAllDjMobile);

//printDebug('INFO: Movable Library loaded successfully');

