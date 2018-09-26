var ajx = 'CRMBackManager.php'; /* Server Side Ajax Requests Handler */
var req = null;
var reqTarget = null;
var AlertOn = false;
var inProgress = false;
var postInterface = 0;
var postWorkspace = 0;
var justLogged = true;

function getWorkspace(wsid){
	if (inProgress) return;
	showLightbox('Workspace');
	reqTarget = 'wsArea';
	__shProgress(true,"");
    __initRequest();
    d = isEmpty(_$('ih_helper').value) ? "0" : _$('ih_helper').value;
    _$(reqTarget).innerHTML = "";
    _$('wsTextTitle').innerHTML = "";
    postWorkspace = wsid;
    switch(wsid){
        default: req.doGet(ajx+'?jxid=1&wsid='+wsid+'&dat='+d,_getWorkspace); break;
    }
}

function _getWorkspace(res){
	//alert(res);
	__shProgress(false,"");
    if (res.indexOf('0x') != -1){
        __handleErrors(res);
    }else{
    	dat = eval(res);
    	_$('wsTextTitle').innerHTML = dat[0];
        _$(reqTarget).innerHTML = dat[1];
        s = _$('ih_helper').value
        switch (postWorkspace){
        	case 1: s = __getCookie("fupsel");
        			if (s){
        				a = s.split(",");
        				selectByValue('cb_filt1',a[0]);
        				filterCByUser(a[0]);
        				//__removeCookie("fupsel");
        			}
        			s = __getCookie("psel"); 
        			if (s){
        				a = s.split(",");
        				getFollowUpData(a[0],a[1]); 
        				_$('ih_helper').value = ''; 
        				__removeCookie("psel");
        			} 
        			break;
        	case 2: initCalendar(false); break; 
        }
    }
}

function getSubWorkspace(wsid){
	if (inProgress) return;
	reqTarget = 'subWSArea';
	__shProgress(true,"");
    __initRequest();
    d = isEmpty(_$('ih_helper').value) ? "0" : _$('ih_helper').value;
    _$(reqTarget).innerHTML = "";
    postWorkspace = wsid;
    req.doGet(ajx+'?jxid=1&wsid='+wsid+'&dat='+d,_getSubWorkspace);
}

function _getSubWorkspace(res){
	__shProgress(false,"");
    if (res.indexOf('0x') != -1){
        __handleErrors(res);
    }else{
        _$(reqTarget).innerHTML = res;
        switch (postWorkspace){
        case 505: initCalendar(true); cal1.time_comp = false; break;
        }
    }
}

function closeWorkspace(){
	if (inProgress) return;
	hideLightbox();
	//if (inProgress){__shProgress(false,"");}
}

function initCalendar(ncal){ 
	obj = _$("tb_date1");
	if (obj){
		cal1 = new calendar3(obj);
		cal1.year_scroll = true;
		cal1.time_comp = true;
		if (ncal){
			cal2 = new calendar3(_$("tb_date2"));
			cal2.year_scroll = true;
			cal2.time_comp = false;
		}
	}
}

function showAlert(txt,title){
	try{
		AlertOn = true;
		ws = getPageSize();
		wobj = _$('winfo');
		//wobj.style.top = (arrayPageScroll[1] + ((arrayPageSize[3] - 210) / 2) + 'px');
		//wobj.style.left = (((arrayPageSize[0] - 410) / 2) + 'px');
		wobj.style.display = 'block';
		_$('InfoText').innerHTML = txt;
		_$('InfoTitle').innerHTML = title;
	}catch (ex){
		alert(txt);
	}
}

function hideAlert(){
	AlertOn = false;
	_$('winfo').style.display = 'none';
}

function __showResponse(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		_$(reqTarget).innerHTML = res;
	}
}

function __handleErrors(err){
	_err = "";
	switch (err){
		case "0x02INSERR" : _err = "An error ocurred while trying to save the data. Data not saved"; break;
		case "0x02FUPERR" : _err = "Follow up information could not be saved. Update the record on Follow Up Menu"; break;
		case "0x03ADMERR" : _err = "User not authorized to perform this operation"; break;
		case "0x03INSERR" : _err = "An error ocurred while trying to save the user. User not saved"; break;
		default : _err = err + " - Undefined Error. Refresh the web page.";
	}
	showAlert(_err,"Error Message");
}

function __initRequest(){
	req = null;
	req = new CRMAjax();
}

function __shProgress(b,lt){
	inProgress = b;
	ws = getPageSize();
	_$('LoaderInd').style.display = b ? 'block' : 'none';
	//_$('LoaderInd').style.top = parseInt(ws[1]-75)+'px';
	//_$('LoaderTxt').innerHTML = "&nbsp;"+lt;
}

function __setCookie(key,value,days){
	if (days){
		var d = new Date();
		d.setTime(d.getTime() + (days*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
	}else{
		var expires = "";
	}
    document.cookie = key + "=" + value + "; " + expires;
}

function __getCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function __removeCookie(name) {
	__setCookie(name,"",-1);
}