<?php
namespace Clicalmani\Flesco\Exceptions;

class ClassNotFoundException extends \Exception {
	function __construct($class = ''){
		parent::__construct("Class $class Not Found");
	}
}
?>