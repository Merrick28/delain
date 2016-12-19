var OVERLAY_DIV_ID = 'win_jlib:overlay';
var WIN_DELAY_FADE = 1;
var WIN_INTER_FADE = 5;
function openWindow( name ) {
   replaceClass(name, 'hidden', 'visible');
   addClass(name, 'ontop');
   overlayOn();
   printDebug('open window !');
}
function closeWindow( name ) {
   replaceClass(name, 'visible', 'hidden');
   removeClass(name, 'ontop');
   overlayOff();
}

function overlayOn(){
  var ovlDiv = document.getElementById(OVERLAY_DIV_ID);
  if(isUndefined(ovlDiv)){
      var el = document.createElement('div');
      el.id = OVERLAY_DIV_ID;
      el.className = 'overlay_dialog';
      document.body.appendChild(el);
  }
  ovlDiv = document.getElementById(OVERLAY_DIV_ID);
  replaceClass(ovlDiv.id, 'hidden', 'visible');
   setOpacity( ovlDiv, 0.6 );
  // TODO: Effect pas terrible...
  //startEffect(ovlDiv.style, 'opacity', 0.6);
}
function overlayOff(){
  var ovlDiv = document.getElementById(OVERLAY_DIV_ID);
  if(!isUndefined(ovlDiv)){
      removeClass(ovlDiv.id, 'visible');
      addClass(ovlDiv.id, 'hidden');
      setOpacity( ovlDiv, 0.0 );
  }
}
function fadeInOpacity( div, op ) {
  div.style.opacity = op;
}

function setOpacity( div, op ) {
  div.style.opacity = op;
}

function startEffect(element, attrName, value){
  var diff = ( value - element[attrName] ) / WIN_INTER_FADE;
  window.setTimeout(applyFadeEffect,WIN_DELAY_FADE , WIN_INTER_FADE, element, attrName, diff);
}
function applyFadeEffect(iter, element, attrName, value) {
  /*while(iter > 1){
     iter--;
     var newVal = parseFloatGen(element[attrName]) + parseFloatGen(value);
     element[attrName] = newVal;
     sleep( WIN_DELAY_FADE );
  } */
  var newVal = parseFloatGen(element[attrName]) + parseFloatGen(value);
  //printDebug('set'+attrName+' TO '+element[attrName]+ value + newVal);
  element[attrName] = newVal;
  if(iter > 1){
     window.setTimeout(applyFadeEffect,WIN_DELAY_FADE , iter - 1, element, attrName, value);
  }
}
function sleep(time){
    var start = date.getTime();
    while(start+time > date.getTime()) true;
    return;
}
