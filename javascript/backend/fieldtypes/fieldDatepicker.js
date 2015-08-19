/* ANSI Datepicker Calendar - David Lee 2005

  david [at] davelee [dot] com [dot] au

  project homepage: http://projects.exactlyoneturtle.com/date_picker/

  License:
  use, modify and distribute freely as long as this header remains intact;
  please mail any improvements to the author
*/

var DatePicker = {
  version: 0.31,

  /* Configuration options */

  // if false, hide last row if empty
  constantHeight: true,

  // show select list for year?
  useDropForYear: false,

  // show select list for month?
  useDropForMonth: false,

  // number of years before current to show in select list
  yearsPriorInDrop: 10,

  // number of years after current to show in select list
  yearsNextInDrop: 10,

  // The current year
  year: new Date().getFullYear(),
  
  // The first day of the week (0=Sunday, 1=Monday, ...)
  firstDayOfWeek: 1,

  // show only 3 chars for month in link
  abbreviateMonthInLink: true,

  // show only 2 chars for year in link
  abbreviateYearInLink: false,

  // eg 1st
  showDaySuffixInLink: false,

  // eg 1st; doesn't play nice w/ month selector
  showDaySuffixInCalendar: false,

  // px size written inline when month selector used
  largeCellSize: 22,

  // if set, choosing a day will send the date to this URL, eg 'someUrl?date='
  urlBase: null,

  // show a cancel button to revert choice
  showCancelLink: true,
  
  useTime: false,
  useHour: false,

  // stores link text to revert to when cancelling
  _priorLinkText: [],

  // stores date before datepicker to revert to when cancelling
  _priorDate: [],

  months: 'January,February,March,April,May,June,July,August,September,October,November,December'.split(','),

  days: 'Sun,Mon,Tue,Wed,Thu,Fri,Sat'.split(','),
  
  time: 'Time',
  

  /* Method declarations */

  toggleDatePicker: function (id,usetime,usehour) {
    
  	if(usetime)
  		this.useTime = true;
  	else	
  		this.useTime = false;
  
  	if(usehour)
  		this.useHour = true;
  	else	
  		this.useHour = false;
  		
  	if(document.getElementById(id).value == "") {
		
  		var newDate = new Date();
			var month = newDate.getMonth() + 1;

		if(month < 10) {
			month = '0' + month;
		}
		
		var day = newDate.getDate();
		if(day < 10) {
			day = '0' + day;
		}
		
		var strTime = day + '-' + month + '-' + newDate.getFullYear();
  		
  	if(this.useTime) {
	  	var minute = newDate.getMinutes();
	  	if(minute < 10) {
				minute = '0' + minute;
			}
	  	var hour = newDate.getHours();
			if(hour < 10) {
				hour = '0' + hour;
			}
	  	strTime+= ' '+ hour +':'+ minute;
  	}
  	if(this.useHour) {
  		var hour = newDate.getHours();
			if(hour < 10) {
				hour = '0' + hour;
			}
		  strTime+= ' '+ hour;
  	}
  	document.getElementById(id).value = strTime;
  	}
  		
  	if ($('pickdate_'+id)) {  // If showing, hide
      if($('pickdate_'+id).style.display == 'block') {
      	$('pickdate_'+id).style.display = 'none'
      } else {
      	this._priorDate[id] = this.customDateFormat(document.getElementById(id).value,id);
      	this.arrCurrentDate = this.getDateAsArray(this._priorDate[id]);
      	this.writeCalendar(id);
      }
    } else {     
    	this._priorDate[id] = this.customDateFormat(document.getElementById(id).value,id);
      this.arrCurrentDate = this.getDateAsArray(this._priorDate[id]);
      this.writeCalendar(id);
    }
  },
  hideSelects: function(id){
  	var selects = document.getElementsByTagName('select');
  	for(var i = 0;i < selects.length;i++) {
  		selects[i].style.visibility = 'hidden';
  	}
  },
  showSelects: function(){
  	var selects = document.getElementsByTagName('select');
  	for(var i = 0;i < selects.length;i++) {
  		selects[i].style.visibility = 'visible';
  	}
  },	
  getDateAsArray: function(value){
  	 var datetime = value.split("-");
  	 if(3 == datetime.length) {
  	 	datetime[0] = Number(datetime[0]);
  	 	datetime[1] = Number(datetime[1]);
  	 	datetime[2] = Number(datetime[2]);
  	 	return datetime;
  	 }
  },
  customDateFormat: function(value,id){
  		var returnValue = '';
  		if(value.length > 0) {
			if(this.useTime) {
				if(value.match(/^([1-9]|0[1-9]|[12][0-9]|3[01])\-([1-9]|0[1-9]|1[012])\-(19[0-9][0-9]|20[0-9][0-9])\ ([0-9]|[0-1][0-9]|2[0-3]):([1-9]|[0-5][0-9])$/)) {
					var datetime = value.split(" ");
					var myDate = datetime[0].split("-");
					var Time = datetime[1].split(":");
					returnValue = myDate[2] + '-' + myDate[1] + '-' + myDate[0];
					this.hour = Time[0];
					this.min = Time[1];	
				} else {
					var myDate = new Date();

					this.min = myDate.getMinutes();
					if(this.min < 10) {
						this.min = '0' + this.min;
					}
					this.hour = myDate.getHours();
					if(this.hour < 10) {
						this.hour = '0' + this.hour;
					}
					
					var month = myDate.getMonth() + 1;
					returnValue = myDate.getFullYear() + '-' + month + '-' + myDate.getDate();
					//update inputfield
					$(id).value = myDate.getDate() + '-' + month + '-' + myDate.getFullYear()  + ' ' + this.hour + ':' + this.min;
					
				}
			} else if(this.useHour){
				if(value.match(/^([1-9]|0[1-9]|[12][0-9]|3[01])\-([1-9]|0[1-9]|1[012])\-(19[0-9][0-9]|20[0-9][0-9])\ ([0-9]|[0-1][0-9]|2[0-3])$/)) {
					var datetime = value.split(" ");
					var myDate = datetime[0].split("-");
					var Time = datetime[1].split(":");
					returnValue = myDate[2] + '-' + myDate[1] + '-' + myDate[0];
					this.hour = Time[0];
				} else {
					var myDate = new Date();
				
					this.hour = myDate.getHours();
					if(this.hour < 10) {
						this.hour = '0' + this.hour;
					}
					
					var month = myDate.getMonth() + 1;
					returnValue = myDate.getFullYear() + '-' + month + '-' + myDate.getDate();
					//update inputfield
					$(id).value = myDate.getDate() + '-' + month + '-' + myDate.getFullYear()  + ' ' + this.hour;
					
				}
  		} else {

				if(value.match(/^([1-9]|0[1-9]|[12][0-9]|3[01])\-([1-9]|0[1-9]|1[012])\-(19[0-9][0-9]|20[0-9][0-9])$/)){
					var myDate = value.split("-");	
					returnValue = myDate[2] + '-' + myDate[1] + '-' + myDate[0];
				} else {
					var myDate = new Date();
					var month = myDate.getMonth() + 1;
					
					returnValue = myDate.getFullYear() + '-' + month + '-' + myDate.getDate();
					
					$(id).value = myDate.getDate() + '-' + month + '-' + myDate.getFullYear();
				}
			}
		}
		this._currentDate = returnValue;
  		return returnValue;	
  },
   customDateFormatReverse: function(value){
   	
   	var myDate = value.split("-");
		
   	if(myDate[1].length == 1)
			myDate[1] = '0' + myDate[1];
		if(myDate[2].length == 1)
			myDate[2] = '0' + myDate[2];	
		
		returnValue = myDate[2] + '-' +  myDate[1] + '-' + myDate[0];
			
		if(this.useTime) {
			returnValue = returnValue + ' ' + $F('hour') + ':' + $F('min');
		}	
		if(this.useHour) {
			returnValue = returnValue + ' ' + $F('hour');
		}	
				
		return returnValue;	
  },
  
  cancel: function (id) {
    document.getElementById(id).value = this.customDateFormatReverse(this._priorDate[id],id);
    tuksi_divPopup.hide();
    this.showSelects();
  },

  // mitigate clipping when new month has less days than selected date
  unclipDates: function (d1, d2) {
    if (d2.getDate() != d1.getDate()) {
      d2 = new Date(d2.getFullYear(), d2.getMonth(), 0);
    }

    return d2;
  },

  // change date given an offset from the current date as a number of months (+-)
  changeCalendar: function (id, offset) {
    var d1 = this.getSelectedDate(id), d2;
    if (offset % 12 == 0) { // 1 year forward / back (fix Safari bug)
      d2 = new Date (d1.getFullYear() + offset / 12, d1.getMonth(), d1.getDate() );
    } else if (d1.getMonth() == 0 && offset == -1) {// tiptoe around another Safari bug
      d2 = new Date (d1.getFullYear() - 1, 11, d1.getDate() );
    } else {
      d2 = new Date (d1.getFullYear(), d1.getMonth() + offset, d1.getDate() );
    }

    d2 = this.unclipDates(d1, d2);
    ansi_date = d2.getFullYear() + '-' + (d2.getMonth() + 1) + '-' + d2.getDate();
    this.setDate(id, ansi_date);
    this.writeCalendar(id);
  },

  setDate: function (id, ansiDate) {
  	document.getElementById(id).value = this.customDateFormatReverse(ansiDate);
  },
  setTime: function(id){
  	if(this.useTime) {
			var datetime = this._currentDate.split('-');
			newDate = datetime[2] + '-' + datetime[1] + '-' + datetime[0] + ' ' + $F('hour') + ':' + $F('min');
  		document.getElementById(id).value = newDate;
		}	
		if(this.useHour) {
			var datetime = this._currentDate.split('-');
			newDate = datetime[2] + '-' + datetime[1] + '-' + datetime[0] + ' ' + $F('hour');
  		document.getElementById(id).value = newDate;
		}
  },
  close: function(id) {
  		this.setTime(id);
  		this.showSelects();
      $('datepick_'+id).style.display = "none";
  },
  pickDate: function (id, ansi_date) {
  	this.setDate(id, ansi_date);
    this.toggleDatePicker(id,this.useTime,this.useHour);
    if (this.urlBase) {
      document.location.href = this.urlBase + ansi_date
    }
  },

  getMonthName: function(monthNum) { //anomalous
    return this.months[monthNum];
  },

  dateFromAnsiDate: function (ansi_date) {
    return new Date(ansi_date.split('-')[0], Number(ansi_date.split('-')[1]) - 1, ansi_date.split('-')[2])
  },

  ansiDateFromDate: function(date) {
    alert( date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate() );
  },

  getSelectedDate: function (id) {
  	if (document.getElementById(id).value == '') return new Date(); // default to today if no value exists
    return this.dateFromAnsiDate(this.customDateFormat(document.getElementById(id).value,id));
  },

  makeChangeCalendarLink: function (id, label, offset) {
    return ('<a href="#" onclick="DatePicker.changeCalendar(\''+id+'\','+offset+')">' + label + '</a>');
  },

  formatDay: function (n) {
    var x;
    switch (String(n)){
      case '1' :
      case '21': case '31': x = 'st'; break;
      case '2' : case '22': x = 'nd'; break;
      case '3' : case '23': x = 'rd'; break;
      default:
        x = 'th';
    }

    return n + x;
  },

  writeMonth: function (id, n) {
    if (this.useDropForMonth) {
      var opts = '';
      for (i in this.months) {
        sel = (i == this.getSelectedDate(id).getMonth() ? 'selected="selected" ' : '');
        opts += '<option ' + sel + 'value="'+ i +'">' + this.getMonthName(i) + '</option>';
      }

      return '<select onchange="DatePicker.selectMonth(\'' + id + '\', this.value)">' + opts + '</select>';
    } else {
      return this.getMonthName(n)
    }
  },

  writeYear: function (id, n) {
    if (this.useDropForYear) {
      var min = this.year - this.yearsPriorInDrop;
      var max = this.year + this.yearsNextInDrop;
      var opts = '';
      for (i = min; i < max; i++) {
        sel = (i == this.getSelectedDate(id).getFullYear() ? 'selected="selected" ' : '');
        opts += '<option ' + sel + 'value="'+ i +'">' + i + '</option>';
      }

      return '<select onchange="DatePicker.selectYear(\'' + id + '\', this.value)">' + opts + '</select>';
    } else {
      return n
    }
  },

  selectMonth: function (id, n) {
    d = this.getSelectedDate(id)
    d2 = new Date(d.getFullYear(), n, d.getDate())
    d2 = this.unclipDates(d, d2)
    this.setDate(id, d2.getFullYear() + '-' + (Number(n)+1) + '-' + d2.getDate() )
    this.writeCalendar(id)
  },

  selectYear: function (id, n) {
    d = this.getSelectedDate(id)
    d2 = new Date(n, d.getMonth(), d.getDate())
    d2 = this.unclipDates(d, d2)
    this.setDate(id, n + '-' + (d2.getMonth()+1) + '-' + d2.getDate() )
    this.writeCalendar(id)
  },
	adjustTime: function(id,type,dir){
		
		var currentVal = $(type).value;
		
		if(isNaN(currentVal) || currentVal == '') {
			currentVal = 0;
		} else if (currentVal.substring(0,1) == '0') {
			currentVal = currentVal.substring(1,2)
		}
		
		if(dir == 'up') {
			newVal = parseInt(currentVal) + 1;
		} else {
			newVal = parseInt(currentVal) - 1;
		}
		if(type == 'min') {	
			if(newVal > 59) {
				newVal = 0;	
			}else if(newVal < 0) {
				newVal = 59;	
			}
		} else {
			if(newVal > 23) {
				newVal = 0;
			}else if(newVal < 0){
				newVal = 23;
			}
		}
		if(newVal < 10) {
			newVal = '0' + newVal;	
		}
		
		$(type).value = newVal;
		this.setTime(id);
	},
		
  writeCalendar: function (id) {
  	
  	var date = this.getSelectedDate(id);
    var firstWeekday = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
    var lastDateOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    var day  = 1; // current day of month

    // not quite entirely pointless: fix Safari display bug with absolute positioned div
    //this.findLinkElement(id).innerHTML = this.findLinkElement(id).innerHTML;

    var o = '<div class="tableWrapper" style="display:block;">';
    o += '<table class="top">';
		o += '<td class="previous"><a href="#" onclick="DatePicker.changeCalendar(\''+id+'\',-1)" title="Forrige måned">&lt;</a></td>';
		o += '<td>' + (this.showDaySuffixInCalendar ? this.formatDay(date.getDate()) : date.getDate()) +
      ' ' + this.writeMonth(id, date.getMonth()) + " " + this.writeYear(id,  date.getFullYear()) + '</td>';
		o += '<td class="next"><a href="#" onclick="DatePicker.changeCalendar(\''+id+'\',1)" title="Næste måned">&lt;</a></td>';
		o += '</table>';
	  
    // day labels
    o += '<table class="header"><tr><th class="column1"><span>Uge</span></th>';
    
    for(var i = 0; i < this.days.length; i++) {
      o += '<th><span>' + this.days[(i+this.firstDayOfWeek) % 7] + '</span></th>';
    }
    
    o += '</tr></table><table class="middle">';

    // day grid
    
    for(rows = 1; rows < 7 && (this.constantHeight || day < lastDateOfMonth); rows++) {

    	if(day <= lastDateOfMonth) {
    	o += '<tr>';
      for(var day_num = 0; day_num < this.days.length; day_num++) {
        
      var translated_day = (this.firstDayOfWeek + day_num) % 7;
			
      if(day_num == 0) {
      	var d = new Date(date.getFullYear(), date.getMonth(), day);
      	var weekNumber = d.getWeek(1)
      		o += '<td class="column1"><span>'+weekNumber+'</span></td>';
      }
			
      if(firstWeekday == 0 && day == 1) {
				if(day_num == 6) {
					args = [id, (date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + day)];
					if(date.getDate() == day) {
						style = ' class="active" ';
					} else {
						style = '';
					}
					o +=
					'<td>' + // link : each day
					"<a " + style + " href=\"#\" onclick=\"DatePicker.pickDate('" + args.join("','") + "'); return false;\">" + day + '</a>' +
					'</td>';
					day++;          
				} else {
					o += '<td></td>';
				}
			} else if ((translated_day >= firstWeekday || day > 1) && (day <= lastDateOfMonth) ) {
				args = [id, (date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + day)];
				if(date.getDate() == day) {
					style = ' class="active" ';
				} else {
					style = '';
				}
				o +=
				'<td>' + // link : each day
				"<a " + style + "href=\"#\" onclick=\"DatePicker.pickDate('" + args.join("','") + "'); return false;\">" + day + '</a>' +
				'</td>';
				day++;          
			} else {
				o += '<td></td>';
			}
      }
		}
      o += '</tr>';
    }
	 	o += '</table>'
    o += '<table class="bottom">';
	 	
    if(this.useTime) {
    	o += '<tr><td class="column1">'+ this.time +':</td>	<td class="column2">';
			o += '<input type="text" class="text" value="'+this.hour+'" id="hour">';
			o += '</td><td class="column3">';
			o += '<span class="previous" title="Forrige time" onclick="DatePicker.adjustTime(\''+id+'\',\'hour\',\'up\');"></span>';
			o += '<span class="next" title="Næste time" onclick="DatePicker.adjustTime(\''+id+'\',\'hour\',\'down\');"></span>';
			o += '</td><td class="column4">:</td><td class="column5">';
			o += '<input type="text" class="text" id="min" value="'+this.min+'">';
			o += '</td><td class="column6">';
			o += '<span class="previous" title="Forrige minut" onclick="DatePicker.adjustTime(\''+id+'\',\'min\',\'up\');"></span>';
			o += '<span class="next" title="Næste minut" onclick="DatePicker.adjustTime(\''+id+'\',\'min\',\'down\');"></span>';
			o += '</td></tr><tr>'
    } 
     if(this.useHour) {
    	o += '<tr><td class="column1">'+ this.time +':</td>	<td class="column2">';
			o += '<input type="text" class="text" value="'+this.hour+'" id="hour">';
			o += '</td><td class="column3">';
			o += '<span class="previous" title="Forrige time" onclick="DatePicker.adjustTime(\''+id+'\',\'hour\',\'up\');"></span>';
			o += '<span class="next" title="Næste time" onclick="DatePicker.adjustTime(\''+id+'\',\'hour\',\'down\');"></span>';
			o += '</td></tr><tr>'
    } 
    
    
    o += '<tr><td colspan="6"><a href="#" onclick="DatePicker.close(\''+id+'\');" class="button buttonType3"><span><span>Ok</span></span></a></td></tr></table>';
    
    /*$('datepick_'+id).innerHTML = o;
    $('datepick_'+id).style.display = "block";*/
    
    if(!$('datepick_wrapper_'+id)) {
    	
    	var btnPos = Position.page($('datespick_btn_'+id));
    	var scrollFramePos = Position.page($('scrollFrame'));
    	var d = document.createElement('div');
    	d.id = 'datepick_wrapper_'+id;
    	d.className = 'mCalenderPicker';
    	d.innerHTML = '<div class="positionAbsolute"><div class="tableWrapper" id="datepick_'+id+'">'+o+'</div></div>';
    	if(Prototype.Browser.Gecko){
    		 btnPos[0] =  btnPos[0] - 110;
    	}
    	
      // btnPos[1] = btnPos[1] + $('scrollFrame').scrollTop;
    	d.style.left = btnPos[0] - scrollFramePos[0] + 'px';
    	d.style.top = btnPos[1] - scrollFramePos[1] + 'px';
    	d.style.position = 'absolute';
    	document.getElementById('scrollFrame').appendChild(d);
    } else {
    	$('datepick_'+id).innerHTML = o;
    	$('datepick_'+id).style.display = "block";
    }
    
    
    //tuksi.window.popup({fromajax:false,content:o,placement:'handle_',isDraggable:false});
  },
  
  findLinkElement: function(id) {
    return document.getElementById('_' + id + '_link');
  },
  getWeekNr:function(date) {
		Year = this.takeYear(date);
		Month = date.getMonth();
		Day = date.getDate();
		now = Date.UTC(Year,Month,Day,0,0,0);
		var Firstday = new Date();
		Firstday.setYear(Year);
		Firstday.setMonth(0);
		Firstday.setDate(1);
		then = Date.UTC(Year,0,1,0,0,0);
		var Compensation = Firstday.getDay();
		if (Compensation > 3) Compensation -= 4;
		else Compensation += 3;
		NumberOfWeek =  Math.round((((now-then)/86400000)+Compensation)/7);
		return NumberOfWeek;
	},


	takeYear:function(theDate) {
		x = theDate.getYear();
		var y = x % 100;
		y += (y < 38) ? 2000 : 1900;
		return y;
	}
};

Date.prototype.getWeek = function (dowOffset) {
	/*getWeek() was developed by Nick Baicoianu at MeanFreePath: http://www.meanfreepath.com */
	dowOffset = 1;
	var newYear = new Date(this.getFullYear(),0,1);
	var day = newYear.getDay() - dowOffset; //the day of week the year begins on
	day = (day >= 0 ? day : day + 7);
	var daynum = Math.floor((this.getTime() - newYear.getTime() -
	(this.getTimezoneOffset()-newYear.getTimezoneOffset())*60000)/86400000) + 1;
	var weeknum;
	//if the year starts before the middle of a week
	if(day < 4) {
		weeknum = Math.floor((daynum+day-1)/7) + 1;
	if(weeknum > 52) {
		nYear = new Date(this.getFullYear() + 1,0,1);
		nday = nYear.getDay() - dowOffset;
		nday = nday >= 0 ? nday : nday + 7;
		/*if the next year starts before the middle of
		the week, it is week #1 of that year*/
		weeknum = nday < 4 ? 1 : 53;
	}
}
else {
weeknum = Math.floor((daynum+day-1)/7);
}
return weeknum;
};
