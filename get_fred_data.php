<?php
require_once('util/Constants.php');
require_once('util/FileUtilities.php');

$key = NULL;
$tab_id = NULL;

if(isset($_GET['key'])) {
  $key = $_GET['key'];
}

if(isset($_GET['tab_id'])) {
  $tab_id = $_GET['tab_id'];
}

if(!isset($key)) {
  $error_arr = Constants::error_map();
  echo '{"fred_data":{"fred_results_data":"' . Constants::DATA_NONE . '", "fred_error_data":{"error_code":"' . Constants::ERR_GFD_KEY_REQ . '", "error_msg":"' . $error_arr[Constants::ERR_GFD_KEY_REQ] . '"}}}';
} elseif(!isset($tab_id)) {
  $error_arr = Constants::error_map();
  echo '{"fred_data":{"fred_results_data":"' . Constants::DATA_NONE . '", "fred_error_data":{"error_code":"' . Constants::ERR_GFD_TAB_REQ . '", "error_msg":"' . $error_arr[Constants::ERR_GFD_TAB_REQ] . '"}}}';
} else { 
  //Create an array of all values for the given tab_id
  $full_data_arr = array();

  $key_to_job_arr = Constants::key_to_job_map();
  $tabinfo_arr = Constants::tabinfo_arr();

  if(!array_key_exists($key, $key_to_job_arr)) {
    $error_arr = Constants::error_map();
    echo '{"fred_data":{"fred_results_data":"' . Constants::DATA_NONE . '", "fred_error_data":{"error_code":"' . Constants::ERR_GFD_KEY_NF . '", "error_msg":"' . sprintf($error_arr[Constants::ERR_GFD_KEY_NF], $key) . '"}}}';

  } elseif(!array_key_exists($tab_id, $tabinfo_arr)) {
    $error_arr = Constants::error_map();
    echo '{"fred_data":{"fred_results_data":"' . Constants::DATA_NONE . '", "fred_error_data":{"error_code":"' . Constants::ERR_GFD_TAB_NF . '", "error_msg":"' . sprintf($error_arr[Constants::ERR_GFD_TAB_NF], $tab_id) . '"}}}';
  } else {
    $error_arr = Constants::error_map();
    $temp_arr['fred_data']['fred_results_data'] = array();
    $temp_arr['fred_data']['fred_error_data'] = Constants::ERR_NONE;
    foreach($tabinfo_arr[$tab_id]->variable_arr as $fred_variable) {
      $temp_arr['fred_data']['fred_results_data'][$fred_variable] =
      FileUtilities::parse_fred_variable_file_to_array(Constants::RESULTS_DIR . 'JOB/' . $key_to_job_arr[$key]  . '/DATA/REPORTS/' . $fred_variable . '_daily-0.dat');;
    }

    echo(json_encode($temp_arr));
  }
    
} 
  
?>