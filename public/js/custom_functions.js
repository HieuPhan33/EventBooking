function addBookmark(id){
	document.getElementById(id).style.pointerEvents = 'none';
	$.ajax({
		type:'GET',
		url: '/bookmark/store',
		data : { eventID : id}
	}).done(function (msg){
		console.log(msg);
		document.getElementById(id).style.pointerEvents = 'auto';
		$('#'+id).attr("onclick","cancelBookmark(id)");
		$('#'+id).text('Remove Bookmark');

	});

}

function cancelBookmark(id){
	document.getElementById(id).style.pointerEvents = 'none';
	$.ajax({
		type:'GET',
		url: '/bookmark/remove',
		data : { eventID : id}
	}).done(function (msg){
		console.log(msg);
		document.getElementById(id).style.pointerEvents = 'auto';
		$('#'+id).attr("onclick","addBookmark(id)");
		$('#'+id).text('Bookmark Event');

	});
}

// function sendBookingMail(eventId){
// 	document.getElementById("book"+eventId).style.pointerEvents = 'none';
// 	console.log('start sending booking receipt');
// 	$.ajax({
// 		type:'POST',
// 		url:'/mail/bookingNotify',
// 		headers: {
//     	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//   		},
// 		data : { eventID : eventId }
// 	}).done(function(msg){
// 		console.log(msg);
// 	});

// 	$.ajax({
// 		type:'POST',
// 		url:'/mail/reminder',
// 		headers: {
//     	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//   		},
// 		data : { eventID : eventId }
// 	}).done(function(msg){
// 		console.log(msg);
// 	});
// }

// function sendCancelingMail(eventId){
// 	document.getElementById("cancel"+eventId).style.pointerEvents = 'none';
// 	console.log('start sending canceling confirmation');
// 	$.ajax({
// 		type:'POST',
// 		url:'/mail/cancelingNotify',
// 		headers: {
//     	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//   		},
// 		data : { eventID : eventId }
// 	}).done(function(msg){
// 		console.log(msg);
// 	});
// }

// function sendJoinQueueMail(eventId){
// 	document.getElementById("join"+eventId).style.pointerEvents = 'none';
// 	console.log('start sending joining queue confirmation');
// 	$.ajax({
// 		type:'POST',
// 		url:'/mail/joinQueueNotify',
// 		headers: {
//     	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//   		},
// 		data : { eventID : eventId }
// 	}).done(function(msg){
// 		console.log(msg);
// 	});
// }

function disable(id){
	document.getElementById(id).style.pointerEvents = 'none';
}

function generateChart(){
	var chartType = $('input:radio[name="chartOpt"]:checked').val();
	if(chartType == 0){
		generateAttendanceChartByCategory();
	}
	else if(chartType == 1){
		generateAttendanceChartByTime();
	}
	else if(chartType == 2){
		generateAttendanceChartByStudentType();
	}
	else if(chartType == 3){
		generateProfitChartByCategory();
	}
	else{
		generateProfitChartByTime();
	}
}

var chartData;
var chart;
function zoomChart() {
    if (chart.zoomToIndexes) {
        chart.zoomToIndexes(130, chartData.length - 1);
    }
}
function zoomChart2(){
    chart.zoomToIndexes(chart.dataProvider.length - 20, chart.dataProvider.length - 1);
}
function generateAttendanceChartByCategory(){
	$.ajax({
		type:'POST',
		url:'/chart/attendanceByCategory',
		headers: {
    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		},
		data : {}
	}).done(function(JSONdata){
		var data = $.parseJSON(JSONdata);
		console.log(data);
		$("#chartUndergradDiv").empty();
		$("#chartPostgradDiv").empty();
		$("#chartContainer").css({"background-color": "", "color": ""})
		$("#chartUndergradDiv").css({"width":"auto","height":"auto"});
		$("#chartPostgradDiv").css({"width":"auto","height":"auto"});
		$("#chartdiv").css({"width":"100%","height":"500px"});
		chart = AmCharts.makeChart("chartdiv", {
			"type": "serial",
		  	"titles": [
				{
					"text": "Expected vs Real Attendance of Student By Category",
					"size": 15
				}
			],
		     "theme": "light",
			"categoryField": "category",
			"rotate": true,
			"startDuration": 1,
			"categoryAxis": {
				"gridPosition": "start",
				"position": "left"
			},
			"trendLines": [],
			"graphs": [
				{
					"balloonText": "Expected:[[value]]",
					"fillAlphas": 0.8,
					"id": "AmGraph-1",
					"lineAlpha": 0.2,
					"title": "Expected",
					"type": "column",
					"valueField": "expectedAttendance"
				},
				{
					"balloonText": "Real:[[value]]",
					"fillAlphas": 0.8,
					"id": "AmGraph-2",
					"lineAlpha": 0.2,
					"title": "Real",
					"type": "column",
					"valueField": "realAttendance"
				}
			],
			"guides": [],
			"valueAxes": [
				{
					"id": "ValueAxis-1",
					"position": "top",
					"axisAlpha": 0
				}
			],
			"allLabels": [],
			"balloon": {},
			"dataProvider": data,
		    "export": {
		    	"enabled": true
		     }

		});
	});
}
function generateAttendanceChartByTime(){

	$.ajax({
		type:'POST',
		url:'/chart/attendanceByTime',
		headers: {
    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		},
		data : {}
	}).done(function(JSONdata){
		var data = $.parseJSON(JSONdata);
		console.log(data);
		chartData = data;
		$("#chartUndergradDiv").empty();
		$("#chartPostgradDiv").empty();
		$("#chartContainer").css({"background-color": "#3f3e3b", "color": "#fff"});
		$("#chartUndergradDiv").css({"width":"auto","height":"auto"});
		$("#chartPostgradDiv").css({"width":"auto","height":"auto"});
		$("#chartdiv").css({"width":"100%","height":"500px"});
		chart = AmCharts.makeChart("chartdiv", {
		    "type": "serial",
		    "theme": "chalk",
		    "legend": {
		        "useGraphSettings": true
		    },
		    "dataProvider": chartData,
		    "synchronizeGrid":true,
		    "valueAxes": [{
		        "id":"v1",
		        "axisColor": "#FF6600",
		        "axisThickness": 2,
		        "axisAlpha": 1,
		        "position": "left"
		    }, {
		        "id":"v2",
		        "axisColor": "#FCD202",
		        "axisThickness": 2,
		        "axisAlpha": 1,
		        "position": "right"
		    }],
		    "graphs": [{
		        "valueAxis": "v1",
		        "lineColor": "#FF6600",
		        "bullet": "round",
		        "bulletBorderThickness": 1,
		        "hideBulletsCount": 30,
		        "title": "Real Attendance",
		        "valueField": "realAttendance",
				"fillAlphas": 0
		    }, {
		        "valueAxis": "v2",
		        "lineColor": "#FCD202",
		        "bullet": "square",
		        "bulletBorderThickness": 1,
		        "hideBulletsCount": 30,
		        "title": "Expected Attendance",
		        "valueField": "expectedAttendance",
				"fillAlphas": 0
		    }],
		    "chartScrollbar": {},
		    "chartCursor": {
		        "cursorPosition": "mouse"
		    },
		    "categoryField": "month",
		    "categoryAxis": {
		        "axisColor": "#DADADA",
		    },
		    "export": {
		    	"enabled": true,
		        "position": "bottom-right"
		     }
		});
		chart.addListener("dataUpdated", zoomChart2);
		zoomChart2();
	});
}


function generateAttendanceChartByStudentType(){
	$.ajax({
		type:'POST',
		url:'chart/attendanceByUndergraduate',
		headers: {
    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		},
		data : {}
	}).done(function(JSONdata){
		var data = $.parseJSON(JSONdata);
		console.log(data);
		$("#chartdiv").empty();
		$("#chartContainer").css({"background-color": "", "color": ""})
		$("#chartUndergradDiv").css({"width":"100%","height":"500px"});
		$("#chartPostgradDiv").css({"width":"100%","height":"500px"});
		$("#chartdiv").css({"width":"auto","height":"auto"});
		chart = AmCharts.makeChart( "chartUndergradDiv", {
		  "type": "radar",
		  "theme": "patterns",
		  "titles": [
				{
					"text": "Attendance of Undergraduate Student By Category",
					"size": 15
				}
			],
		  "dataProvider": data,
		  "startDuration": 2,
		  "graphs": [ {
		    "balloonText": "[[value]] Undergraduate Students attend to [[category]] events",
		    "bullet": "round",
		    "lineThickness": 2,
		    "valueField": "attendance"
		  } ],
		  "categoryField": "name",
		  "export": {
		    "enabled": true
		  }
		} );
	});
	$.ajax({
		type:'POST',
		url:'chart/attendanceByPostgraduate',
		headers: {
    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		},
		data : {}
	}).done(function(JSONdata){
		var data = $.parseJSON(JSONdata);
		console.log(data);
		$("#chartdiv").empty();
		$("#chartdiv").css({"width":"auto","height":"auto"});
		$("#chartContainer").css({"background-color": "", "color": ""})
		$("#chartUndergradDiv").css({"width":"100%","height":"500px"});
		$("#chartPostgradDiv").css({"width":"100%","height":"500px"});
		chart = AmCharts.makeChart( "chartPostgradDiv", {
		  "type": "radar",
		  "theme": "patterns",
		  "titles": [
				{
					"text": "Attendance of Postgraduate Student By Category",
					"size": 15
				}
			],
		  "dataProvider": data,
		  "startDuration": 2,
		  "graphs": [ {
		    "balloonText": "[[value]] Postgrduate Students attend to [[category]] events",
		    "bullet": "round",
		    "lineThickness": 2,
		    "valueField": "attendance"
		  } ],
		  "categoryField": "name",
		  "export": {
		    "enabled": true
		  }
		} );
	});
}

function generateProfitChartByCategory(){
	$.ajax({
		type:'POST',
		url:'/chart/profitByCategory',
		headers: {
    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		},
		data : {}
	}).done(function(JSONdata){
		var data = $.parseJSON(JSONdata);
		console.log(data);
		$("#chartUndergradDiv").empty();
		$("#chartPostgradDiv").empty();
		$("#chartContainer").css({"background-color": "#3f3e3b", "color": "#fff"});
		$("#chartUndergradDiv").css({"width":"auto","height":"auto"});
		$("#chartPostgradDiv").css({"width":"auto","height":"auto"});
		$("#chartdiv").css({"width":"100%","height":"500px"});
		chart = AmCharts.makeChart("chartdiv", {
		    "theme": "chalk",
		    "type": "serial",
			 "titles": [
					{
						"text": "Profit By Category",
						"size": 15
					}
			],
			"startDuration": 2,
		    "dataProvider": data,
		    "valueAxes": [{
		        "position": "left",
		        "title": "Profit (in AUD)"
		    }],
		    "graphs": [{
		        "balloonText": "[[category]]: <b>$[[value]]</b>",
		        "fillColorsField": "color",
		        "fillAlphas": 1,
		        "lineAlpha": 0.1,
		        "type": "column",
		        "valueField": "profit"
		    }],
		    "depth3D": 20,
			"angle": 30,
		    "chartCursor": {
		        "categoryBalloonEnabled": false,
		        "cursorAlpha": 0,
		        "zoomable": false
		    },
		    "categoryField": "name",
		    "categoryAxis": {
		        "gridPosition": "start",
		        "labelRotation": 90
		    },
		    "export": {
		    	"enabled": true
		     }

		});
	});
}

function generateProfitChartByTime(){
	$.ajax({
		type:'POST',
		url:'/chart/profitByTime',
		headers: {
    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		},
		data : {}
	}).done(function(JSONdata){
		var data = $.parseJSON(JSONdata);
		console.log(data);
		chartData = data;
		$("#chartUndergradDiv").empty();
		$("#chartPostgradDiv").empty();
		$("#chartUndergradDiv").css({"width":"auto","height":"auto"});
		$("#chartPostgradDiv").css({"width":"auto","height":"auto"});
		$("#chartContainer").css({"background-color": "#3f3e3b", "color": "#fff"});
		$("#chartdiv").css({"width":"100%","height":"500px"});
		chart = AmCharts.makeChart("chartdiv", {
		    "theme": "chalk",
		    "type": "serial",
		  	"titles": [
				{
					"text": "Profit Over The Last 6 Months",
					"size": 15
				}
			],
		    "marginRight": 80,
		    "autoMarginOffset": 20,
		    "marginTop":20,
		    "dataProvider": chartData,
		    "valueAxes": [{
		        "id": "v1",
		        "axisAlpha": 0.1
		    }],
		    "graphs": [{
		        "useNegativeColorIfDown": true,
		        "balloonText": "[[category]]<br><b>value: $[[value]]</b>",
		        "bullet": "round",
		        "bulletBorderAlpha": 1,
		        "bulletBorderColor": "#FFFFFF",
		        "hideBulletsCount": 50,
		        "lineThickness": 2,
		        "lineColor": "#fdd400",
		        "negativeLineColor": "#67b7dc",
		        "valueField": "profit"
		    }],
		    "chartScrollbar": {
		        "scrollbarHeight": 5,
		        "backgroundAlpha": 0.1,
		        "backgroundColor": "#868686",
		        "selectedBackgroundColor": "#67b7dc",
		        "selectedBackgroundAlpha": 1
		    },
		    "chartCursor": {
		        "valueLineEnabled": true,
		        "valueLineBalloonEnabled": true
		    },
		    "categoryField": "date",
		    "categoryAxis": {
		        "parseDates": true,
		        "axisAlpha": 0,
		        "minHorizontalGap": 60
		    },
		    "export": {
		        "enabled": true
		    }
		});

		chart.addListener("dataUpdated", zoomChart);

	});
}


function validatePromoCode(codes){
	var code = $("#code").val();
	console.log(codes);
	var isValid = false;
	$.each(codes, function(key,value){
		if(code.localeCompare(key) == 0){
			$("#type").val(value);
			isValid = true;
			return false;
		}
	});
	console.log($("#type").val());
	$("#err").text("Invalid Promotional Codes");
	return isValid;
}

function validateCheckout(){
	return false;
}

function validateQuantity(){
	var promoCode = $("#checkoutForm input[name='promoCode']").val();
	var quantity = $("#checkoutForm select[name='quantity']").find(":selected").val();
	$("#checkoutButton").hide();
	if(quantity > 0 || promoCode != null){
		$("#checkoutButton").show();
	}
}


function addCodes(){
	var index = $("#numOfCodes").val();
	index++;
	var quantity = "quantity"+index;
	console.log(index);
	var quantityLabel = $("<label/>",{for:quantity,text:"quantity",class:"col-1 col-form-label"});
	var containerDiv = $("<div>",{class:"form-group row"});
	var div1 = $("<div/>",{class:"col-2"});
	var quantityInput = $("<input/>",{
		type:'text',
		name:quantity,
		id:quantity,
		class:"form-control"
	}).appendTo(div1);
	var type = "type"+index;
	var div2 = $("<div/>",{class:"col-1"});
	var typeLabel = $("<label/>",{for:type,text:"Discount ",class:"col-1 col-form-label"}).appendTo(div2);
	var div3 = $("<div/>",{class:"col-2"});
	var selectType = $('<select>',{id:type,text:"type",name:type}).appendTo(div3);
	for(var i = 1 ; i < 10; i++){
		var val = 1-i/10;
		var discountStr = i*10 + "%";
		selectType.append($("<option>").attr('value',val).text(discountStr));
	}
	containerDiv.append(quantityLabel, div1, div2, div3);
	$("#promoCodesContainer").append(containerDiv);
	$("#numOfCodes").val(index);
}


