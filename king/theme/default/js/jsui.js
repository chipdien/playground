$(document).ready(function(){
  if(window.location.hash != "") { 
    $('.nav-tabs a[href="' + window.location.hash + '"]').click();
    $(window).scrollTop(0)
    return false;
  }
});
/*
* 	
* This function makes all groups checkbox checked/unchecked
*
*/
$(document).ready(function () {
	$("#checkbox-all").click(function () {
		$('#groupsDatabale tbody input[type="checkbox"],#datatable tbody input[type="checkbox"]').prop('checked', this.checked);
	});

		
  $( ".settings #fbapp_secret" ).keyup(function() {
    if($.trim($(this).val())){
      $(".settings #fbapp_auth_Link").prop('disabled', true); 
    }else{
      $(".settings #fbapp_auth_Link").prop('disabled', false); 
    }
  });

  $( ".settings #fbapp_auth_Link" ).keyup(function() {
    if($.trim($(this).val())){
      $(".settings #fbapp_secret").prop('disabled', true); 
    }else{
      $(".settings #fbapp_secret").prop('disabled', false); 
    }
  });

});
/*
* Display alert
*
*/
function alertBox(message,type,errorHolder,showIcon,close){
	var icons = {};	
	icons['success'] = 'ok';
	icons['info'] = 'info';
	icons['warning'] = 'warning';
	icons['danger'] = 'exclamation';
				
	var html = "<div class='alert alert-"+type+"' role='alert'>";
	if(close) html += "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
	if(showIcon) html += "<span class='glyphicon glyphicon-"+icons[type]+"-sign' aria-hidden='true'></span>&nbsp;";
			html += message+"</div>";

	$( document ).ready(function() {
		if(errorHolder){
			$(errorHolder).hide();
			$(errorHolder).html(html);
			$(errorHolder).fadeIn(300);
		}else{
			$(".alerts").hide();
			$(".alerts").html(html);
			$(".alerts").fadeIn(300);
		}
	});
}

/* 
* Close erro panel
*/
$(".errorsPanelClose").click(function(){
	this.hide();
});

function updatemsgdismiss(){
  document.cookie = 'kp_update_msg' +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function replaceEmoticons(text) {
  var emoticons = {
    '👍' : '_2e7',
    '👎' : '_2e8',
    '😝' : '_2g7',
    '😜' : '_2g6',
    '😃' : '_2fu',
    '😉' : '_2fx',
    '😂' : '_2ft',
    '😨' : '_2gf',
    '😭' : '_2gj',
    '😓' : '_2g1',
    '😡' : '_2ga',
    '😠' : '_2g9',
    '👽' : '_2es',
    '😘' : '_2g4',
    '🌹' : '_2c9',
    '💋' : '_2ez',
    '💰' : '_4_q-',
    '💲' : '_4_q_',
    '💵' : '_4_r1',
    '❤' : '_2hc',
    '💔' : '_2f2',
    '💓' : '_2f1',
    '😍' : '_2f-',
    '🌟' : '_2c3',
    '✈' : '_4_u2',
    '⚡' : '_2h2',
    '🎵' : '_2cy',
    '⛔' : '_4_tb',
    '➡' : '_4_tk',
    '⬅' : '_4_tm',
    '⚽' : '_4_t9',
    '👀' : '_2dx',
    '☎' : '_4_t4',
    '✉' : '_4_u3',
    "(y)"   : '_2e7',
    ":)"   : '_2fy',
    "😊"   : '_2fy',
    "🎁"   : '_2cn',
  },
  patterns = [],
  metachars = /[[\]{}()*+?.\\|^$\-,&#\s]/g;

  // build a regex pattern for each defined property
  for (var i in emoticons) {
    if (emoticons.hasOwnProperty(i)){ // escape metacharacters
      patterns.push('('+i.replace(metachars, "\\$&")+')');
    }
  }

  // build the regular expression and replace
  return text.replace(new RegExp(patterns.join('|'),'g'), function (match) {
    return typeof emoticons[match] != 'undefined' ?
           ' <span class="emoji '+emoticons[match]+' _1a-"></span>' :
           match;
  });
}
 
$( document ).ready(function() {
	$('#emoticons a').click(function (event) {
		event.preventDefault();
	   var smiley = " "+$(this).attr('title');
	   ins2pos(smiley, 'message');
	   $( "#message" ).change();
	});
});

function ins2pos(str, id) {
   var TextArea = document.getElementById(id);
   var val = TextArea.value;
   var before = val.substring(0, TextArea.selectionStart);
   var after = val.substring(TextArea.selectionEnd, val.length);
   
   TextArea.value = before + str + after;
   setCursor(TextArea, before.length + str.length);
   
}

function setCursor(elem, pos) {
   if (elem.setSelectionRange) {
      elem.focus();
      elem.setSelectionRange(pos, pos);
   } else if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
   }
}