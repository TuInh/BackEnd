$(function() {
	
});

function jSelectUser_jform_memberlist(id, name , username) {

    if(memberArray.indexOf(id) == -1){
        generateRow(id, name , username);
    }
	
	SqueezeBox.close();
};

function generateRow(id, name , username) {
	memberArray.push(""+id);
	var row = document.createElement("tr");
	row.setAttribute("data-member-id",id);
	
	var dataHtml =  '';//'<tr data-member-id="'+id+'">';
        dataHtml += 	'<input type="hidden" name="'+formName+'[id][]" value="null" />';
        dataHtml += 	'<input type="hidden" name="'+formName+'[task][]" value="create" />';
        dataHtml += 	'<td>'+ id +'</td><input type="hidden" name="'+formName+'[user_id][]" value="'+id+'" /></td>';
        dataHtml += 	'<td>'+ name +'</td>';
        dataHtml += 	'<td>'+ username +'</td>';
        dataHtml += 	'<td><a onclick="deleteConfirmation(this)" data-toggle="modal" href="#delete_confirm_modal" id="member-id-'+id+'" class="btn btn-danger"> <i class="icon-trash"></i></a></td>';
        //dataHtml += 	'</tr>';
        row.innerHTML = dataHtml;
	tbody.appendChild(row);
        
       
}

var tbl;
var tbody;
var deleteMemberId;
var deleteMemberRow;
var memberArray = [];

function removeEditable(element) { 
	var price = tbody.getElementsByClassName('current');
	price[0].innerHTML = element.value;
	price[0].removeClass('current'); 		
}

function makeEditable(element) { 
	console.log(element);
	element.innerHTML = '<input onblur="removeEditable(this)" id="editbox" size="255" type="text" value="'+ element.textContent +'">';  
	
	tbody.getElementById('editbox').focus();
	element.setAttribute('class', 'edit current'); 
}

function deleteConfirmation(element) {
	var parent = element.parentNode;
	deleteMemberRow = parent.parentNode;
	deleteMemberId = deleteMemberRow.dataset.memberId;

}

function deleteMember(element) {	
	if(deleteMemberRow) {
        memberArray.erase(deleteMemberId);
        
        var input = deleteMemberRow.getElementsByTagName('input');
		var removeQuantities = deleteMemberRow.getElementsByClassName('ajax-member-quantity');
		for(var q in removeQuantities) {
			removeQuantities[q].value = 0;
		}
        
        var x = 0,check = 0;
        for (x in input) {
        	
        	console.log("name "+input[x].name);
        	console.log("class "+input[x].className);
        	if(input[x].name == formName+'[task][]' && input[x].className == 'ajax-detail-id'){
        		check = 1;
        		break;
        	}
        }
        
        if(check){
        	input[x].value = 'delete';
        	deleteMemberRow.style.display = "none";
        } else {
        	tbody.removeChild(deleteMemberRow);
        }
        deleteMemberRow = null;
		CostManager('jform_price','jform_final_price','ajax-member-price','ajax-member-quantity');
	}
}


jQuery(document).ready(function(){
	

	var producListLabel = [document.id('jform_memberList-lbl')];
	producListLabel.each(function(a){
		   //a.style.display = "none";
	});
	
	tbl = document.id('table-member-list');
	tbody = tbl.getElementsByTagName('tbody')[0];

});


