var TT_PREFIX = 'tooltip:';
var TT_DELAY_SHOW = 500;
var TT_DELAY_HIDE = 50;
// Objet DjToolTip
function DjToolTip( aSrcDiv ) {
	this.srcDiv = aSrcDiv;
	var targetDivName = aSrcDiv.id.substr( TT_PREFIX.length );
	this.targetDiv = document.getElementById( targetDivName );
	if( this.targetDiv ){
	 addEventListenerGen(this.targetDiv,"mousemove",showTooltip);
	 addEventListenerGen(this.targetDiv,"mouseout",hideTooltip);
  }
}
function showTooltip(event){
  if(ttDisplayer.target == '' || ttDisplayer.target != this.id) {
    ttDisplayer.target = this.id;
    ttDisplayer.targetDiv = this;
    window.setTimeout(effectiveShowTooltip,TT_DELAY_SHOW , this.id);
  }
}
function effectiveShowTooltip(targetId){
  if(ttDisplayer.target == targetId){
    var toolTipDiv = document.getElementById( TT_PREFIX + targetId );
    showDiv(toolTipDiv);
    removeEventListenerGen(ttDisplayer.targetDiv,"mousemove",showTooltip);
  }
}
function hideTooltip(event){
  var toolTipDiv = document.getElementById( TT_PREFIX+this.id );
  hideDiv(toolTipDiv);
  addEventListenerGen(this,"mousemove",showTooltip);
  ttDisplayer.target = '';
  ttDisplayer.time = null;
}
function TTDisplayer(){
  this.targetDiv = null;
  this.targetId = '';
  this.time = null;
}
var ttDisplayer = new TTDisplayer();
/*
*
* 	Initialize mobile list.
*
*/

var toolTipList = new Array();

function initAllDjToolTip() {
  var divList = document.getElementsByTagName( "div" );
  for ( var i = 0; i < divList.length; i++ ) {
	  var curDiv = divList[i];
	  if( curDiv.id.indexOf(TT_PREFIX) == 0 ) {
	    // add to tooltip list
		  toolTipList[curDiv.id] = new DjToolTip( curDiv );
		  //printDebug( 'INFO: new Tooltip:' + curDiv.id );
    }
  }
}

addEventListenerGen( window, "load", initAllDjToolTip );

//printDebug('INFO: ToolTip Library loaded successfully');

