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
    
    //echo(json_encode($full_data_arr));
    
  }
  
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    
    <title>FRED Navigator Professional</title>
    <link rel="stylesheet" type="text/css" href="css/fn_style.css" />
    <link rel="stylesheet" type="text/css" href="javascript/jquery-ui-1.10.3.custom/css/redmond/jquery-ui-1.10.3.custom.css" />
    <link rel="stylesheet" type="text/css" href="css/ext-jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="css/jquery.jqplot.min.css" />   
    <style>
      body { font-family: sans-serif,arial;  }
      .wrapper.vertical { position: relative; width: 250px; height: 80%; margin: auto; }
      .wrapper.vertical > div { position: absolute; margin: auto; }
    </style>
    <style type="text/css" title="currentStyle">
      @import "javascript/DataTables/media/css/demo_page.css";
/*       @import "javascript/DataTables/media/css/jquery.dataTables.css"; */
      @import "javascript/DataTables/media/css/jquery.dataTables_themeroller.css";
    </style>
    
    <script type="text/javascript" src="javascript/jquery-2.0.3.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
    <script type="text/javascript" src="javascript/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="javascript/plugins/jqplot.json2.min.js"></script>
    <script type="text/javascript" src="javascript/plugins/jqplot.canvasTextRenderer.min.js"></script>
    <script type="text/javascript" src="javascript/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script type="text/javascript" src="javascript/DataTables/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="javascript/sprintf.js"></script>
    
    <!-- This jquery extension includes a slider with ticks. It comes from https://github.com/bseth99/jquery-ui-extensions -->
    <script src="javascript/ext-jquery-ui.js"></script>

    <script>
    var jsonUrl = "get_fred_data.php";
    var currentTabId = "tab_1"; 
    var tabData = {'tab_1': 
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


    <?php foreach($json_data['fred_experiment']['sweep_variable'] as $slider_var) { ?>
    var <?php echo 'current_' . $slider_var['var_name'] . ' = ' . $slider_var['var_value'][0]; ?>;
    <?php } ?>
 
    //Setup the tab pages
    $(function() {
      var tabOpts = {
          heightStyle: "fill", 
          beforeActivate: function(event, ui) {
            currentTabId = ui.newPanel[0].id;
            updateGraphAndGrid();
          },
          activate: function(event, ui) {        
            var tempId = ui.newPanel[0].id;         
            $("#" + tempId + "_accordion").accordion( "refresh" );
          }
      };
      $("#frednav_tabs").tabs(tabOpts);
      //$("#frednav_tabs").tabs().css({'resize':'none','min-height':'600px'});
    });

    <?php foreach(Constants::tabinfo_arr() as $tabinfo) { ?>
    $(function() {
      var accordionOpts = {
          heightStyle: "fill"
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
                //Update graph
                updateGraphAndGrid();
              }
              alert("value of slider = " + $("#slider_<?php echo $slider_var['var_name']; ?>").labeledslider("value"));
              alert("value of <?php echo 'current_' . $slider_var['var_name']; ?> = " + <?php echo 'current_' . $slider_var['var_name']; ?>);
            } 
          });
    });
    <?php } ?>

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
        $.jqplot('tab_1_graph', <?php echo $data_as_string; ?>, 
          {
            title: tabData.tab_1.title,
            series: 
              [ 
                {label:tabData.tab_1.variableLabels[0], showMarker:false}, 
                {label:tabData.tab_1.variableLabels[1], showMarker:false}, 
                {label:tabData.tab_1.variableLabels[2], showMarker:false}, 
                {label:tabData.tab_1.variableLabels[3], showMarker:false} 
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
                label:tabData.tab_1.xAxisLabel,
                pad: 0
              },
              yaxis: {
                label:tabData.tab_1.yAxisLabel,
                pad:0,
                labelRenderer:$.jqplot.CanvasAxisLabelRenderer
              }
            }
          }
        );
      });

      //Pull data from AJAX call and create a graph
      var updateGraphAndGrid = function() {
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
              $sprintfKeys .= ('current_' . $slider_var['var_name'] . '.toFixed(1)' . ($i+1 < $countVar ? ', ' : ''));
            } 
        ?>      
        //fredKey = sprintf(fredKey, current_R0, current_percent_per_day);
        fredKey = sprintf(fredKey, <?php echo $sprintfKeys; ?>);
     
        $.getJSON(  
            jsonUrl,  
            { key: fredKey, 
              tab_id: currentTabId },
            function(json) {  
              //alert("made JSON call[" + json + "]");  
            }  
        )
        .done(function(data, textStatus, jqXHR) { 
          if(data.fred_data.fred_error_data === <?php echo '"' . Constants::ERR_NONE . '"'; ?>) {
            console.log(JSON.stringify(data)); 

            //Remove the previous graph area and previous datagrid area
            var child = document.getElementById(currentTabId + '_graph');
            var parent = child.parentNode;
            parent.removeChild(child);

            var div = document.createElement('div');
            div.id = (currentTabId + '_graph');
            parent.appendChild(div);
            console.log(div); 

            child = document.getElementById(currentTabId + '_data_table_body');
            parent = child.parentNode;
            parent.removeChild(child);

            var tBody =   document.createElement('tbody'); //document.createElement('div');
            tBody.id = (currentTabId + '_data_table_body');
            parent.appendChild(tBody);
            
            var seriesArr = new Array();
            var graphDataArr = new Array();
            var countLabels = tabData[currentTabId].variableLabels.length;
            
            for(var i = 0; i < countLabels; i++) {
              var tmpPntSeries = new Array();
              seriesArr[i] = {label:tabData[currentTabId].variableLabels[i], showMarker:false};
              var tmpStr = tabData[currentTabId].variableNames[i];

              countDataPoints = data.fred_data.fred_results_data[tmpStr].length; 
              for(var j = 0; j < countDataPoints; j++) {
                //Create the table row
                var tr = $('<tr/>');
                $('#' + currentTabId + '_data_table_body').append(tr);
                tr.
                append('<td>' + tmpStr + '</td>').
                append('<td>' + data.fred_data.fred_results_data[tmpStr][j].day + '</td>').
                append('<td>' + data.fred_data.fred_results_data[tmpStr][j].mean + '</td>').
                append('<td>' + data.fred_data.fred_results_data[tmpStr][j].stdev + '</td>');
                                
                //$(document).ready( function () {
                $('#' + currentTabId + '_data_table').dataTable();
                //});
                
                var tmpPnt = new Array();
                tmpPnt[0] = parseInt(data.fred_data.fred_results_data[tmpStr][j].day);
                tmpPnt[1] = parseFloat(data.fred_data.fred_results_data[tmpStr][j].mean);
                tmpPntSeries[j] = tmpPnt;
              } 

              console.log($('#' + currentTabId + '_data_table')); 
              graphDataArr[i] = tmpPntSeries;
            }

            console.log(JSON.stringify(graphDataArr)); 
            
            $(function() {
              $.jqplot(currentTabId + '_graph', graphDataArr, 
                {
                  // Give the plot a title.
                  title: tabData[currentTabId].title,
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
                      label:tabData[currentTabId].xAxisLabel,
                      pad: 0
                    },
                    yaxis: {
                      label:tabData[currentTabId].yAxisLabel,
                      pad:0,
                      labelRenderer:$.jqplot.CanvasAxisLabelRenderer
                    }
                  }
                }
              );
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
      
    </script>
</head>

<body>

<div id="pagewidth" >

    <div id="header">
        <p>This is the Header</p>
    </div><!-- #header -->
        
    <div id="nav_menu">
        <p>This is the Menu</p>
    </div><!-- #nav_menu -->
    
    <div id="wrapper" class="clearfix">

        <div id="maincol">
            <div id="frednav_tabs">                  
        	    <div class="header-footer ui-state-default ui-corner-all"  style="padding: 3px 5px 5px; text-align: center; margin-bottom: 1ex;">
        	    <?php echo $json_data['fred_experiment']['title']; ?>
        		</div>
        		<ul style="-moz-border-radius-bottomleft: 0; -moz-border-radius-bottomright: 0;">
        		<?php foreach(Constants::tabinfo_arr() as $tabinfo) { ?>
        		    <li><a href="<?php echo '#' . $tabinfo->tab_id; ?>"><span><?php echo $tabinfo->tab_title; ?></span></a></li>
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
                                <div id="<?php echo $tabinfo->tab_id; ?>_data">
                                    <table id="<?php echo $tabinfo->tab_id; ?>_data_table" class="display">
                                        <thead>
                                            <tr>
                                                <th>Variable</th>
                                                <th>Day</th>
                                                <th>Mean</th>
                                                <th>Standard Deviation</th>
                                            </tr>
                                        </thead>
                                        <tbody id="<?php echo $tabinfo->tab_id; ?>_data_table_body">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>                                        
                                        </tbody>
                                    </table>
                                </div>                                                           
                            </div>
                        </div>
                    </div>
                <?php } ?>    
                </div>
        <!--                   <div class="header-footer ui-state-default ui-corner-all" style="padding: 3px 5px 5px; text-align: center; margin-top: 1ex;"> -->  
        <!--                         Footer div under ui-layout-content div -->
        <!--                     </div> -->
            </div>
        </div><!-- #maincol -->

        <div id="leftcol">
            <div class="wrapper vertical ">
            <?php
                $pct_pos_inc = 100 / count($json_data['fred_experiment']['sweep_variable']);
                $position = 0;
                foreach($json_data['fred_experiment']['sweep_variable'] as $slider_var) { ?>
                <div style="position:absolute; left: <?php echo $position; ?>%; top: 20px;">
                    <div id="slider_<?php echo $slider_var['var_name']; ?>" style="align:center; height:350px;"></div>
                    <div style="padding: 10px; align:center;"><?php echo $slider_var['var_display_name']; ?></div>
                </div>
            <?php
                  $position += $pct_pos_inc;
                } 
            ?>  
            </div>         
        </div><!-- #leftcol -->    

    </div><!-- #wrapper -->

    <div id="footer">
        <p>This is the Footer</p>
    </div><!-- #footer --> 
</div><!-- #pagewidth -->
</body>
</html>

<?php 
} else {
  //TODO
}
?>