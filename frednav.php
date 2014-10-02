<?php
require_once('util/Constants.php');
require_once('util/FileUtilities.php');

$id = NULL;

if(isset($_GET['id'])) {
  $id = $_GET['id'];
}

if(isset($id)) {
  //Get the Experiment Space file
  $str_data = file_get_contents(Constants::RESULTS_DIR . 'EXPERIMENT_SPACES/' . $id . '.json');
  $json_data = json_decode($str_data, true);
  
  //For the default output values and any additional output variables create a tab
  $full_data_arr = array();
  
  //For each sweep_variable create a slider
  //print_r(Constants::key_to_job_map());
  $key_to_job_arr = Constants::key_to_job_map();
  //count($json_data['fred_experiment']['key'])
  for($i = 0; $i < 1; $i++) {
    if(array_key_exists($json_data['fred_experiment']['key'][$i], $key_to_job_arr)) {
      //echo '<h1>' . Constants::RESULTS_DIR . 'JOB/' . Constants::key_to_job_map()[$id] . '/DATA/REPORTS/AR_daily-0.dat' . '</h1>';
      //$test_data = file_get_contents(Constants::RESULTS_DIR . 'JOB/' . Constants::key_to_job_map()[$id] . '/DATA/REPORTS/AR_daily-0.dat');
      $temp_arr = array();
      foreach(Constants::tabinfo_arr() as $tabinfo) {
        foreach($tabinfo->variable_arr as $fred_variable) {
          $temp_arr[$tabinfo->tab_id][$fred_variable] = 
              FileUtilities::parse_fred_variable_file_to_array(Constants::RESULTS_DIR . 'JOB/' . $key_to_job_arr[$json_data['fred_experiment']['key'][$i]]  . '/DATA/REPORTS/' . $fred_variable . '_daily-0.dat');
        }
      }
      
      $full_data_arr[$json_data['fred_experiment']['key'][$i]] = $temp_arr;
      
    } else {
      echo '<h1>KEY NOT FOUND: ' . $json_data['fred_experiment']['key'][$i] . '</h1>';
    }
    
  }
  
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    
    <title>FRED Navigator Web: Explore Graphs</title>
    <link rel="stylesheet" type="text/css" href="css/fn_style.css" />
    <link rel="stylesheet" type="text/css" href="javascript/jquery-ui-1.10.3.custom/css/redmond/jquery-ui-1.10.3.custom.css" />
    <link rel="stylesheet" type="text/css" href="css/ext-jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="css/jquery.jqplot.min.css" />   
    <style>        
      /*for the vertical sliders*/
      .wrapper.vertical { position:relative; margin:auto; }
      .wrapper.vertical > div { position:relative; margin:auto; }
      
      /*for the tooltip box*/
      .ui-tooltip, .arrow:after {
        border: 2px solid #76A7D1;
      }
      .ui-tooltip {
        padding: 10px 20px;
        border-radius: 20px;
        text-transform: uppercase;
        box-shadow: 0 0 7px #76A7D1;
      }
      .arrow {
        width:70px;
        height:16px;
        overflow:hidden;
        position:absolute;
        left:50%;
        margin-left:-35px;
        bottom:-16px;
      }
      .arrow.top {
        top:-16px;
        bottom:auto;
      }
      .arrow.left {
        left:80%;
      }
      .arrow:after {
        content:"";
        position:absolute;
        left:20px;
        top:-20px;
        width:25px;
        height:25px;
        box-shadow:6px 5px 9px -9px black;
        -webkit-transform:rotate(45deg);
        -moz-transform:rotate(45deg);
        -ms-transform:rotate(45deg);
        -o-transform:rotate(45deg);
        tranform:rotate(45deg);
      }
      .arrow.top:after {
        bottom:-20px;
        top:auto;
      }
    </style>
    
    <script type="text/javascript" src="javascript/jquery-2.0.3.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
    <script type="text/javascript" src="javascript/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="javascript/plugins/jqplot.canvasTextRenderer.min.js"></script>
    <script type="text/javascript" src="javascript/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script type="text/javascript" src="javascript/sprintf.js"></script>
    
    <!-- This jquery extension includes a slider with ticks. It comes from https://github.com/bseth99/jquery-ui-extensions -->
    <script src="javascript/ext-jquery-ui.js"></script>

    <script>
    var JSON_URL = 'get_fred_data.php';
    var CURRENT_TAB_ID = 'tab_1';
    var MAIN_COLUMN_HEIGHT = 0; 
    var TAB_INFO = {'tab_1': 
                    {
                      title: 'SEIR',
                      xAxisLabel: 'Day',
                      yAxisLabel: 'Number of People', 
                      variableNames: 
                        [
                          'S', 'E', 'I', 'R'
                        ], 
                      variableLabels: 
                        [
                          'Susceptible', 'Exposed', 'Infectious', 'Recovered'
                        ]
                    },
                     
                   'tab_2': 
                     {
                       title: 'Attack Rate',
                       xAxisLabel: 'Day',
                       yAxisLabel: '% Infected', 
                       variableNames: 
                         [
                           'AR', 'ARs'
                         ], 
                       variableLabels: 
                         [
                           'Attack Rate', 'Clinical Attack Rate'
                         ]
                     },
                      
                   'tab_3': 
                     {
                       title: 'Incidence',
                       xAxisLabel: 'Day',
                       yAxisLabel: 'Number of People', 
                       variableNames: 
                         [
                           'C', 'Cs'
                         ], 
                       variableLabels: 
                         [
                           'Incidence', 'Clinical Incidence'
                         ]
                     },
                      
                   'tab_4': 
                     { 
                       title: 'Prevalence',
                       xAxisLabel: 'Day',
                       yAxisLabel: 'Number of People',  
                       variableNames: 
                         [
                           'P'
                         ], 
                       variableLabels: 
                         [
                           'Prevalence'
                         ]
                     }
                  };

    $(document).ready(function() {
      MAIN_COLUMN_HEIGHT = parseInt($('#maincol').height(), 10)

      <?php foreach($json_data['fred_experiment']['sweep_variable'] as $slider_var) { ?>
      var <?php echo 'current_' . $slider_var['var_name'] . ' = ' . $slider_var['var_value'][0]; ?>;
      <?php } ?>
   
      //Setup the tab pages
      $(function() {
        var tabOpts = {
            heightStyle: 'fill', 
            beforeActivate: function(event, ui) {
              CURRENT_TAB_ID = ui.newPanel[0].id;
              var active = $('#'+ CURRENT_TAB_ID + '_accordion').accordion('option', 'active');
              if(active == 1) {
                updateData();
              } else {  
                updateGraph();
              }
            },
            activate: function(event, ui) {              
              $('#' + CURRENT_TAB_ID + '_accordion').accordion('refresh');
            }
        };
        $('#frednav_tabs').tabs(tabOpts);

        var h =  Math.floor(MAIN_COLUMN_HEIGHT * .99);
        $('#frednav_tabs').height(h);

        h = Math.floor(h * .85);
        $("#tab_1").height(h);
        $("#tab_2").height(h);
        $("#tab_3").height(h);
        $("#tab_4").height(h);

      });

      <?php foreach(Constants::tabinfo_arr() as $tabinfo) { ?>
      $(function() {
        var accordionOpts = {
            heightStyle: 'fill',
            activate: function(event, ui) {
              var active = $('#'+ CURRENT_TAB_ID + '_accordion').accordion('option', 'active');
              if(active == 1) {
                updateData();
              } else {  
                updateGraph();
              }
            }
        };

        $("#<?php echo $tabinfo->tab_id; ?>_accordion").accordion(accordionOpts);
      });
      <?php } ?>

      
      <?php foreach($json_data['fred_experiment']['sweep_variable'] as $slider_var) {?>
      $(function() {
        $("#slider_<?php echo $slider_var['var_name']; ?>").labeledslider(
            { min: <?php echo $slider_var['var_value'][0]; ?>, 
              max: <?php echo $slider_var['var_value'][(count($slider_var['var_value']) - 1)]; ?>, 
              step: <?php echo $slider_var['var_value'][1] - $slider_var['var_value'][0]; ?>, 
              orientation: "vertical",
              numberPrecision: <?php echo $slider_var['var_decimal_precision']; ?>,
              stop: function(event, ui) { 
                if($("#slider_<?php echo $slider_var['var_name']; ?>").labeledslider("value") != <?php echo 'current_' . $slider_var['var_name']; ?>) {
                  <?php echo 'current_' . $slider_var['var_name']; ?> = $("#slider_<?php echo $slider_var['var_name']; ?>").labeledslider("value") ;
                  var active = $('#'+ CURRENT_TAB_ID + '_accordion').accordion('option', 'active');
                  if(active == 1) {
                    updateData();
                  } else {  
                    updateGraph();
                  }
                }
              } 
            });
      });
      <?php } ?>

        $(function() {
          $(document).tooltip({
            position: {
              my: 'center bottom-20',
              at: 'center top',
              using: function(position, feedback) {
                $(this).css(position);
                $('<div>') 
                  .addClass('arrow')
                  .addClass(feedback.vertical)
                  .addClass(feedback.horizontal)
                  .appendTo(this);
                  
              }
            },
            show: { effect: "fade" },
            hide: { effect: "fade"}
          });
        });
      

        //Pull data from AJAX call and create a graph
        var updateGraph = function() {
          //Use current values for tab and sliders to know what data to retrieve        
          <?php 
              $countVar = count($json_data['fred_experiment']['sweep_variable']);
              $fred_key = '';
              for($i = 0; $i < $countVar; $i++) { 
                $fred_key .= ($json_data['fred_experiment']['sweep_variable'][$i]['var_name'] . '=%s' . ($i+1 < $countVar ? '-' : ''));
              } 
          ?>
          
          var fredKey = "<?php echo $fred_key; ?>";
          <?php 
              $sprintfKeys = '';
              $countVar = count($json_data['fred_experiment']['sweep_variable']);
              for($i = 0; $i < $countVar; $i++) {
                $slider_var = $json_data['fred_experiment']['sweep_variable'][$i];
                $sprintfKeys .= ('current_' . $slider_var['var_name'] . '.toFixed(' . $json_data['fred_experiment']['sweep_variable'][$i]['var_decimal_precision'] . ')' . ($i+1 < $countVar ? ', ' : ''));
              } 
          ?>      
          //fredKey = sprintf(fredKey, current_R0, current_percent_per_day);
          fredKey = sprintf(fredKey, <?php echo $sprintfKeys; ?>);
       
          $.getJSON(  
              JSON_URL,  
              { key: fredKey, 
                tab_id: CURRENT_TAB_ID },
              function(json) {  
                
              }  
          )
          .done(function(data, textStatus, jqXHR) { 
            if(data.fred_data.fred_error_data === <?php echo '"' . Constants::ERR_NONE . '"'; ?>) {
              console.log(JSON.stringify(data)); 

              //Remove the previous graph area and previous data area
              var child = document.getElementById(CURRENT_TAB_ID + '_graph');
              var parent = child.parentNode;
              parent.removeChild(child);

              var div = document.createElement('div');
              div.id = (CURRENT_TAB_ID + '_graph');
              parent.appendChild(div);
              
              var seriesArr = new Array();
              var graphDataArr = new Array();
              var countLabels = TAB_INFO[CURRENT_TAB_ID].variableLabels.length;
              
              for(var i = 0; i < countLabels; i++) {
                var tmpPntSeries = new Array();
                seriesArr[i] = {label:TAB_INFO[CURRENT_TAB_ID].variableLabels[i], showMarker:false};
                var tmpStr = TAB_INFO[CURRENT_TAB_ID].variableNames[i];

                countDataPoints = data.fred_data.fred_results_data[tmpStr].length; 
                for(var j = 0; j < countDataPoints; j++) {
                                                
                  var tmpPnt = new Array();
                  tmpPnt[0] = parseInt(data.fred_data.fred_results_data[tmpStr][j].day);
                  tmpPnt[1] = parseFloat(data.fred_data.fred_results_data[tmpStr][j].mean);
                  tmpPntSeries[j] = tmpPnt;
                } 
                graphDataArr[i] = tmpPntSeries;
              }

              console.log(JSON.stringify(graphDataArr)); 
              
              $(function() {
                var plot1 = $.jqplot(CURRENT_TAB_ID + '_graph', graphDataArr, 
                  {
                    // Give the plot a title.
                    title: TAB_INFO[CURRENT_TAB_ID].title,
                    series: seriesArr,
                    legend: 
                    {
                      show: true,
                      location: 'ne',
                      xoffset: 12,
                      yoffset: 12
                    },
                    axes: {
                      xaxis: {
                        label:TAB_INFO[CURRENT_TAB_ID].xAxisLabel,
                        pad: 0
                      },
                      yaxis: {
                        label:TAB_INFO[CURRENT_TAB_ID].yAxisLabel,
                        pad:0,
                        labelRenderer:$.jqplot.CanvasAxisLabelRenderer
                      }
                    }
                  }
                );

                var h = parseInt($('.jqplot-title').height(), 10) + parseInt($('.jqplot-xaxis').height(), 10) + parseInt($('#' + CURRENT_TAB_ID).height(), 10);
                h = Math.floor(h * .75);
                $('#' + CURRENT_TAB_ID + '_graph').height(h);
                plot1.replot();
                        
              });                        
                             
            } else {           
              alert(data.fred_data.fred_error_data.error_code + ": " + data.fred_data.fred_error_data.error_msg);
              console.log(JSON.stringify(data));       
            }

          })
          .fail(
            function(jqXHR, textStatus, errorThrown) { 
              console.log(jqXHR);
              console.log(errorThrown); 
            })
          .always(function() { console.log( "complete" ); }); 
          
        };

        //Pull data from AJAX call and put it in the Data panel
        var updateData = function() {
          //Use current values for tab and sliders to know what data to retrieve        
          <?php 
              $countVar = count($json_data['fred_experiment']['sweep_variable']);
              $fred_key = '';
              for($i = 0; $i < $countVar; $i++) { 
                $fred_key .= ($json_data['fred_experiment']['sweep_variable'][$i]['var_name'] . '=%s' . ($i+1 < $countVar ? '-' : ''));
              } 
          ?>
          
          var fredKey = "<?php echo $fred_key; ?>";
          <?php 
              $sprintfKeys = '';
              $countVar = count($json_data['fred_experiment']['sweep_variable']);
              for($i = 0; $i < $countVar; $i++) {
                $slider_var = $json_data['fred_experiment']['sweep_variable'][$i];
                $sprintfKeys .= ('current_' . $slider_var['var_name'] . '.toFixed(' . $json_data['fred_experiment']['sweep_variable'][$i]['var_decimal_precision'] . ')' . ($i+1 < $countVar ? ', ' : ''));
              } 
          ?>      
          //fredKey = sprintf(fredKey, current_R0, current_percent_per_day);
          fredKey = sprintf(fredKey, <?php echo $sprintfKeys; ?>);
       
          $.getJSON(  
              JSON_URL,  
              { key: fredKey, 
                tab_id: CURRENT_TAB_ID },
              function(json) {  
                //alert("made JSON call[" + json + "]");  
              }  
          )
          .done(function(data, textStatus, jqXHR) { 
            if(data.fred_data.fred_error_data === <?php echo '"' . Constants::ERR_NONE . '"'; ?>) {
              console.log(JSON.stringify(data)); 

              //Remove the previous data area
              var child = document.getElementById(CURRENT_TAB_ID + '_data');
              var parent = child.parentNode;
              parent.removeChild(child);

              var pre = document.createElement('pre');
              pre.id = (CURRENT_TAB_ID + '_data');
              
              var jsonPretty = JSON.stringify(data, null, 2);  
              var textNode = document.createTextNode(jsonPretty);
              pre.appendChild(textNode);
              parent.appendChild(pre);
                                                                         
            } else {           
              alert(data.fred_data.fred_error_data.error_code + ": " + data.fred_data.fred_error_data.error_msg);
              console.log(JSON.stringify(data));       
            }

          })
          .fail(
            function(jqXHR, textStatus, errorThrown) { 
              console.log(jqXHR);
              console.log(errorThrown); 
            })
          .always(function() { console.log( "complete" ); }); 
          
        };

    <?php
        //Only populate the first tab
        $tabinfoArr = Constants::tabinfo_arr();
        $tabinfo = $tabinfoArr['tab_1']; 
        $data_as_string = '[';
        $count = 0;
        foreach($tabinfo->variable_arr as $fred_variable) { 
          $data_as_string .= '[';
          for($i = 0; $i < count($full_data_arr[$json_data['fred_experiment']['key'][0]][$tabinfo->tab_id][$fred_variable]); $i++) {
            $data_as_string .= ('[' . $full_data_arr[$json_data['fred_experiment']['key'][0]][$tabinfo->tab_id][$fred_variable][$i]['day'] . ', ' . 
                $full_data_arr[$json_data['fred_experiment']['key'][0]][$tabinfo->tab_id][$fred_variable][$i]['mean'] . ']');
            if($i + 1 < count($full_data_arr[$json_data['fred_experiment']['key'][0]][$tabinfo->tab_id][$fred_variable]))
            { 
              $data_as_string .= ', ';
            }
          }
              
          $count++;
          if($count < count($tabinfo->variable_arr)) {
            $data_as_string .= '], ';
          } else {
            $data_as_string .= ']';
          }
        }
        $data_as_string .= ']';

      ?>

        //Create a graph on the intial tab page
        $(function() {
          var plot1 = $.jqplot('tab_1_graph', <?php echo $data_as_string; ?>, 
            {
              title: TAB_INFO.tab_1.title,
              series: 
                [ 
                  {label:TAB_INFO.tab_1.variableLabels[0], showMarker:false}, 
                  {label:TAB_INFO.tab_1.variableLabels[1], showMarker:false}, 
                  {label:TAB_INFO.tab_1.variableLabels[2], showMarker:false}, 
                  {label:TAB_INFO.tab_1.variableLabels[3], showMarker:false} 
                ],
              legend: 
                {
                  show: true,
                  location: 'ne',     // compass direction, nw, n, ne, e, se, s, sw, w.
                  xoffset: 12,        // pixel offset of the legend box from the x (or x2) axis.
                  yoffset: 12        // pixel offset of the legend box from the y (or y2) axis.
                },
              axes: {
                xaxis: {
                  label:TAB_INFO.tab_1.xAxisLabel,
                  pad: 0
                },
                yaxis: {
                  label:TAB_INFO.tab_1.yAxisLabel,
                  pad:0,
                  labelRenderer:$.jqplot.CanvasAxisLabelRenderer
                }
              }
            }
          );

          var h = parseInt($('.jqplot-title').height(), 10) + parseInt($('.jqplot-xaxis').height(), 10) + parseInt($('#' + CURRENT_TAB_ID).height(), 10);
          h = Math.floor(h * .75);
          $('#' + CURRENT_TAB_ID + '_graph').height(h);
          plot1.replot();
          
        }); 
        
    });


     
      
    </script>
</head>

<body>

<div id="pagewidth">

    <!-- This is the Pitt Banner. The right-hand links are listed at the bottom of the HTML page. -->
    <!-- ======================================================================================== -->
    <div id="pitt-header" class="white">
        <a href="http://www.pitt.edu/" title="University of Pittsburgh" id="p-link">University of Pittsburgh</a>
    </div><!-- end pitt-header -->

    <div id="nav_menu">
        <div id="fred-links">
	        <p>
	            <a href="<?php echo Constants::getSiteBase(); ?>">Home</a> &nbsp;&nbsp;|&nbsp;&nbsp;
	            <a href="http://fred.publichealth.pitt.edu">FRED Home</a> &nbsp;&nbsp;|&nbsp;&nbsp;
	            <a href="<?php echo Constants::getSiteBase() . '/contact_us.php'?>">Contact Us</a>
            </p>
        </div>
    </div><!-- #nav_menu -->
    
    <div id="wrapper" class="clearfix">

        <div id="maincol">
            <div id="frednav_tabs">                  
        	    <div title="<?php echo $json_data['fred_experiment']['info']; ?>" class="header-footer ui-state-default ui-corner-all"  style="padding: 3px 5px 5px; text-align: center; margin-bottom: 1ex;">
        	    <?php echo $json_data['fred_experiment']['title']; ?>
        		</div>
        		<ul style="-moz-border-radius-bottomleft: 0; -moz-border-radius-bottomright: 0;">
        		<?php foreach(Constants::tabinfo_arr() as $tabinfo) { ?>
        		    <li title="<?php echo $tabinfo->tab_description; ?>"><a href="<?php echo '#' . $tabinfo->tab_id; ?>"><span><?php echo $tabinfo->tab_title; ?></span></a></li>
        		<?php } ?>    
                </ul>
                <div class="ui-layout-content ui-widget-content ui-corner-bottom" style="border-top: 0; padding-bottom: 1em;">
                <?php foreach(Constants::tabinfo_arr() as $tabinfo) { ?>
                    <div id="<?php echo $tabinfo->tab_id; ?>">
                        <div id="<?php echo $tabinfo->tab_id; ?>_accordion">
                            <h3>Plot</h3>
                            <div>
                                <div id="<?php echo $tabinfo->tab_id; ?>_graph"></div>
                            </div>
                            <h3>Data</h3>
                            <div>
                                <pre id="<?php echo $tabinfo->tab_id; ?>_data"></pre>                                                         
                            </div>
                        </div>
                    </div>
                <?php } ?>    
                </div>
            </div>
        </div><!-- #maincol -->

        <div id="leftcol">
            <table style="height:500px;">
                <tr>
                    <td colspan="<?php echo count($json_data['fred_experiment']['sweep_variable']); ?>">Adjustable Variables</td>
                </tr>
                <tr> 
                <?php      
                  foreach($json_data['fred_experiment']['sweep_variable'] as $slider_var) { 
                ?>
                    <td style="height:425px;">
                        <div class="wrapper vertical ">
                            <div id="slider_<?php echo $slider_var['var_name']; ?>" style="height:425px;"></div>
                        </div>
                    </td>     
                <?php
                  } 
                ?>
                </tr>
                <tr>
                <?php      
                  foreach($json_data['fred_experiment']['sweep_variable'] as $slider_var) { 
                ?>
                    <td>
                         <?php echo $slider_var['var_display_name']; ?>
                    </td>
                <?php
                  } 
                ?>
                </tr>  
            </table>   
        </div><!-- #leftcol -->    

    </div><!-- #wrapper -->

    <div id="footer">
        <div>
            <img src="image/fredNav-pro.png" alt="FRED Navigator: Professional" height="100" width="220" style="position:absolute; left:.5em; top:1.2em;" />
            <img src="image/midas_logo_text.jpg" alt="MIDAS" height="100" width="80" style="position:absolute; left:49%; top:.5em;" />
            <img src="image/PPH_Mark_Web-med.png" alt="Pitt Public Health" height="100" width="466" style="position:absolute; right:.5em; top:.8em;" />
        </div>
        <div style="position:absolute; left:42%; bottom:.4em;">
            <span class="grey small">&copy; 2013 <a href="http://www.phdl.pitt.edu" target="_blank" alt="Public Health Dynamics Laboratory" 
               title="Public Health Dynamics Laboratory">Public Health Dynamics Laboratory</a>, University of Pittsburgh</span>
        </div>
    </div><!-- #footer --> 
    
    <!-- PITT BANNER RIGHT-HAND LINKS -->
    <!-- ======================================================================================== -->
    <ul id="pitt-links">
	    <li id="p-home"><a href="http://www.as.pitt.edu/">Arts and sciences</a> | <a href="http://www.pitt.edu/">Pitt Home</a> | <a href="http://www.pitt.edu/findpeople.html" title="University of Pittsburgh Directory">Find People</a></li>
    </ul>
    
</div><!-- #pagewidth -->
</body>
</html>

<?php 
} else {
  //TODO
}
?>