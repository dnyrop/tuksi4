<script type="text/javascript">

var currentListId = '{$listInfo.id}';

var arrRecList = [];
{foreach from=$recip item=rec}
arrRecList[{$rec.id}] = {ldelim}name:'{$rec.name}',rec:{$rec.rec}{rdelim};
{/foreach}

{literal}

function sendSingleNewsletterDialog(title){
	
	var ok = true;
	
	var newsletter_title = $F('newsletter_title');
	var newsletterid = $F('newsletter_choose');
	var newsletter_from = $F('newsletter_from');

	error = ''
	if(!newsletter_title) {
		ok = false;
		error = error + '{/literal}{cmstext value="error_newsletter_title"}{literal}<br>';
	}
	if(!newsletterid) {
		ok = false;
		error = '{/literal}{cmstext value="error_newsletter"}{literal}<br>';
	}
	if(!newsletter_from) {
		ok = false;
		error = error + '{/literal}{cmstext value="error_newsletter_from"}{literal}<br>';
	}
	
	if(ok) {
	
		url = '/services/ajax/sendnewsletterdialog.php?id=_sendsinglenewstterdialog';
			
		tuksi.window.popup({
			title:title,
			ajax: true,
			url: url,
			placement:'center',
			options:{
				width:350,
				id:'sendsinglenewsletter'
			}
		});
	} else {
		tuksi.window.alert(error);
	}
}

function sendSingleNewsletter(){
	
	var toemail = $F('sendsinglenewslettertoemail');
	
	if(!toemail) {
		$('sendsinglenewslettertoemail').style.border = 'red 1px solid';
	} else {
		var newsletterid = $F('newsletter_choose');
		var newsletter_from = $F('newsletter_from');
		var newsletter_title = $F('newsletter_title');
		
		var arrVal = $H({
			newsletterid:newsletterid,
			newsletter_title:newsletter_title,
			fromemail:newsletter_from,
			toemail: toemail
		});
		$('json').value = Object.toJSON(arrVal);
		doAction('SENDSINGLE');
	} 
}

{/literal}
</script>
<div class="clr"><!--clearfloat--></div>
