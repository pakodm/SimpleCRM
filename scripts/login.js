function _$(elmId){return document.getElementById(elmId);}
function getKeyPressed(e){if (window.event){return e.keyCode;}else{return e.which;}}
function enterSubmit(ev){if (getKeyPressed(ev) == 13){doLogin();}}

function userStatus(tb,clean){
	if (clean){
		if (tb.value == "Username"){
			tb.value = "";
			tb.style.color = "#000000";
		}
	}else{
		if (tb.value == ""){
			tb.value = "Username";
			tb.style.color = "#999999";
		}
	}
}

function passStatus(tb,clean){
	if (clean){
		if (tb.type == 'text'){
			tb.value = "";
			tb.style.color = "#000000";
			tb.type = 'password';
		}
	}else{
		if (tb.value == ""){
			tb.value = "Password";
			tb.style.color = "#999999";
			tb.type = 'text'
		}
	}
}

function doLogin(){
	dat1 = _$("tb_usr").value;
	dat2 = _$("tb_pwd").value;
	if ((dat1.length > 0) && (dat2.length > 0) && (dat1 != "Username") && (dat2 != "Password")){
		document.forms[0].submit();
	}else{
		_$("result").innerHTML = "Input your username and/or password please!";
	}
}

