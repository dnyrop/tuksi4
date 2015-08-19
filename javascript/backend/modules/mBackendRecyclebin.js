var __deleteFromThrashDialogText = "";
var __moveFromThrashDialogText = "";
var __deleteFromThrashDialogTreeid = 0;
var __moveFromThrashDialogTreeid = 0;

function moveFromThrashDialog(treeid){
	__moveFromThrashDialogTreeid = treeid;
	tuksi.window.confirm(__moveFromThrashDialogText,{callback:moveFromThrash});
}

function moveFromThrash(){
	$('moveFromThrash').value = __moveFromThrashDialogTreeid;
	doAction('SAVE');
}

function deleteFromThrashDialog(treeid){
	__deleteFromThrashDialogTreeid = treeid;
	tuksi.window.confirm(__deleteFromThrashDialogText,{callback:deleteFromThrash});
}

function deleteFromThrash(){
	$('deleteFromThrash').value = __deleteFromThrashDialogTreeid;
	doAction('SAVE');
}