<?php
namespace Clicalmani\Flesco\App\Exceptions;

class ClassNotFoundException extends \Exception {
	function __construct($class){
		parent::__construct("Class $class can not been found");
	}
}
?>