function saveNewProspect(id){
	if (inProgress) return;
	if (AlertOn) return;
	_$('td_errormsg').innerHTML = "&nbsp;";
	d1 = trim12(_$('tb_dat1').value); d2 = trim12(_$('tb_dat2').value); d3 = trim12(_$('tb_dat3').value);
	d4 = trim12(_$('tb_dat4').value); d5 = trim12(_$('tb_dat5').value); d6 = trim12(_$('cb_dat6').value);
	d7 = trim12(_$('cb_dat7').value); d8 = trim12(_$('cb_dat8').value); d9 = trim12(_$('cb_dat9').value);
	d10 = trim12(_$('tb_date1').value); d11 = (_$('cb_dat10')) ? trim12(_$('cb_dat10').value) : 0;
	d12 = trim12(_$('cb_dat11').value); d13 = trim12(_$('cb_dat12').value); d14 = trim12(_$('tb_dat13').value);
	if (isEmpty(d1)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Name"; return;}
	if (isEmpty(d2)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Last Name"; return;}
	if (isEmpty(d3)){_$('td_errormsg').innerHTML = "Missing Data. Please input the E-Mail"; return;}
	if (isEmpty(d4)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Phone Number"; return;}
	if (d7 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose the lead source or referrer"; return;}
	if (d8 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose current step"; return;}
	if (d9 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose the next step"; return;}
	if (d11 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please select an user to assign"; return;}
	if (isEmpty(d10)){_$('td_errormsg').innerHTML = "Missing Data. Please input a follow up date"; return;}
	if (!checkRegData(d1)){_$('td_errormsg').innerHTML = "Data Error. Name contains invalid characters"; return;}
	if (!checkRegData(d2)){_$('td_errormsg').innerHTML = "Data Error. Last Name contains invalid characters"; return;}
	if (!checkMail(d3)){_$('td_errormsg').innerHTML = "Data Error. E-Mail format does not seem valid"; return;}
	if ((!validatel(d4)) || (d4.length != 10)){_$('td_errormsg').innerHTML = "Data Error. Phone Number must be 10 digits"; return;}
	if (!isEmpty(d5)){
		if ((!validatel(d5)) || (d5.length != 10)){_$('td_errormsg').innerHTML = "Data Error. Mobile Number must be 10 digits"; return;}
	}else{
		d5 = '4490000000';
	}
	if (isEmpty(d12)){d12 = "NV";}
	if (isEmpty(d14)){d14 = "Initial Record";}else{d14 = d14.sanitize();}
	__shProgress(true,"Saving...");
	__initRequest();
	post = [];
	post.push(d1); post.push(d2); post.push(d3); post.push(d4); post.push(d5); post.push(d6); post.push(d7); 
	post.push(d8); post.push(d9); post.push(d10); post.push(d11); post.push(d12); post.push(d13); post.push(d14);
	data = "UID="+id+"&DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=2',data,_saveNewProspect);
}

function _saveNewProspect(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		closeWorkspace();
		showAlert("The new prospect has been saved.","Information");
	}
}

function getFollowUpData(a,b){
	if (inProgress) return;
	if (AlertOn) return;
	__shProgress(true,"Retrieving...");
	__initRequest();
	data = "D1="+a+"&D2="+b;
	reqTarget = "subWSArea";
	req.doPost(ajx+'?jxid=9',data,_getFollowUpData);
}

function _getFollowUpData(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		_$(reqTarget).innerHTML = res;
		initCalendar(false);
	}
}

function saveFollowup(a,b){
	if (inProgress) return;
	if (AlertOn) return;
	_$('td_errormsg').innerHTML = "&nbsp;";
	d1 = trim12(_$('cb_dat1').value); d2 = trim12(_$('tb_date1').value); d3 = trim12(_$('tb_dat2').value);
	d4 = (_$('cb_dat2')) ? trim12(_$('cb_dat2').value) : 0;
	d5 = (_$('cb_dat3')) ? trim12(_$('cb_dat3').value) : 0;
	d6 = 0;
	if (_$('rb_yes')){
		if (_$('rb_yes').checked){d6 = 1;}
		if (_$('rb_no').checked){d6 = 2;}
	}
	if (d1 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose the next step"; return;}
	if (isEmpty(d2)){_$('td_errormsg').innerHTML = "Missing Data. Please input a follow up date"; return;}
	if (isEmpty(d3)){_$('td_errormsg').innerHTML = "Missing Data. Please input any comment"; return;}
	//if (!checkRegData(d3)){_$('td_errormsg').innerHTML = "Data Error. Comments contains invalid characters"; return;}
	d3 = d3.sanitize();
	_$('tb_dat2').value = d3;
	if (d4 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please select an user to assign"; return;}
	if (d5 == 'NV'){d5 = 0;}
	if ((_$('rb_yes')) && (d6 == 0)){_$('td_errormsg').innerHTML = "Missing Data. Please specify if the prospect took the exam"; return;}
	__shProgress(true,"Saving...");
	__initRequest();
	post = [];
	post.push(a); post.push(b); post.push(d1); post.push(d2); post.push(d3.sanitize()); post.push(d4); post.push(d5); post.push(d6);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=10',data,_saveFollowup);
}

function _saveFollowup(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		showAlert("Data saved successfully.","Information");
		getWorkspace(1);
	}
}

function saveMyProfile(v){
	if (inProgress) return;
	if (AlertOn) return;
	_$('td_errormsg').innerHTML = "&nbsp;";
	d1 = trim12(_$('tb_dat1').value); d2 = trim12(_$('tb_dat2').value);
	if (isEmpty(d1)){_$('td_errormsg').innerHTML = "Missing Data. Please input the New Password"; return;}
	if (isEmpty(d2)){_$('td_errormsg').innerHTML = "Missing Data. Please input New Password Confirmation"; return;}
	if (d2 != d1){_$('td_errormsg').innerHTML = "Data Error. Typed passwords do not match"; return;}
	__shProgress(true,"Saving...");
	__initRequest();
	post = [];
	post.push(d1); post.push(d2); post.push(v);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=14',data,_saveMyProfile);
}

function _saveMyProfile(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		showAlert("Password changed successfully.","Information");
		getSubWorkspace(301);
	}
}

function saveNewUser(id,v){
	if (inProgress) return;
	if (AlertOn) return;
	_$('td_errormsg').innerHTML = "&nbsp;";
	d1 = trim12(_$('tb_dat1').value); d2 = trim12(_$('tb_dat2').value); d3 = trim12(_$('tb_dat3').value);
	d4 = _$('cb_dat4').value;
	if (isEmpty(d1)){_$('td_errormsg').innerHTML = "Missing Data. Please input the User Name"; return;}
	if ((isEmpty(d2)) && (v == 0)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Password"; return;}
	if ((isEmpty(d3)) && (v == 0)){_$('td_errormsg').innerHTML = "Missing Data. Please input Password Confirmation"; return;}
	if (d4 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose the user type"; return;}
	if (!checkUserName(d1)){_$('td_errormsg').innerHTML = "Data Error. User name contains spaces or invalid characters [A..Z, 0-9]"; return;}
	if (d2 != d3){_$('td_errormsg').innerHTML = "Data Error. Typed passwords do not match"; return;}
	__shProgress(true,"Saving...");
	__initRequest();
	post = [];
	post.push(d1); post.push(d2); post.push(d4); post.push(v);
	data = "UID="+id+"&DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=3',data,_saveNewUser);
}

function _saveNewUser(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		showAlert("User saved successfully.","Information");
		getSubWorkspace(302);
	}
}

function editUser(idu){
	if (inProgress) return;
	if (AlertOn) return;
	__shProgress(true,"Retrieving...");
	__initRequest();
	reqTarget = "crmEditUser";
	data = "DAT="+idu;
	req.doPost(ajx+'?jxid=13',data,__showResponse);
}

function toggleVisible(idc, idr, v){
	if (inProgress) return;
	if (AlertOn) return;
	__shProgress(true,"Updating...");
	__initRequest();
	reqTarget = "crmDivCatalog";
	post = [];
	post.push(idc); post.push(idr); post.push(v);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=5',data,__showResponse);
}

function editCatalog(idc, idr){
	if (inProgress) return;
	if (AlertOn) return;
	__shProgress(true,"Retrieving...");
	__initRequest();
	reqTarget = "crmEditCatalog";
	post = [];
	post.push(idc); post.push(idr);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=11',data,__showResponse);
}

function deleteCatalog(idc, idr){
	if (inProgress) return;
	if (AlertOn) return;
	if (confirm("Before deleting this entry, make sure it's not currently assigned or in use, since unexpected behaviour may happen.\nAn alternative is to make the entry Invisible. Do you want to proceed with the removal?")){
		__shProgress(true,"Deleting...");
		__initRequest();
		reqTarget = "crmDivCatalog";
		post = [];
		post.push(idc); post.push(idr);
		data = "DAT="+JSON.stringify(post);
		req.doPost(ajx+'?jxid=12',data,__showResponse);
	}
}

function saveCatalog(idc, idr, idv){
	if (inProgress) return;
	if (AlertOn) return;
	_$('td_errormsg').innerHTML = "&nbsp;";
	d1 = trim12(_$('tb_dat1').value);
	d2 = ((idc == 2) || (idc == 4) || (idc == 7)) ? trim12(_$('tb_dat2').value) : "NV";
	if (idc == 4){
		if (isEmpty(d1)){_$('td_errormsg').innerHTML = "Missing Data. Please input teacher's First Name"; return;}
		if (!checkRegData(d1)){_$('td_errormsg').innerHTML = "Data Error. First name contains invalid characters"; return;}
	}else if (idc == 7){
		if (isEmpty(d1)){_$('td_errormsg').innerHTML = "Missing Data. Please input Company's or Institution's Name"; return;}
		if (!checkRegData(d1)){_$('td_errormsg').innerHTML = "Data Error. Company name contains invalid characters"; return;}
	}else{
		if (isEmpty(d1)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Description"; return;}
		if (!checkRegData(d1)){_$('td_errormsg').innerHTML = "Data Error. Description contains invalid characters"; return;}
	}
	if (idc == 2){
		if (isEmpty(d2)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Success Ratio"; return;}
		if (!checkNumData(d2)){_$('td_errormsg').innerHTML = "Data Error. Success Ratio should be numbers only"; return;}
		if (parseFloat(d2) > 100){_$('td_errormsg').innerHTML = "Data Error. Success Ratio can't be greater than 100"; return;}
	}
	if (idc == 4){
		if (isEmpty(d2)){_$('td_errormsg').innerHTML = "Missing Data. Please input teacher's Last Name"; return;}
		if (!checkRegData(d2)){_$('td_errormsg').innerHTML = "Data Error. Last name contains invalid characters"; return;}
	}
	if (idc == 7){
		if (isEmpty(d2)){_$('td_errormsg').innerHTML = "Missing Data. Please input agreement's description"; return;}
		if (!checkRegData(d2)){_$('td_errormsg').innerHTML = "Data Error. Agreement contains invalid characters"; return;}
	}
	__shProgress(true,"Updating...");
	__initRequest();
	reqTarget = "crmDivCatalog";
	post = [];
	post.push(idc); post.push(idr); post.push(d1); post.push(d2); post.push(idv);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=4',data,_saveCatalog);
}

function _saveCatalog(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		_$(reqTarget).innerHTML = res;
		_$('tb_dat1').value = "";
		if (_$('tb_dat2') != null){_$('tb_dat2').value = "";}
	}
}

function filterFUPByName(e){
	if (e.value){
		s = e.value;
		e.value = s.toUpperCase();
	}
	filterPropetsFBy();
}

function filterPropetsFBy(){
	x = _$("cb_filt1").value;
	filterCByUser(x);
}

function filterCByUser(v){
	if (inProgress) return;
	if (AlertOn) return;
	__shProgress(true,"Updating...");
	reqTarget = "divCandidates";
	d1 = (v == 'NV') ? 0 : v;
	d2 = (_$('cb_filt2').checked) ? 1 : 0;
	d3 = trim12(_$('tb_filt3').value);
	d4 = (_$("cb_filt4").value == 'NV') ? 0 : _$("cb_filt4").value;
	d5 = (_$("cb_filt5").value == 'NV') ? 0 : _$("cb_filt5").value; 
	__setCookie("fupsel",d1+","+d2,1);
	post = [];
	post.push(d1); post.push(d2); post.push(d3); post.push(d4); post.push(d5);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=15',data,__showResponse);
}

function filterSchedule(){
	if (inProgress) return;
	if (AlertOn) return;
	f1 = _$('cb_filt1').value; f2 = _$('cb_filt2').value; f3 = _$('cb_filt3').value; f4 = _$('cb_filt4').value;
	__shProgress(true,"Updating...");
	__initRequest();
	reqTarget = "crmDivCatalog";
	post = [];
	post.push(f1); post.push(f2); post.push(f3); post.push(f4);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=7',data,__showResponse);
}

function saveSchedule(){
	if (inProgress) return;
	if (AlertOn) return;
	_$('td_errormsg').innerHTML = "&nbsp;";
	d1 = _$('cb_dat1').value; d2 = _$('cb_dat2').value; d3 = _$('cb_dat3').value; d4 = _$('cb_dat4').value;
	d5 = _$('cb_st1').value; d6 = _$('cb_st2').value; d7 = _$('cb_et1').value; d8 = _$('cb_et2').value;
	if (d1 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose a day"; return;}
	if (d2 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose a level"; return;}
	if (d3 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose a classroom"; return;}
	if (d4 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose a teacher"; return;}
	if (d5.length == 1){d5 = "0"+d5;} if (d6.length == 1){d6 = "0"+d6;}
	if (d7.length == 1){d7 = "0"+d7;} if (d8.length == 1){d8 = "0"+d8;}
	st = d5+""+d6; et = d7+""+d8;
	if (parseInt(st) >= parseInt(et)){
		_$('td_errormsg').innerHTML = "Data Error. End Time must be after Start Time"; return;
	}
	__shProgress(true,"Looking for conflicts...");
	__initRequest();
	reqTarget = "crmDivCatalog";
	post = [];
	post.push(d1); post.push(d2); post.push(d3); post.push(d4); post.push(d5); 
	post.push(d6); post.push(d7); post.push(d8);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=6',data,_saveSchedule);
}

function _saveSchedule(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		data = eval(res);
		if (data[0] == 1){
			//getSubWorkspace(309);
			filterSchedule();
		}else{
			_$('td_errormsg').innerHTML = data[1];
		}
	}
}

function toggleSchedule(idr, v){
	if (inProgress) return;
	if (AlertOn) return;
	__shProgress(true,"Updating...");
	__initRequest();
	reqTarget = "crmDivCatalog";
	post = [];
	post.push(idr); post.push(v);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=8',data,_toggleSchedule);
}

function _toggleSchedule(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		filterSchedule();
	}
}

function showClassLevel(v,o){
	tr = _$('trlvlclass');
	opts = o.split(/\|/);
	tr.style.visibility = 'collapse';
	if (opts.isInArray(v)){
		tr.style.visibility = 'visible';
	}
}

function setDemoClassDetails(idr,o,n){
	_$('sp_pname').innerHTML = n;
	opts = o.split(/\|/);
	selectByValue('cb_dat1',opts[0]);
	selectByValue('cb_dat2',opts[1]);
	selectByValue('cb_dat3',opts[2]);
	selectByValue('cb_dat4',"0");
	_$('ih_crecord').value = idr;
}

function filterDemoClass(){
	if (inProgress) return;
	if (AlertOn) return;
	f1 = _$('cb_filt1').value; f2 = _$('cb_filt2').value; f3 = _$('cb_filt3').value; 
	f4 = (_$('cb_filt4').checked) ? 1 : 0;
	__shProgress(true,"Updating...");
	__initRequest();
	reqTarget = "crmDivCatalog";
	post = [];
	post.push(f1); post.push(f2); post.push(f3); post.push(f4);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=18',data,__showResponse);
}

function updateDemoClass(){
	if (inProgress) return;
	if (AlertOn) return;
	idr = trim12(_$('ih_crecord').value);
	_$('td_errormsg').innerHTML = "&nbsp;";
	if ((!isNaN(parseInt(idr))) && (parseInt(idr) > 0)){	
		d1 = _$('cb_dat1').value; d2 = _$('cb_dat2').value; d3 = _$('cb_dat3').value; d4 = _$('cb_dat4').value;
		if ((d1 == "NV") && (d2 == "NV") && (d3 == "NV") && (d4 == "NV")){
			_$("td_errormsg").innerHTML = "Please select at least one value to update";
			return;
		}
		reqTarget = "crmDivCatalog";
		post = [];
		post.push(d1); post.push(d2); post.push(d3); post.push(d4); post.push(idr);
		data = "DAT="+JSON.stringify(post);
		req.doPost(ajx+'?jxid=19',data,_updateDemoClass);
	}else{
		_$("td_errormsg").innerHTML = "Please select a prospect from the above list";
	}
}

function _updateDemoClass(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		data = eval(res);
		if (data[0] == 1){
			filterDemoClass();
			_$('sp_pname').innerHTML = "Select one from the above list";
			selectByValue('cb_dat1',"0");
			selectByValue('cb_dat2',"0");
			selectByValue('cb_dat3',"0");
			selectByValue('cb_dat4',"0");
			_$('ih_crecord').value = "0";
		}else{
			_$('td_errormsg').innerHTML = data[1];
		}
	}
}

function editProspect(id){
	if (inProgress) return;
	if (AlertOn) return;
	_$('ih_helper').value = id;
	getSubWorkspace(551);
}

function updateProspect(a,b){
	if (inProgress) return;
	if (AlertOn) return;
	_$('td_errormsg').innerHTML = "&nbsp;";
	d1 = trim12(_$('tb_dat1').value); d2 = trim12(_$('tb_dat2').value); d3 = trim12(_$('tb_dat3').value);
	d4 = trim12(_$('tb_dat4').value); d5 = trim12(_$('tb_dat5').value); d6 = trim12(_$('cb_dat6').value);
	d7 = trim12(_$('cb_dat7').value); d8 = trim12(_$('cb_dat11').value); d9 = trim12(_$('cb_dat12').value); 
	d11 = (_$('cb_dat10')) ? trim12(_$('cb_dat10').value) : 0;
	
	if (isEmpty(d1)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Name"; return;}
	if (isEmpty(d2)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Last Name"; return;}
	if (isEmpty(d3)){_$('td_errormsg').innerHTML = "Missing Data. Please input the E-Mail"; return;}
	if (isEmpty(d4)){_$('td_errormsg').innerHTML = "Missing Data. Please input the Phone Number"; return;}
	if (d7 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please choose the lead source or referrer"; return;}
	if (d11 == 'NV'){_$('td_errormsg').innerHTML = "Missing Data. Please select an user to assign"; return;}
	if (!checkRegData(d1)){_$('td_errormsg').innerHTML = "Data Error. Name contains invalid characters"; return;}
	if (!checkRegData(d2)){_$('td_errormsg').innerHTML = "Data Error. Last Name contains invalid characters"; return;}
	if (!checkMail(d3)){_$('td_errormsg').innerHTML = "Data Error. E-Mail format does not seem valid"; return;}
	if ((!validatel(d4)) || (d4.length != 10)){_$('td_errormsg').innerHTML = "Data Error. Phone Number must be 10 digits"; return;}
	if (!isEmpty(d5)){
		if ((!validatel(d5)) || (d5.length != 10)){_$('td_errormsg').innerHTML = "Data Error. Mobile Number must be 10 digits"; return;}
	}else{
		d5 = '4490000000';
	}
	if (isEmpty(d8)){d8 = "NV";}
	__shProgress(true,"Saving...");
	__initRequest();
	post = [];
	post.push(d1); post.push(d2); post.push(d3); post.push(d4); post.push(d5); post.push(d6); 
	post.push(d7); post.push(d8); post.push(d11); post.push(d9); post.push(b);
	data = "UID="+a+"&DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=17',data,_updateProspect);
}

function _updateProspect(res){
	__shProgress(false,"");
	if (res.indexOf('0x') != -1){
		__handleErrors(res);
	}else{
		getSubWorkspace(501);
		showAlert("Prospect updated successfully","Information");
	}
}

function deleteProspect(id){
	if (inProgress) return;
	if (AlertOn) return;
	if (confirm("This action will completely remove the prospect from the system and cannot be undone. Do you want to proceed with the removal?")){
		__shProgress(true,"Deleting...");
		__initRequest();
		reqTarget = "subWSArea";
		post = [];
		post.push(id);
		data = "DAT="+JSON.stringify(post);
		req.doPost(ajx+'?jxid=16',data,__showResponse);
	}
}

function activateProspect(id){
	if (inProgress) return;
	if (AlertOn) return;
	__shProgress(true,"Deleting...");
	__initRequest();
	reqTarget = "subWSArea";
	post = [];
	post.push(id);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=20',data,__showResponse);
}

function filterByName(e,id){
	if (e.value){
		s = e.value;
		e.value = s.toUpperCase();
	}
	//if (inProgress) return;
	filterProspectsRBy(id);
}

function filterProspectsRBy(id){
	__shProgress(true,"Searching...");
	__initRequest();
	reqTarget = "AllProspektList";
	post = [];
	post.push(trim12(_$('tb_dat1').value));
	if (id == 0){
		post.push(trim12(_$('cb_status').value));
	}else{
		post.push("0");
	}
	post.push(trim12(_$('cb_level').value));
	post.push(id);
	data = "DAT="+JSON.stringify(post);
	req.doPost(ajx+'?jxid=21',data,__showResponse);
}

function filterDemoAttBy(){
	__shProgress(true,"Searching...");
	__initRequest();
	reqTarget = "DemoClassAttList";
	post = [];
	d1 = trim12(_$('tb_date1').value);
	d2 = trim12(_$('tb_date2').value);
	if (d1.length != 10){d1 = (new Date()).toISOString().slice(0,10);}
	if (d2.length != 10){d2 = (new Date()).toISOString().slice(0,10);}
	x = (getTimeBetweenDates(d1+" 00:00:00", d2+" 23:59:59")/60);
	if (x > 0){
		post.push(d1); post.push(d2);
		data = "DAT="+JSON.stringify(post);
		req.doPost(ajx+'?jxid=22',data,__showResponse);
	}else{
		showAlert("Supplied from and to dates are not valid","Error");
	}
}

function getCommentsHistory(a,b){
	if (inProgress) return;
	if (AlertOn) return;
	_$('ih_helper').value = a;
	__setCookie("psel",a+","+b,1);
	getWorkspace(101);
}

function getProspectProgress(a){
	if (inProgress) return;
	if (AlertOn) return;
	_$('ih_helper').value = a;
	getSubWorkspace(552);
}

function exportToXLS(id){
	post = "";
	if (parseInt(id) == 1){
		post += (trim12(_$('tb_dat1').value)) + "|";
		post += (trim12(_$('cb_status').value)) + "|";
		post += (trim12(_$('cb_level').value)) + "|";
	}
	if (parseInt(id) == 2){
		post += (trim12(_$('tb_date1').value)) + "|";
		post += (trim12(_$('tb_date2').value)) + "|";
	}
	post += id + "|";
	xlsWin = openWindow("RXLS","XLSReport.php?XLSID="+id+"&PARMS="+btoa(post));
}