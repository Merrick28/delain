var DEBUG_DIV_ID = 'debug_jlib:debug_log';
var LOG_LVL_DEBUG = 1;
var LOG_LVL_INFO = 5;
var LOG_LVL_WARNING = 10;
var LOG_LVL_ERROR = 15;
var currentLogLevel = LOG_LVL_DEBUG;

var LOG_LVL_TXT = {
            1: "Debug",
            5: "Info",
            10: "Warning",
            15: "Error"};

function printDebug(text){
  printLog (LOG_LVL_DEBUG, text);
}
function printInfo(text){
  printLog (LOG_LVL_DEBUG, text);
}
function printWarning(text){
  printLog (LOG_LVL_DEBUG, text);
}
function printError(text){
  printLog (LOG_LVL_DEBUG, text);
}
function printLog (errorLevel, text){
  if(errorLevel >= currentLogLevel){
    var debugDiv = document.getElementById( DEBUG_DIV_ID );
      if( !isDefined( debugDiv ) ) {
        initDebug();
        debugDiv = document.getElementById( DEBUG_DIV_ID );
      }
    var p = document.createElement('p');
    debugDiv.appendChild(p);
    p.appendChild(document.createTextNode(LOG_LVL_TXT[5]+":"+text));
  }
}
function initDebug(){
   var el = document.createElement('div');
   el.id = DEBUG_DIV_ID;
   el.setAttribute('class','debug_log mobile');
   var titre = document.createElement('div');
   titre.id = HOOK_PREFIX + DEBUG_DIV_ID;
   titre.setAttribute('class','cadre');
   el.appendChild(titre);
   titre.appendChild(document.createTextNode('Debug Log'));
   document.body.appendChild(el);
   
   initAllDjMobile();
}
