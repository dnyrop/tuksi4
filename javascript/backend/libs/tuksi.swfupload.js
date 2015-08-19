var tuksiSwfupload = {};
tuksiSwfupload =  Base.extend({
	constructor:function(id,uploadpath){
		this.base();
		this.id = id;
		document.observe('dom:loaded',this.setUploader.bind(this,uploadpath));
	},	
	setUploader:function(uploadpath){
		
		this.uploader = new SWFUpload({
				
			// Backend Settings
				upload_url: uploadpath,
				post_params: {"id" : this.id},

				// File Upload Settings
				file_size_limit : "102400",	// 100MB
				file_types : "*.*",
				file_types_description : "All Files",
				file_upload_limit : "0",
				file_queue_limit : "1",

				// Event Handler Settings (all my handlers are in the Handler.js file)
				file_dialog_start_handler : this.fileDialogStart.bind(this),
				file_queued_handler : this.fileQueue.bind(this),
				file_queue_error_handler : this.fileQueueError.bind(this),
				file_dialog_complete_handler : this.fileDialogComplete.bind(this),
				upload_start_handler : this.uploadStart.bind(this),
				upload_progress_handler : this.uploadProgress.bind(this),
				upload_error_handler : this.uploadError.bind(this),
				upload_success_handler : this.uploadSuccess.bind(this),
				upload_complete_handler : this.uploadComplete.bind(this),

				// Flash Settings
				flash_url : "/thirdparty/swfupload/swfupload_f9.swf",	// Relative to this file (or you can use absolute paths)
				
				// Debug Settings
				debug: false
			});
	},
	selectFiles:function(){
		this.uploader.selectFiles();
	},
	fileDialogStart:function(){
		this.uploader.cancelUpload();
	},
	fileQueue: function(fileObj){
		$('uploadFilename'+this.id).value = fileObj.name;
		$('uploadProgressText'+this.id).update('Filesize: ' + tuksi.util.humanFilesize(fileObj.size));
		tuksi.pagegenerator.addUpload(this);
	},
	fileQueueError: function(fileObj, error_code, message){
		var strErrorMsg = this.getError(error_code,message);
		$('uploadProgressText'+this.id).update(strErrorMsg);
	},
	fileDialogComplete: function(num_files_selected){
		if(num_files_selected > 0) {
			//this.uploader.startUpload();
		}
	},
	initUpload:function(){
		this.uploader.startUpload();
	},
	uploadStart: function(fileObj){
		return true;
	},
	uploadProgress: function(fileObj, bytesLoaded, bytesTotal){
		var w = (bytesLoaded / bytesTotal) * 270;
		var per = Math.round((bytesLoaded / bytesTotal) * 100);
		////console.log('uploadProgress loaded: ' + bytesLoaded + ' bytesTotal: ' + bytesTotal + ' w:'+w);
		$('uploadProgress'+this.id).setStyle({width:w+'px'});
		$('uploadProgressTextPopup'+this.id).update('Uploaded ' + per + '% of ' + fileObj.name);
	},
	uploadError: function(fileObj, error_code, message){
		//console.log(fileObj,error_code,message);
	},
	uploadSuccess: function(fileObj,server_data){
		$('uploadProgressTextPopup'+this.id).update('Finished uploading of ' + fileObj.name);
		$('fileUploaded'+this.id).value = server_data;
		tuksi.pagegenerator.uploadFinished(this);
	},
	uploadComplete: function(){
		//console.log('uploadComplete');
	},
	getError:function(error_code,message) {
		var strErrorMsg = "";
		switch(error_code) {
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				strErrorMsg = "File is too big.";
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				strErrorMsg = "Cannot upload Zero Byte files.";
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				strErrorMsg = "Invalid File Type.";
				break;
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
				strErrorMsg = "You have selected too many files.  " +  (message > 1 ? "You may only add " +  message + " more files" : "You cannot add any more files.");
				break;
			default:
				if (fileObj !== null) {
					strErrorMsg = "Unhandled Error";
				}
				break;
		}
		return strErrorMsg;
	}
});