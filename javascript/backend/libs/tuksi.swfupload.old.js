function ppp(){
	alert('dddd');
}
var tuksiSwfupload = {};
tuksiSwfupload =  Base.extend({
	constructor:function(id){
		this.base();
		this.id = id
		document.observe('dom:loaded',this.setUploader.bind(this));
	},	
	setUploader:function(){
		this.uploader = new SWFUpload({
				
			// Backend Settings
				upload_url: "/core/services/upload.php",
				post_params: {"id" : this.id},

				// File Upload Settings
				file_size_limit : "102400",	// 100MB
				file_types : "*.*",
				file_types_description : "All Files",
				file_upload_limit : "10",
				file_queue_limit : "0",

				// Event Handler Settings (all my handlers are in the Handler.js file)
				file_dialog_start_handler : this.uploadFileStart.bind(this),
				file_queued_handler : this.fileQueued.bind(this),
				file_queue_error_handler : this.uploadError.bind(this),
				file_dialog_complete_handler : this.uploadFileStart.bind(this),
				upload_start_handler : this.uploadFileStart.bind(this),
				upload_progress_handler : this.uploadProgress.bind(this),
				upload_error_handler : this.uploadError.bind(this),
				upload_success_handler : this.uploadFileComplete.bind(this),
				upload_complete_handler : this.uploadFileComplete.bind(this),

				// Flash Settings
				flash_url : "/core/thirdparty/swfupload/swfupload_f8.swf",	// Relative to this file (or you can use absolute paths)
				
				custom_settings : {
					progressTarget : "fsUploadProgress1"+this.id,
					cancelButtonId : "btnCancel1"
				},
				
				// Debug Settings
				debug: false
			});
	},
	selectFiles:function(){
		this.uploader.selectFiles();
	},
	uploadFileStart: function(file, position, queuelength) {
		$('fileUploadStatus').update(position);
	},
	fileQueued: function(file, queuelength){
		
		var listingfiles = document.getElementById("SWFUploadFileListingFiles");

		if(!listingfiles.getElementsByTagName("ul")[0]) {
			var ul = document.createElement("ul")
			listingfiles.appendChild(ul);
		}
		
		listingfiles = listingfiles.getElementsByTagName("ul")[0];
		
		var li = document.createElement("li");
		li.id = file.id;
		li.className = "SWFUploadFileItem";
		li.innerHTML = file.name + " <br><img src='/themes/default/images/general/bar.gif' style='height:10px;' id='" + file.id + "progress'><a id='" + file.id + "deletebtn' class='cancelbtn' href='javascript:swfu.cancelFile(\"" + file.id + "\");'><!-- IE --></a><br>";
	
		listingfiles.appendChild(li);
		
		var queueinfo = document.getElementById("queueinfo");
		queueinfo.innerHTML = queuelength + " files queued";
		document.getElementById(swfu.movieName + "UploadBtn").style.display = "block";
		document.getElementById("cancelqueuebtn").style.display = "block";
	},
	uploadFileCancelled: function(file, queuelength) {
		var li = document.getElementById(file.id);
		li.innerHTML = file.name + " - cancelled";
		li.className = "SWFUploadFileItem uploadCancelled";
		var queueinfo = document.getElementById("queueinfo");
		queueinfo.innerHTML = queuelength + " files queued";
	},
	uploadProgress:function(file, bytesLoaded) {
		alert('uploadProgress');
		var progress = document.getElementById(file.id + "progress");
		var percent = Math.ceil((bytesLoaded / file.size) * 200);
		progress.style.width = (percent) + "px";
	},
	uploadError:function(errno) {
			alert('uploadError');
		// SWFUpload.debug(errno);
	},
	uploadFileComplete: function(file) {
					alert('uploadFileComplete');
		var li = document.getElementById(file.id);
		li.className = "SWFUploadFileItem uploadCompleted";
	},
	cancelQueue:function() {
		swfu.cancelQueue();
		document.getElementById(swfu.movieName + "UploadBtn").style.display = "none";
		document.getElementById("cancelqueuebtn").style.display = "none";
	},
	uploadQueueComplete:function(file) {
		var div = document.getElementById("queueinfo");
		div.innerHTML = "All files uploaded..."
		document.getElementById("cancelqueuebtn").style.display = "none";
	}
});