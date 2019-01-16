<?php
namespace app\core;
class registry implements \ArrayAccess 
 {
    private $vars = array();
     
     function set($key, $var=0) {
        if (isset($this->vars[$key]) == true) {
            throw new Exception('Unable to set var `' . $key . '`. Already set.');
        }
        $this->vars[$key] = $var;
        return true;
    }
 

    function get($key) {
        if (isset($this->vars[$key]) == false) {
            return null;
        }
        return $this->vars[$key];
    }
 
    function get_system()
    {
      return $this->vars;
    }
    
    function remove($var) 
    {
        unset($this->vars[$key]);
    }
    
    function offsetExists($offset) 
    {
		   return isset($this->vars[$offset]);
		}


		function offsetGet($offset)
		{
        return $this->get($offset);
		}

		function offsetSet($offset, $value)
		{
       $this->set($offset, $value);
		}

		function offsetUnset($offset) 
		{
			  unset($this->vars[$offset]);
		}
}