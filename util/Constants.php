<?php

require_once('model/TabInfo.php');
/*
 * Copyright 2013 by the University of Pittsburgh
*
* David Galloway
*/

class Constants {

  //const RESULTS_DIR = '/storage/fred_navigator_web/RESULTS/';
  const RESULTS_DIR = '/Users/ddg5/storage/FredNavigatorWeb/RESULTS/';

  const DEFAULT_TZ = 'America/New_York';

  const FEEDBACK_MAILTO = 'ddg5@pitt.edu';
  
  static $display_variable = array('S', 'E', 'I', 'R', 'AR');

  private static $tabinfo_arr = NULL;

  public static function tabinfo_arr() {
    if (self::$tabinfo_arr === NULL) {
      self::$tabinfo_arr = array(
          'tab_1' => new TabInfo('tab_1', 'SEIR', 
              'On the curves, we assume that everyone in the population is in one of four Health States: susceptible (S), exposed (E), infectious (I) or recovered (R).', array('S', 'E', 'I', 'R'), array('Susceptible', 'Exposed', 'Infectious', 'Recovered')),
          'tab_2' => new TabInfo('tab_2', 'Attack Rate', 
              'The attack rate is the percentage of the population that becomes infected over the course of an epidemic. The curves show the overall attack rate, and the symptomatic attack rate (the proportion of the population who have symptoms).', array('AR', 'ARs'), array('Attack Rate', 'Clinical Attack Rate')),
          'tab_3' => new TabInfo('tab_3', 'Incidence', 
              'The incidence is the number of new infections in the population in a given time period.  The curves show the incidence per day, and the symptomatic incidence per day. The number of symptomatic cases per day is important because it may indicate how many people may go to the doctor or to the hospital at one time.', array('C', 'Cs'), array('Incidence', 'Clinical Incidence')),
          'tab_4' => new TabInfo('tab_4', 'Prevalence', 
              'The prevalence is the total number people in the population who are infected at a given time. The curve shows the prevalence on each day of the epidemic. Prevalance is important because it may determine how many infectious people you might encounter on any given day.', array('P'), array('Prevalence'))
      );
    }
    return self::$tabinfo_arr;
  }
  
  const ERR_GFD_KEY_REQ = 'ERR_GFD_KEY_REQ';
  const ERR_GFD_TAB_REQ = 'ERR_GFD_TAB_REQ';
  const ERR_GFD_KEY_NF = 'ERR_GFD_KEY_NF';
  const ERR_GFD_TAB_NF = 'ERR_GFD_TAB_NF';
  const ERR_NONE = 'ERR_NONE';
  const DATA_NONE = 'DATA_NONE';
  
  private static $error_map = NULL;
  
  public static function error_map() {
    if (self::$error_map === NULL) {
      self::$error_map = 
        array(
          'ERR_GFD_KEY_REQ' => 'get_fred_data.php requires a key to be included with the call',
          'ERR_GFD_TAB_REQ' => 'get_fred_data.php requires a tab_id to be included with the call',
          'ERR_GFD_KEY_NF' => 'get_fred_data.php: Key [%s] was not found',
          'ERR_GFD_TAB_NF' => 'get_fred_data.php: Tab [%s] was not found'
        ); 
    }
    
    return self::$error_map;
  }
  
  private static $key_to_job_map = NULL;
  
  public static function key_to_job_map() {
    if (self::$key_to_job_map === NULL) {
      $txt_file = file_get_contents(self::RESULTS_DIR . 'KEY');
      $rows = explode("\n", $txt_file);
      //array_shift($rows);
      
      foreach($rows as $row => $data)
      {
        //get row data
        $row_data = explode(' ', $data);
        if($row_data[0] !== NULL && trim($row_data[0]) !== '') {
          self::$key_to_job_map[$row_data[0]] = $row_data[1];
        }
      }
      
    }
    return self::$key_to_job_map;
  }

  public static function getSiteBase() {
    //Live
    //return 'http://fred.publichealth.pitt.edu/FredNavigatorWeb';
    //Local
    return 'http://localhost/FredNavigatorWeb';    
  }
}

?>