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

      #size1-v { height: 100px; }
      #size2-v { height: 200px; }
      #size3-v { height: 300px; }

    </style>
    
    <script type="text/javascript" src="javascript/jquery-2.0.3.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
    <script type="text/javascript" src="javascript/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="javascript/plugins/jqplot.json2.min.js"></script>
    
    <!-- This jquery extension includes a slider with ticks. It comes from https://github.com/bseth99/jquery-ui-extensions -->
    <script src="javascript/ext-jquery-ui.js"></script>

    <script>
    var jsonUrl = "get_fred_data.php";  


    
    
    //Setup the tab pages
    $(function() {
      var tabOpts = {
          heightStyle: "fill", 
          beforeActivate: function(event, ui) {
            alert("test");
            $.getJSON(  
                jsonUrl,  
                {key: "<?php echo $json_data['fred_experiment']['key'][0]?>"},    
                function(json) {  
                  alert("made JSON call[" + json + "]");  
                }  
            ); 
          },
          activate: function(event, ui) {
            var tempId = ui.newPanel.id;
            alert(JSON.stringify(ui.newPanel));
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
            stop: function(event, ui) { 
              alert("value of slider = " + $("#slider_<?php echo $slider_var['var_name']; ?>").labeledslider("value"));
            } 
          });
    });
    <?php } ?>

    <?php 
      foreach(Constants::tabinfo_arr() as $tabinfo) { 
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
        //Setup a graph for each variable on the correct tab page
        $(function() {
          $.jqplot('<?php echo $tabinfo->tab_id; ?>-graph', <?php echo $data_as_string; ?>);
        });

    <?php   

      } 
    ?>

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
                                <div id="<?php echo $tabinfo->tab_id; ?>-graph"></div>
        
        <!-- <pre class="code prettyprint brush: js"></pre> -->
                            </div>
                            <h3>Data</h3>
                            <div>
                                <p>
                                <?php echo $test_data; ?>
                                </p>
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