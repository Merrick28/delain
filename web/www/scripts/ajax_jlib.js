

// Objet AjZone
function AjZone(p_name, p_url, p_skin) {
	this.name = p_name;
	this.url = p_url;
	this.skin = p_skin;
}

function reloadContent() {
	printDebug("reloadContent");
}
AjZone.prototype.reloadContent = reloadContent;
function loadContent(p_xml_content) {
	printDebug("loadContent");
}
AjZone.prototype.loadContent = loadContent;
function loadSkin() {
	printDebug("loadSkin");
}
AjZone.prototype.loadSkin = loadSkin;

// objet AjActionHandler
function AjActionHandler(p_name) {
	this.name = p_name;
}
function showZone(p_zone_name) {
	printDebug("showZone");
}
AjActionHandler.prototype.showZone = showZone;
function hideZone(p_zone_name) {
	printDebug("hideZone");
}
AjActionHandler.prototype.hideZone = hideZone;
function drawZone(p_zone_name) {

}
AjActionHandler.prototype.drawZone = drawZone;

function handleResult(resultNode) {
	var status = resultNode.firstChild;
	if(!status) {
		printDebug("Erreur Grave: retour non valide");
		return;
	}
	printDebug("Debug: Retour CODE="+status.getAttribute('code') + " MESSAGE="+status.getAttribute('message'));
	for(var c = 0;c < resultNode.childNodes.length;++c) {
		chNode = resultNode.childNodes[c];
		if(chNode.nodeName == 'action') {
			var zoneName = chNode.getAttribute('zone');
			var type = chNode.getAttribute('type');
			var data = null;
			if(chNode.hasChildNodes()) {
				data = chNode.firstChild;
			}
			
			printDebug("Debug ACTION TYPE="+type+" ZONE="+zoneName);
			var zoneDiv = document.getElementById(zoneName);
			if(zoneDiv) {
				initStylePositionFromClassPosition(zoneDiv);
			}
			// recherche de la fonction
			var f = eval(type);
			if(f != null && typeof f == 'function') {
				f.call(this, zoneDiv, data);
			} else {
				printDebug("Can't find function:"+type);
			}
		}
	}
}
function handleShowZone(aZone) {
	printDebug("Debug SHOW ZONE");
}
function handleHideZone(aZone) {
	printDebug("Debug SHOW ZONE");
}
function handleMoveZone(aZone, aData) {
	var coordNode = getNodeByName(aData, 'coords');
	var x =  coordNode.getAttribute('x');
	var y =  coordNode.getAttribute('y');	
	var box = new AjBox(aZone);
	box.boxTop = y;
	box.boxLeft = x;
	box.applyBoxValues();
	printDebug("Debug MOVE ZONE III X="+x+" Y="+y);
}
function handleResizeZone(aZone, aData) {
	var dimNode = getNodeByName(aData, 'dim');
	var width =  dimNode.getAttribute('width');
	var height =  dimNode.getAttribute('height');
	var box = new AjBox(aZone);
	box.boxWidth = width;
	box.boxHeight = height;
	box.applyBoxValues();
	printDebug("Debug RESIZE ZONE W="+width+" H="+height);
}
function handleUpdateForm(aZone, aData) {
	printDebug("Debug UPDATE FORM");
}
function handleExecuteScript(aZone, aData) {
	var scriptNode = getNodeByName(aData, 'script');
	var code =  scriptNode.getAttribute('code');
	try {
    eval(code);
  }
  catch (e) {
    printError("Error EXECUTE SCRIPT");
  }
	printDebug("Debug EXECUTE SCRIPT");
}
function submitForm()
{ 
    
    var xhr; 
    try { xhr = new XMLHttpRequest(); }                 
    catch(e) 
    {    
      xhr = new ActiveXObject(Microsoft.XMLHTTP);
    } 

    xhr.onreadystatechange  = function()
    { 
         if(xhr.readyState  == 4)
         {
              if(xhr.status  == 200) 
                 document.ajax.dyn="Received:"  + xhr.responseText; 
              else 
                 document.ajax.dyn="Error code " + xhr.status;
         }
    }; 

   xhr.open( "GET", "data.xml",  true); 
   xhr.send(null); 
}

function stringToNode(aStr){
	var doc; 
	if (document.implementation.createDocument) { 
		// Mozilla, create a new DOMParser 
		var parser = new DOMParser(); 
		doc = parser.parseFromString(aStr, "text/xml"); 
	} else if (window.ActiveXObject) { 
		// Internet Explorer, create a new XML document using ActiveX 
		// and use loadXML as a DOM parser. 
		doc = new ActiveXObject("Microsoft.XMLDOM") 
		doc.async="false"; 
		doc.loadXML(aStr);   
	}
	return doc;
}


function getZoneContent(){
	return  stringToNode("<p id='toto'> test <A href=\"#\">gras</A> fin test</p>");
}
function getTestActionResult(){
	var str = "<result date='13-06-2007 18:30:55'>"
		+"<status code='OK' message='the request was successful'/>"
		+"<action zone='zone1' type='handleExecuteScript'><data><script code=\"document.getElementById('debug').value='';\" /></data></action>"
		+"<action zone='zone1' type='handleShowZone'/>"
		+"<action zone='zone1' type='handleMoveZone'><data><coords x='300' y='300' /></data></action>"
		+"<action zone='zone1' type='handleResizeZone'><data><dim width='150' height='180' /></data></action>"
		+"</result>";
	return stringToNode(str).firstChild;
}
function setDisplayClass(aZoneName,aClassName){
	document.getElementById(aZoneName).className = aClassName;
}
function updateZonecontent(aZoneName){
	//alert('ZC='+describeNode(getZoneContent()));
	//document.getElementsByTagName("body")[0].appendChild(getZoneContent());
	removeAllChilds(document.getElementById(aZoneName));
	//document.getElementById(aZoneName).appendChild(getZoneContent().firstChild);
	 copyElements(getZoneContent(), document.getElementById(aZoneName));
	//setDisplayClass("toto","testClass2");
	//document.getElementById("zone1").normalize();
	//printDebug('Z1='+describeNode(document.getElementById("zone1")));
	//printDebug('Z2='+describeNode(document.getElementById("zone2")));
	handleResult(getTestActionResult());
}
function describeNode(aNode){
	res = aNode.nodeName;
	res += '%'+aNode.textContent+'//'+ aNode.nodeValue+'%'
	if(aNode.type) {
		res += '('+aNode.type+'//'+ aNode.nodeType+')';
	}
	if(aNode.attributes && aNode.attributes.length > 0) {
		res += '[';
		for(var c = 0;c < aNode.attributes.length;++c){
			chNode = aNode.attributes[c];
			res += chNode.nodeName + " = " + chNode.nodeValue;
			if(c < (aNode.attributes.length-1)){
				res += ", ";
			}
		}
		res += ']\n';
	}
	if(aNode.childNodes && aNode.childNodes.length > 0) {
		res += '{{';
		for(var c = 0;c < aNode.childNodes.length;++c){
			res += '- ';
			chNode = aNode.childNodes[c];
			res += describeNode(chNode);
			if(c < (aNode.childNodes.length-1)){
				res += "\n";
			}		
		}
		res += '}}';
		
	}
	return res;
}
function removeAllChilds(aNode){
	while(aNode.firstChild){
		aNode.removeChild(aNode.firstChild);
	}
}

function getNodeByName(aNode, aName){
	for(var i = 0;i < aNode.childNodes.length;++i){
		chNode = aNode.childNodes[i];
		if(chNode.nodeName == aName) {
			return chNode;
		}	
	}
	return null;
}

function cleanNode(aNode){
	if(aNode.hasChildNodes()){
	
	} else {
	
	}
}

function printDebug(text){
	if(document.getElementById('debug')){
		document.getElementById('debug').value += text + '\n';
	} else {
		alert('DEBUG'+text);
	}
}
function copyElements(srcNode, dstNode){
	if(srcNode.hasChildNodes()){
		for(var c = 0;c < srcNode.childNodes.length;++c){
			chNode = srcNode.childNodes[c];
			if(chNode.nodeName == "#text"){
				if(browser.isIE){
					dstNode.appendChild(document.createTextNode(chNode.text));
				} else {
					dstNode.appendChild(document.createTextNode(chNode.textContent));
				}
			} else {
				chCopyNode = document.createElement(chNode.nodeName);
				for(var j = 0;j < chNode.attributes.length;++j){
					chAttr = chNode.attributes[j];
					chCopyNode.setAttribute(chAttr.nodeName,chAttr.nodeValue);
				}
				dstNode.appendChild(chCopyNode);
				copyElements(chNode,chCopyNode);
			}
		}
	}
}


