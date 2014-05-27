<?php
// main ajax back end
class Time extends CI_Controller {
  // just returns time
  public function index()
  {
  	echo time();
  } 
}
?>