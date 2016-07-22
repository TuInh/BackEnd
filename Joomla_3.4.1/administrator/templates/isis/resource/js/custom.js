$(function() {

	
});

function generateChart(defectStatistic){

    var lineChartData = {
    		labels: ["January", "February", "March", "April", "May", "June", "July", "August","September", "October", "November", "December"],
    	    datasets: [
    	        {
    	            label: "PERFORMANCE",
    	            fillColor: "rgba(220,220,220,0.2)",
    	            strokeColor: "rgba(220,220,220,1)",
    	            pointColor: "rgba(220,220,220,1)",
    	            pointStrokeColor: "#fff",
    	            pointHighlightFill: "#fff",
    	            pointHighlightStroke: "rgba(220,220,220,1)",
    	            data: defectStatistic.performance
    	        },
    	        {
    	            label: "MANUAL",
    	            fillColor: "rgba(151,187,205,0.2)",
    	            strokeColor: "rgba(151,187,205,1)",
    	            pointColor: "rgba(151,187,205,1)",
    	            pointStrokeColor: "#fff",
    	            pointHighlightFill: "#fff",
    	            pointHighlightStroke: "rgba(151,187,205,1)",
    	            data: defectStatistic.manual
    	        },
    	        {
    	            label: "API",
    	            fillColor: "rgba(151,187,205,0.2)",
    	            strokeColor: "rgba(151,187,205,1)",
    	            pointColor: "rgba(151,187,205,1)",
    	            pointStrokeColor: "#fff",
    	            pointHighlightFill: "#fff",
    	            pointHighlightStroke: "rgba(151,187,205,1)",
    	            data: defectStatistic.api
    	        },
    	        {
    	            label: "CODE OPTIMIZE",
    	            fillColor: "rgba(151,187,205,0.2)",
    	            strokeColor: "rgba(151,187,205,1)",
    	            pointColor: "rgba(151,187,205,1)",
    	            pointStrokeColor: "#fff",
    	            pointHighlightFill: "#fff",
    	            pointHighlightStroke: "rgba(151,187,205,1)",
    	            data: defectStatistic.code_optimize
    	        }
    	    ]
    };

    var myLine = new Chart(document.getElementById("area-chart").getContext("2d")).Line(lineChartData, {
        bezierCurve: false,
        scaleShowLabels: true,
        legendTemplate : "<ul id=\"<%=name.toLowerCase()%>-legend\" class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
        
             
    });
    var legend = myLine.generateLegend();

    document.getElementById("area-chart").onclick = function(evt){
        var activePoints = myLine.getPointsAtEvent(evt);
        // => activePoints is an array of points on the canvas that are at the same position as the click event.
    };
    
    var legendElement =  document.getElementById('line-legend');
    if (typeof(legendElement) != 'undefined' && legendElement != null)
    {
    	 document.getElementById("issue-chart").removeChild(legendElement)
    }
   
    //and append it to your page somewhere
    document.getElementById("issue-chart").insertAdjacentHTML('beforeend',legend);

}
function getDefect(data)
{
	var url = "index.php?option=com_defect&task=DefectStatistic.listDefectDetail&data="+data;
	
	jQuery.ajax({
        type: "GET",
        url: url,
        beforeSend: function( e ) {
        	console.log("beforeSend");
         },
        dataType: 'json',
        success: function(data){
        	var defectStatistic = data.messages[0].data;
        	
        	console.log(defectStatistic);
        	
        	generateChart(defectStatistic);
        	
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(textStatus);
        }
    });
}

function getDefectStatus(){
	var url = "index.php?option=com_defect&task=DefectStatistic.generateDefectStatus";
	
	jQuery.ajax({
        type: "GET",
        url: url,
        beforeSend: function( e ) {
        	console.log("beforeSend getDefectStatus");
         },
        dataType: 'json',
        success: function(data){
        	var defectStatistic = data.messages[0].data;
        	
        	var defectStatusElement = document.getElementsByClassName('defect-status-statistic');
        	
        	
        	defectStatusElement[0].getElementsByClassName("value")[0].innerHTML = defectStatistic['total'];
        	defectStatusElement[1].getElementsByClassName("value")[0].innerHTML = defectStatistic['open'];
        	defectStatusElement[2].getElementsByClassName("value")[0].innerHTML = defectStatistic['resolve'];
        	defectStatusElement[3].getElementsByClassName("value")[0].innerHTML = defectStatistic['close'];
        		
        		

        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(errorThrown);
        }
    });
}

jQuery(document).ready(function(){
	
	var defectStatistic = document.getElementById('defect-statistic');
	
	defectStatistic.onkeypress = function(event) {
		var keyPressed = event.keyCode || event.which;

        //if ENTER is pressed
        if(keyPressed==13)
        {
        	getDefect(defectStatistic.value);
            keyPressed=null;
        }
       
	};
	
	
	// Return today's date and time
	var currentTime = new Date();

	// returns the year (four digits)
	var year = currentTime.getFullYear();
	
	getDefect(year);
	getDefectStatus();
});


