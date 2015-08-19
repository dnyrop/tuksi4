<!-- Start: mail_emailadmin -->
<table>
<tr>
	<td><label for="searchstr">{cmstext value="freetext"}:</label></td>
	<td>
		<input name="searchstr_old" id="searchstr_old" type="hidden" value="{$searchstr}">
		<table>
		<tr>
			<td><input type="text" name="searchstr" id="searchstr" value="{$searchstr}" class="forminput400 text"></td>
			<td><a href="#" onclick="doAction('SEARCH'); return false;" class="buttonType3 itemAdd"><span><span>{cmstext value="search"}</span></span></a></td>	
		</tr>
		</table>
	</td>
</tr>
</table>

<input name="pagenr_old" type="hidden" value="{$pagenr}">
<input name="pagenr" type="hidden" value="{$pagenr}">
<input name="gotopage" type="hidden" value="">

<table>
<tr>	
	<td>{$pagestatus}</td>
	<td>
		<table>
		<tr>
			<td>{$fastleftnav}</td>
			<td>{$leftnav}</td>
			<td>{$pagenav}</td>
			<td>{$rightnav}</td>
			<td>{$fastrightnav}</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<table class="emailAdrList">
<tr>
	<th>{cmstext value="name"}</th>
	<th>{cmstext value="email"}</th>
	<th>{cmstext value="unsubscribe"}</th>
	<th>{cmstext value="valid"}</th>
	<th>{cmstext value="date"}</th>
	<th>{cmstext value="delete"}</th>
</tr>
{foreach from=$emails item=email name=emailAdrList}
	<tr class="{if $smarty.foreach.emailAdrList.iteration%2} even{/if}">
		<td><input class="text" type="text" name="form_name_{$email.id}" value="{$email.name}" style="width: 200px;"></td>
		<td>{$email.email}</td>
		<td class="deleted"><input class="formcheckbox" value="1" type="checkbox" name="form_isdeleted_{$email.id}" {$email.isdeleted}></td>
		<td class="deleted"><input class="formcheckbox" value="1" type="checkbox" name="form_isvalidated_{$email.id}" {$email.isvalidated}></td>
		<td class="date">{$email.dato|date_format:"%d-%m-%Y"}</td>
		<td class="remove"><input class="formcheckbox" value="1" type="checkbox" name="form_deleteemail_{$email.id}" {$email.del}></td>
	</tr>
{/foreach}
</table>

<br>

<div class="mHeader"><h6>{cmstext value="addemailheadline"}</h6></div>
<table>
<tr>
	<td colspan="2">
	<br>
		<a href="#" onclick="doAction('ADDEMAIL'); return false;" class="buttonType3 itemAdd"><span><span>{cmstext value="addemail"}</span></span></a>
	</td>
</tr>
{if $addemail_error}
<tr>
	<td><p class="contentMain"></td>
	<td><span class="red">{$addemail_error}</span></td>
</tr>
{/if}
<tr>
	<td><p class="contentMain">{cmstext value="name"}</td>
	<td><input name="form_name" class="forminput200 text" style="width:200px" value=""></td>
</tr>
<tr>
	<td><p class="contentMain">{cmstext value="email"}</p></td>
	<td><input name="form_email" class="forminput200 text" style="width:200px" value=""></td>
</tr>
<tr>
	<td colspan="2"><br></td>
</tr>
</table>

<div class="mHeader">
	<h6>{cmstext value="addlist"}</h6>
</div>

<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr>
	<td colspan="2">
	<br>
			<a href="#" onclick="doAction('ADDEMAILLIST'); return false;" class="buttonType3 itemAdd"><span><span>{cmstext value="addemail"}</span></span></a>
		
	</td>
</tr>
{if $addemaillist_error}
<tr>
	<td><p class="contentMain"></td>
	<td><span class="red">{$addemaillist_error}</span></td>
</tr>
{/if}
<tr>
	<td><p class="contentMain">{cmstext|replace:" ":"&nbsp;" value="liste"}</p></td>
	<td style="width:100%"><input name="form_liste" class="forminput200" type="file" ><br>
	{cmstext value="addlist_text"}</td>
</tr>
</table>
<!-- End: mail_emailadmin -->
