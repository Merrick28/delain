var CADRE_CLASS_NAME = 'encadrer:';

function encadrer(div){
  //printDebug('Encadrer:' + div.id + div.width);
  var parentElement = div.parentNode;
  var nextElement = div.nextSibling;
  var table = document.createElement("table");
  var style = getStyle(div);
  table.className = style;
  table.border = '0px';
  table.cellSpacing = '0px';
  table.cellPadding = '0px';
  //héritage des dimentions
  table.style.width = getElementCssValue(div,'width');
  table.style.height = getElementCssValue(div,'height');
  //printDebug('PARENT HEIGHT('+div.id+')='+getElementCssValue(div,'height')+'--'+div.style.height);
  var tbody = document.createElement("tbody");
  // ligne haut
  var trh = document.createElement("tr");
  var tdhg = document.createElement("td");
  var tdhc = document.createElement("td");
  var tdhd = document.createElement("td");
  tdhg.className = style+'_hg';
  tdhc.className = style+'_hc';
  tdhd.className = style+'_hd';
  trh.appendChild(tdhg);
  trh.appendChild(tdhc);
  trh.appendChild(tdhd);
  tbody.appendChild(trh);
  // Ligne milieu
  var tr = document.createElement("tr");
  var tdg = document.createElement("td");
  var tdc = document.createElement("td");
  var tdd = document.createElement("td");
  tdg.className = style+'_g';
  tdd.className = style+'_d';
  tdc.className = style+'_c';
  tdc.appendChild(div);
  //tdc.width = div.clientWidth;
  //tdc.height = div.clientHeight;
  tr.appendChild(tdg);
  tr.appendChild(tdc);
  tr.appendChild(tdd);
  tbody.appendChild(tr);
  // ligne bas
  var trb = document.createElement("tr");
  var tdbg = document.createElement("td");
  var tdbc = document.createElement("td");
  var tdbd = document.createElement("td");
  tdbg.className = style+'_bg';
  tdbc.className = style+'_bc';
  tdbd.className = style+'_bd';
  trb.appendChild(tdbg);
  trb.appendChild(tdbc);
  trb.appendChild(tdbd);
  tbody.appendChild(trb);
  table.appendChild(tbody);
  parentElement.insertBefore(table, nextElement);

}

function getStyle(div){
  var res = div.className;
  res = res.substr(res.indexOf(CADRE_CLASS_NAME) + CADRE_CLASS_NAME.length);
  res = (res.indexOf(' ') < 0)?res:res.substr(0,res.indexOf(' '));
  return res;
}

function initEncadrement() {
  var divList = document.getElementsByTagName( "div" );
  for ( var i = 0; i < divList.length; i++ ) {
	  var curDiv = divList[i];
	  //printDebug('Encadrer ??:'+curDiv.id+curDiv.className);
	  if( divHasClass( curDiv,CADRE_CLASS_NAME ) ) {
      encadrer(curDiv);
	  }
  }
}

addEventListenerGen(window,"load",initEncadrement);

//printDebug('INFO: Movable Library loaded successfully');

