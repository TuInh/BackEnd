<?php
/**
 * @package     JIsrael Template package
 * @subpackage  Templates.jisrael
 * @copyright   Copyright (C) 2005 - 2013 J-Guru.com, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.0
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$lang = JFactory::getLanguage();
$this->language = $doc->language;
$this->direction = $doc->direction;
$input = $app->input;
$user = JFactory::getUser();

$stylelink = '';
$stylelink .= '<link rel="stylesheet" href="'.Juri::base().'templates/' . $this->template . '/resource/css/bootstrap.min.css" >'."\n";
$stylelink .= '<link rel="stylesheet" href="'.Juri::base().'templates/' . $this->template . '/resource/css/bootstrap-responsive.min.css" >'."\n";
$stylelink .= '<link rel="stylesheet" href="'.Juri::base().'templates/' . $this->template . '/resource/css/font-awesome.css" >'."\n";
$stylelink .= '<link rel="stylesheet" href="'.Juri::base().'templates/' . $this->template . '/resource/css/style.css" >'."\n";
$stylelink .= '<link rel="stylesheet" href="'.Juri::base().'templates/' . $this->template . '/resource/css/pages/dashboard.css" >'."\n";

$script = '';
$script .= '<script type="text/javascript" src= "'.Juri::base().'templates/' . $this->template . '/resource/js/jquery-1.7.2.min.js"></script>'."\n";
$script .= '<script type="text/javascript" src= "'.Juri::base().'templates/' . $this->template . '/resource/js/excanvas.min.js"></script>'."\n";
$script .= '<script type="text/javascript" src= "'.Juri::base().'templates/' . $this->template . '/resource/js/chart.js"></script>'."\n";
$script .= '<script type="text/javascript" src= "'.Juri::base().'templates/' . $this->template . '/resource/js/bootstrap.js"></script>'."\n";
$script .= '<script type="text/javascript" src= "'.Juri::base().'templates/' . $this->template . '/resource/js/full-calendar/fullcalendar.min.js"></script>'."\n";
$script .= '<script type="text/javascript" src= "'.Juri::base().'templates/' . $this->template . '/resource/js/base.js"></script>'."\n";

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Dashboard - Bootstrap Admin Template</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">

<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2013',  1000,      400],
          ['2014',  1170,      460],
          ['2015',  660,       1120],
          ['2016',  1030,      540]
        ]);

        var options = {
          title: 'Company Performance',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          chartArea:{left:50,top:50,width:"75%",height:"75%"},
          vAxis: {minValue: 0},
          width: 500
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    
<?php 
echo $stylelink;
echo $script;
?>
</head>
<body>

<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container"> <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span
                    class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span> </a><a class="brand" href="<?php echo JUri::base();?>">Bootstrap Admin Template </a>
      <div class="nav-collapse">
        <ul class="nav pull-right">
        
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                            class="icon-cog"></i> System <b class="caret"></b></a>
            <ul class="dropdown-menu">
            
            	<?php 
		        	if ($user->authorise('core.admin'))
		        	{
		        		echo '<li><a href="'.JUri::base().'index.php?option=com_config">'.JText::_('MOD_MENU_CONFIGURATION').'</a></li>';
		        	}
		        	
		        	if ($user->authorise('core.manage', 'com_checkin'))
		        	{
		        		echo '<li class="divider"></li>';
		        		echo '<li><a href="'.JUri::base().'index.php?option=com_checkin">'.JText::_('MOD_MENU_GLOBAL_CHECKIN').'</a></li>';
		        	}
		        	
		        	if ($user->authorise('core.manage', 'com_cache'))
		        	{
		        		echo '<li><a href="'.JUri::base().'index.php?option=com_cache">'.JText::_('MOD_MENU_CLEAR_CACHE').'</a></li>';
		        		echo '<li><a href="'.JUri::base().'index.php?option=com_cache&view=purge">'.JText::_('MOD_MENU_PURGE_EXPIRED_CACHE').'</a></li>';
		        	}
		        	
		        	if ($user->authorise('core.admin'))
		        	{
		        		echo '<li class="divider"></li>';
		        		echo '<li><a href="'.JUri::base().'index.php?option=com_admin&view=sysinfo">'.JText::_('MOD_MENU_SYSTEM_INFORMATION').'</a></li>';
		        	}
		        	 
		        	
		       ?>
              
              <li><a href="javascript:;">Help</a></li>
            </ul>
          </li>
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                            class="icon-user"></i> C portal<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li>
				<span>
					<span class="icon-user"></span>
					<strong><?php echo $user->name; ?></strong>
				</span>
			  </li>
			  <li class="divider"></li>
              <li><a href="index.php?option=com_admin&task=profile.edit&id=<?php echo $user->id; ?>"><?php echo JText::_('TPL_JISRAEL_EDIT_ACCOUNT'); ?></a>
							</li>
              <li><a href="<?php echo JRoute::_('index.php?option=com_login&task=logout&' . JSession::getFormToken() . '=1'); ?>"><?php echo JText::_('TPL_JISRAEL_LOGOUT'); ?></a>
							</li>
            </ul>
          </li>
        </ul>
        <form class="navbar-search pull-right">
          <input type="text" class="search-query" placeholder="Search">
        </form>
      </div>
      <!--/.nav-collapse --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /navbar-inner --> 
</div>
<!-- /navbar -->
<div class="subnavbar">
  <div class="subnavbar-inner">
    <div class="container">
      <ul class="mainnav">
        <li class="active"><a href="index.html"><i class="icon-dashboard"></i><span>Dashboard</span> </a> </li>
        <li><a href="reports.html"><i class="icon-list-alt"></i><span>Reports</span> </a> </li>
        <li><a href="guidely.html"><i class="icon-facetime-video"></i><span>App Tour</span> </a></li>
        <li><a href="charts.html"><i class="icon-bar-chart"></i><span>Charts</span> </a> </li>
        <li><a href="shortcodes.html"><i class="icon-code"></i><span>Shortcodes</span> </a> </li>
        <li class="dropdown"><a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-long-arrow-down"></i><span>Drops</span> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="icons.html">Icons</a></li>
            <li><a href="faq.html">FAQ</a></li>
            <li><a href="pricing.html">Pricing Plans</a></li>
            <li><a href="login.html">Login</a></li>
            <li><a href="signup.html">Signup</a></li>
            <li><a href="error.html">404</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <!-- /container --> 
  </div>
  <!-- /subnavbar-inner --> 
</div>
<!-- /subnavbar -->
<div class="main">
  <jdoc:include type="component" />
</div>
<!-- /main -->
<div class="extra">
  <div class="extra-inner">
    <div class="container">
      <div class="row">
                    <div class="span3">
                        <h4>
                            About Free Admin Template</h4>
                        <ul>
                            <li><a href="javascript:;">EGrappler.com</a></li>
                            <li><a href="javascript:;">Web Development Resources</a></li>
                            <li><a href="javascript:;">Responsive HTML5 Portfolio Templates</a></li>
                            <li><a href="javascript:;">Free Resources and Scripts</a></li>
                        </ul>
                    </div>
                    <!-- /span3 -->
                    <div class="span3">
                        <h4>
                            Support</h4>
                        <ul>
                            <li><a href="javascript:;">Frequently Asked Questions</a></li>
                            <li><a href="javascript:;">Ask a Question</a></li>
                            <li><a href="javascript:;">Video Tutorial</a></li>
                            <li><a href="javascript:;">Feedback</a></li>
                        </ul>
                    </div>
                    <!-- /span3 -->
                    <div class="span3">
                        <h4>
                            Something Legal</h4>
                        <ul>
                            <li><a href="javascript:;">Read License</a></li>
                            <li><a href="javascript:;">Terms of Use</a></li>
                            <li><a href="javascript:;">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <!-- /span3 -->
                    <div class="span3">
                        <h4>
                            Open Source jQuery Plugins</h4>
                        <ul>
                            <li><a href="http://www.egrappler.com">Open Source jQuery Plugins</a></li>
                            <li><a href="http://www.egrappler.com;">HTML5 Responsive Tempaltes</a></li>
                            <li><a href="http://www.egrappler.com;">Free Contact Form Plugin</a></li>
                            <li><a href="http://www.egrappler.com;">Flat UI PSD</a></li>
                        </ul>
                    </div>
                    <!-- /span3 -->
                </div>
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /extra-inner --> 
</div>
<!-- /extra -->
<div class="footer">
  <div class="footer-inner">
    <div class="container">
      <div class="row">
        <div class="span12"> &copy; 2013 <a href="http://www.egrappler.com/">Bootstrap Responsive Admin Template</a>. </div>
        <!-- /span12 --> 
      </div>
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /footer-inner --> 
</div>
<!-- /footer --> 
<!-- Le javascript
================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<?php 
require_once JPATH_ADMINISTRATOR . '/components/com_binary/models/binary.php';
$binaryModel = JModelLegacy::getInstance('binary', 'BinaryModel');
$event = $binaryModel->generateCalendarEvent();

require_once JPATH_ADMINISTRATOR . '/components/com_defect/models/defect.php';
$defectModel = JModelLegacy::getInstance('defect', 'DefectModel');
$defect = $defectModel->generateChart();

?>
<script>     

        var lineChartData = {
        		labels: ["January", "February", "March", "April", "May", "June", "July"],
        	    datasets: [
        	        {
        	            label: "My First dataset",
        	            fillColor: "rgba(220,220,220,0.2)",
        	            strokeColor: "rgba(220,220,220,1)",
        	            pointColor: "rgba(220,220,220,1)",
        	            pointStrokeColor: "#fff",
        	            pointHighlightFill: "#fff",
        	            pointHighlightStroke: "rgba(220,220,220,1)",
        	            data: [65, 59, 80, 81, 56, 55, 40]
        	        },
        	        {
        	            label: "My Second dataset",
        	            fillColor: "rgba(151,187,205,0.2)",
        	            strokeColor: "rgba(151,187,205,1)",
        	            pointColor: "rgba(151,187,205,1)",
        	            pointStrokeColor: "#fff",
        	            pointHighlightFill: "#fff",
        	            pointHighlightStroke: "rgba(151,187,205,1)",
        	            data: [28, 48, 40, 19, 86, 27, 90]
        	        },
    				{
    				    fillColor: "rgba(151,187,205,0.5)",
    				    strokeColor: "rgba(151,187,205,1)",
    				    data: [28, 48, 145, 116, 197, 127, 100]
    				}
        	    ]
        };

        var myLine = new Chart(document.getElementById("area-chart").getContext("2d")).Line(lineChartData, {
            bezierCurve: false,
            scaleShowLabels: true,
            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
            
                 
        });
        var legend = myLine.generateLegend();

        console.log(legend);
        document.getElementById("area-chart").onclick = function(evt){
            var activePoints = myLine.getPointsAtEvent(evt);
            // => activePoints is an array of points on the canvas that are at the same position as the click event.
        };
        //and append it to your page somewhere
       document.getElementById("issue-chart").insertAdjacentHTML('beforeend',legend);
        
        var barChartData = {
            labels: ["January", "February", "March", "April", "May", "June", "July"],
            datasets: [
				{
				    fillColor: "rgba(220,220,220,0.5)",
				    strokeColor: "rgba(220,220,220,1)",
				    data: [65, 59, 90, 81, 56, 55, 40]
				},
				{
				    fillColor: "rgba(151,187,205,0.5)",
				    strokeColor: "rgba(151,187,205,1)",
				    data: [28, 48, 40, 19, 96, 27, 100]
				}
			]

        } ;   

        $(document).ready(function() {
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var calendar = $('#calendar').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
          },
          selectable: true,
          selectHelper: true,
          select: function(start, end, allDay) {
            var title = prompt('Event Title:');
            if (title) {
              calendar.fullCalendar('renderEvent',
                {
                  title: title,
                  start: start,
                  end: end,
                  allDay: allDay
                },
                true // make the event "stick"
              );
            }
            calendar.fullCalendar('unselect');
          },
          editable: true,
          eventRender: function (event, element) {
              var tooltip = event.tip;
              $(element).attr("data-original-title", tooltip)
              $(element).tooltip({ container: "body"})
         },
          events: <?php echo $event?>
        });
      });

        
    </script><!-- /Calendar -->
</body>
</html>
