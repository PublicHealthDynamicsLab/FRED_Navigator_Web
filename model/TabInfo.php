<?php

/*
 * Copyright 2013 by the University of Pittsburgh
*
* David Galloway
*/

class TabInfo {

  public $tab_id = "";
  public $tab_title = "";
  public $tab_description = "";
  public $variable_arr = array();
  public $variable_description_arr = array(); 
  
  /**
   * 
   * @param string $tab_id
   * @param string $tab_title
   * @param string $tab_description
   * @param unknown $variable_arr
   * @param unknown $variable_description_arr
   */
  public function __construct($tab_id = "", $tab_title = "", $tab_description = "", $variable_arr = array(), $variable_description_arr = array()) {
    $this->tab_id = $tab_id;
    $this->tab_title = $tab_title;
    $this->tab_description = $tab_description;
    $this->variable_arr = $variable_arr;
    $this->variable_description_arr = $variable_description_arr;
  }

}

?>