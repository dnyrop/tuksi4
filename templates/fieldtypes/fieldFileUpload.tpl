<input type="hidden" value="{$value}" name="{$tagname}_OLD">
{if $ftp == 0}
{cmstext value="browse_computer"}: <input class="tablesinputfile200" id="{$tagname}_browse" type="file" name="{$tagname}"><br />

{/if}

{if $ftp == 1}
<br />Hent fra FTP upload:
<select class="forminput200" name="{$tagname}_ftp">
	<option value=\"\">-- {cmstext value="choosefile"} --</option>\n
	{foreach from=$ftp_file item=file}
	<option value="{$file}">{$file}</option>
{/foreach}
</select>
{/if}
{if $error}
<p style="color: #7F1301;">{$error}</p>
{/if}

{if $media_type} 
<br>
{if $media_type == 1}
 <input name="{$tagname}_media_save" type="checkbox">{cmstext value="savemedialib"}<br>
{/if}
<br>
<select class="forminput200" id="{$tagname}_media" name="{$tagname}_media">
	<option value="">{cmstext value="choose_media_file"}</option>\n
	{foreach from=$media item=file}
	<option {if $file.selected}selected="selected"{/if} value="{$file.file}">{$file.filename}</option>
{/foreach}
</select>

{/if}

{if $value_old && !$editor_mode}
<br>
<br>
	<input type="hidden" id="{$tagname}_fileDelete" name="{$tagname}_DELETE">
	<input type="hidden" id="{$tagname}_fileReset" name="{$tagname}_RESET">
	
	<a href="#" onclick="if(confirm('{$slet_msg}')){ldelim}document.getElementById('{if $media_selected}{$tagname}_fileReset{else}{$tagname}_fileDelete{/if}').value = 1;saveData();{rdelim}; return false;" class="buttonType3 itemDelete"><span><span>{cmstext value=delete}</span></span></a>
	
	<a href="{$path.upload}/{$value}" target="_blank" style="margin:2px 0 5px; padding-left: 10px; display:block;">{cmstext value=showfile}</a><br />
	<img {$size} src="{$image_path}" style="float:left; margin-right: 10px;" />
	
{/if}
