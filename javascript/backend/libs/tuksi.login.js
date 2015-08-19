function doLogin(){
	
	var username = $F('username');
	var password = $F('password');
	
	var doSubmit = true;
	
	if(username.length == 0) {
		$('username').up().addClassName("loginError");
		doSubmit = false;
	} else {
		$('username').up().removeClassName("loginError");
	}
	
	if(password.length == 0) {
		$('password').up().addClassName("loginError");
		doSubmit = false;
	} else {
		$('password').up().removeClassName("loginError");
	}
	
	if(doSubmit) {
		$('action_login').setValue('login');
		$('login-form').submit();
	}
}


function sendPassword(){
	
	var email = $F('email');
	
	var doSubmit = true;
	
	if(email.length == 0) {
		$('email').up().addClassName("loginError");
		doSubmit = false;
	} else {
		$('email').up().removeClassName("loginError");
	}
	
	if(doSubmit) {
		$('action_lostpassword').setValue('sendpw');
		$('lostpassword-form').submit();
	}
	
}

document.observe('dom:loaded',function(){
	if($('username')) {
		$('username').focus();
		$('login-form').observe('keypress',function(e){
			var code;
			if (!e) var e = window.event;
			if (e.keyCode) code = e.keyCode;
			else if (e.which) code = e.which;
			if(code == 13) {
				doLogin();
			}
		});
	}
});
