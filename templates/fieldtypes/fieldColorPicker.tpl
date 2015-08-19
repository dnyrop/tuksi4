<script type="text/javascript" src="{$options.js_folder}/fieldColorPicker.js"></script>
<div class="colorPicker">
	<input type="text" value="{$options.value}" maxlength="7" class="colorPicker text" name="{$options.htmltagname}" size="10">
	<img class="colorPicker" src="{$options.img_folder}/select_arrow.gif" onmouseover="this.src='{$options.img_folder}/select_arrow_over.gif'" onmouseout="this.src='{$options.img_folder}/select_arrow.gif'" onclick="showColorPicker(this, document.tuksiForm.{$options.htmltagname})">
</div>
