$(function() {
	
});

var register;
var btn_binary;
var primaryLink;

function changeBinaryLink(userid){
	
	var userlink = '&user_id=';
	
	userlink += userid;
	
	btn_binary.setAttribute('href', primaryLink + userlink);
}

jQuery(document).ready(function(){
	
	register = document.getElementById('jform_user_id_id');
	btn_binary = document.getElementById('btn-binary');
	primaryLink = btn_binary.getAttribute('href')
	
	var userId = register.value;
	changeBinaryLink(userId);
});


