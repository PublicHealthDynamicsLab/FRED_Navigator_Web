<?php

class FileUtilities {
	
  //cache result in memory 	
  static $dir = array();	
	
  static function get_files_in_dir($path = '.', $mask = '*', $nocache = 0){ 
    
    $sdir = NULL;
    
    if (!isset(self::$dir[$path]) || $nocache) { 
      self::$dir[$path] = scandir($path); 
    } 
    
    foreach(self::$dir[$path] as $i=>$entry) { 
      if ($entry != '.' && $entry != '..' && fnmatch($mask, $entry) ) { 
        $sdir[] = $entry; 
      } 
    } 
    
    return ($sdir); 
  } 

  static function select_files($dir, $select_name, $label = '', $curr_val = '', $mlength = 30) {
    if ($handle = opendir($dir)) {
      $mydir = '';
      if ($label != '') 
        $mydir .= '<label for="'.$select_name.'">'.$label.'</label>';
      $mydir .= '<select name="'.$select_name.'" id="'.$select_name.'">';
      
      $curr_val = (isset($_REQUEST[$select_name])) ? $_REQUEST[$select_name] : $curr_val;
      if ($curr_val == '') {
        $mydir .= '<option value="" selected="selected">...</option>';
      } else {
        $myditr .= '<option value="">...</option>';
        while (false !== ($file = readdir($handle))) {
          $files[] = $file;
        }
        closedir($handle);
        sort($files);
        $counter = 0;
        foreach ($files as $val) {
          if (is_file($dir.$val)) { // show only "real" files
            $mydir .= '<option value="'.$val.'"';
              if ($val == $curr_val) 
                $mydir .= ' selected="selected"';
              $name = (strlen($val) > $char_length) ? substr($val, 0, $mlength).'...' : $val.'';
              $mydir .= '>'.$name.'</option>';
              $counter++;
            }
        }
        $mydir .= '</select>';
      }
      if ($counter == 0) {
        $mydir = 'No files!';
      } else {
        return $mydir;
      }
    }
  }
  
  static function parse_fred_variable_file_to_array($filename) {
   
    $temp_arr = array();
    $str_data = file_get_contents($filename);
    $line_arr = explode("\n", $str_data);
    array_shift($line_arr);
    array_pop($line_arr);
    foreach($line_arr as $line) {
      $field_arr = explode(" ", $line);
      $temp_arr[] = array('day' => $field_arr[0], 'mean' => $field_arr[1], 'stdev' => $field_arr[2]);
    }

    return $temp_arr;
  }
//  static function stripAndReplace($in_string, $replacement) {
//  
//    $pattern = '/\s+/';
//    return preg_replace($pattern, $replacement, $in_string);
//  } 
//  
//  static function strip($in_string) {
//  
//    $pattern = '/\s+/';
//    $replacement = '';
//    return preg_replace($pattern, $replacement, $in_string);
//  }

}

?>