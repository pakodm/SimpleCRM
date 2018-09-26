var cm = 99;
var origClass = 'LinkMenuA';
var sliderIntervalId = 0;
var sliderHeight = 0;
var sliding = false;
var slideSpeed = 10;
var slideId = null;
var slideMax = 200;
var loginb = null;
var wsopen = false;
var logBtnLeft = 0;

function _$(elmId){
	var the_Elm = document.getElementById(elmId);
	if (the_Elm == null){
		if (frames.length > 0){
			try{
				the_Elm = frames[0].document.getElementById(elmId);
			}catch(err){
			}
		}
	}
	if (the_Elm == null){
		the_Elm = parent.document.getElementById(elmId);
	}
	return the_Elm;
}

function doUpperCase(e){
	if (e.value){
		s = e.value;
		e.value = s.toUpperCase();
	}
}

function getXYLocation(elm){
	var x = y = 0;
	if (elm.offsetParent){
		do{
			x += elm.offsetLeft;
			y += elm.offsetTop;
		}while (elm = elm.offsetParent);
	}
	return [x,y];
}

function updateLWPos(b){
	if (b == null){return;}
	var rect = b.getBoundingClientRect();
	var lpos = (rect.left + 88) - 300;
	_$('wlogin').style.left = lpos+'px';
}

function Slide(chk,id,m){
	if(sliding) return;
	slideId = id;
	sliding = true;
	//if(sliderHeight == 75)
	if (!chk){
		sliderHeight = parseInt(_$(slideId).style.height); //slideMax;
		sliderIntervalId = setInterval('SlideUpRun()', 30);
		//_$(slideId).style.top = '100px';
	}else{
		slideMax = m;
		sliderHeight = 5;
		sliderIntervalId = setInterval('SlideDownRun()', 30);
		_$(slideId).style.top = '55px';
	}
}

function SlideUpRun(){
	slider = _$(slideId);
	if(sliderHeight <= 5){
		sliding = false;
		sliderHeight = 5;
		slider.style.height = '5px';
		slider.style.top = '55px';
		clearInterval(sliderIntervalId);
		slider.style.display = 'none';
	}else{
		sliderHeight -= slideSpeed;
		if(sliderHeight <5)
			sliderHeight = 5;
		slider.style.height = sliderHeight + 'px';
	}
}

function SlideDownRun(){
	slider = _$(slideId);
	if(sliderHeight>= slideMax){
		sliding = false;
		sliderHeight = slideMax;
		slider.style.height = slideMax+'px';
		clearInterval(sliderIntervalId);
	}else{
		//if (slider.style.display == 'none') slider.style.display = 'block';
		slider.style.display = 'block';
		sliderHeight += slideSpeed;
		if(sliderHeight> slideMax)
			sliderHeight = slideMax;
		slider.style.height = sliderHeight + 'px';
	}
}

function handleSlide(o,id,m){
	if (sliding) return;
	if (o.src.indexOf('login-btn.png') > 0){
		o.src = 'images/login-btn-open.png';
		wsopen = true;
		Slide(true,id,m);
	}else{
		o.src = 'images/login-btn.png';
		wsopen = false;
		Slide(false,id,m);
	}
}

function getPageScroll(){
	var yScroll;
	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}
	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll;
}

function getPageSize(){
	var xScroll, yScroll;
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}
	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}

function showLogin(i){
	//alert(i);
	loginb = i;
	updateLWPos(i);
	handleSlide(i,'wlogin',200);
	//document.getElementById('wlogin').style.display = "block";
}

function hideLogin(e){
	//alert('a');
	var targ;
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;
	if (targ.id == 'wlogin')
		return;
	for (i=0; i<6; i++){
		targ = targ.parentNode;
		if (targ == null) break;
		if (targ.id == 'wlogin') return;
	}
	if (wsopen){ _$('fw_result').innerHTML = "&nbsp;"; handleSlide(loginb,'wlogin',5); }
}

function isEmpty(s){
	s = trim12(s);
	return s.length == 0 ? true : false;
}

function checkRegData(x){
	var filter = /^[a-z0-9ñ\%\-\. ]{1,}$/i;
	return filter.test(x);
}

function checkUserName(x){
	var filter = /^[a-z0-9\-\.\_]{1,}$/i;
	return filter.test(x);
}

function checkRFCData(x){
	if ((x.length < 12) || (x.length > 13)){return false;}
	var filter = /^[a-z0-9]{1,}$/i;
	return filter.test(x);
}

function checkNumData(x){
	var filter = /^[0-9\.]{1,}$/i;
	return filter.test(x);
}

function validatel(str){
	k=str.length;
	data = new Array("0","1","2","3","4","5","6","7","8","9","(",")","-"," ");
	for(var i=0; i<k; i++){
		x=str.charAt(i);
		if (!data.isInArray(x)){
			return false;	
		}
	}
	return true;
}

function checkMail(x){
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return filter.test(x);
}

function trim12(str) {
	var	str = str.replace(/^\s\s*/, ''),
		ws = /\s/,
		i = str.length;
	while (ws.test(str.charAt(--i)));
	res = str.slice(0, i + 1);
	str_a = res.split(/\s/);
	x = "";
	for(i=0; i<str_a.length-1; i++){
		if ((str_a[i] != /\s/) && (str_a[i] != '')){
			x += str_a[i]+' '; 
		}
	}
	x += str_a[str_a.length-1];
	return x;
}

function selectByText(sid,txt){
	sel = _$(sid);
	if (sel){
		for(i=0; i<sel.options.length; i++){
			if (sel.options[i].text === txt){
				sel.selectedIndex = i;
				break;
			}
		}
	}
}

function selectByValue(sid,val){
	sel = _$(sid);
	if (sel){
		if (val === "0"){val = "NV";}
		for(i=0; i<sel.options.length; i++){	
			if (sel.options[i].value === val){
				sel.selectedIndex = i;
				break;
			}
		}
	}
}

/*
Returns the time between two dates in minutes
ds1, ds2: Format yyyy-mm-dd hh:mm:ss
*/
function getTimeBetweenDates(ds1,ds2){
	date1 = ds1.split(' '); date2 = ds2.split(' ');
	fecha1 = date1[0].split('-'); fecha2 = date2[0].split('-');
	month1 = getMonthName(fecha1[1]); month2 = getMonthName(fecha2[1]);
	var d1 = month1+", "+fecha1[2]+" "+fecha1[0]+" "+date1[1];
	var d2 = month2+", "+fecha2[2]+" "+fecha2[0]+" "+date2[1];
	var ms1 = Date.parse(d1);
	var ms2 = Date.parse(d2);
	var dif = (ms2 - ms1)/1000/60;
	return dif;
}

function getMonthName(m){
	if (m.indexOf("0") == 0){m = m.substring(1)};
	m_name = "";
	switch (eval(m)){
		case 1:m_name = "January";break;
		case 2:m_name = "February";break;
		case 3:m_name = "March";break;
		case 4:m_name = "April";break;
		case 5:m_name = "May";break;
		case 6:m_name = "June";break;
		case 7:m_name = "July";break;
		case 8:m_name = "August";break;
		case 9:m_name = "September";break;
		case 10:m_name = "October";break;
		case 11:m_name = "November";break;
		case 12:m_name = "December";break;
	}
	return m_name;
}

function openWindow(target,url,width,height){
	var wndHandler = null;
	var left=(screen.width-width)/2;
	var top=(screen.height-height)/2;
	var winargs="width="+width+",height="+height+",resizable=No,location=No,scrollbars=yes,status=Yes,modal=yes,dependent=yes,dialog=yes,left="+left+",top="+top;
	wndHandler=window.open(url,target,winargs);
	return wndHandler;
}

Array.prototype.isInArray = function(val){
	for(i=0; i<this.length; i++){
		if (this[i] == val){
			return true;
		}
	}
	return false;
}

String.prototype.sanitize = function(){
	str = this.replace(/[^a-z0-9áéíóúñü\s\&\%\$\?\"\{\}\[\]\.,_\-\/]/gim,"");
	return str;
}

/*
if (window.attachEvent){
	window.attachEvent('onclick',hideLogin);
	document.attachEvent('onclick',hideLogin);
}else{
	window.addEventListener('click',hideLogin,false);
}
*/