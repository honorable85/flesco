<?php
namespace Clicalmani\Flesco\Database;

use Clicalmani\Flesco\Collection\Collection;

define('DB_QUERY_SELECT', 0);
define('DB_QUERY_INSERT', 1);
define('DB_QUERY_DELETE', 2);
define('DB_QUERY_UPDATE', 3);

class DBQuery 
{
	
	private $type;
	
	function __construct($query = null, $params = [])
	{ 
		$this->params = isset($params)? $params: [];
		
		$this->query = $query;
	}
	
	function set($param, $value) 
	{ 
		$this->params[$param] = $value;
	}

	function unset($param)
	{
		unset($this->params[$param]);
	}

	function getParam($param)
	{
		if (isset($this->params[$param])) {
			return $this->params[$param];
		}

		return null;
	}
	
	function exec()
	{ 
		
		$this->query = isset($this->params['query'])? $this->params['query']: $this->query;
		
		switch ($this->query){
			
			case DB_QUERY_SELECT:
				$obj = new Select($this->params);
				$obj->query();
				return $obj;
			
			case DB_QUERY_INSERT:
				$obj = new Insert($this->params);
				$obj->query();
				return $obj;
				
			case DB_QUERY_DELETE:
				$obj = new Delete($this->params);
				$obj->query();
				return $obj;
				
			case DB_QUERY_UPDATE:
				$obj = new Update($this->params);
				$obj->query();
				return $obj;
		}
	}

	function select($fields) 
	{
		$this->params['fields'] = $fields;
		return $this;
	}

	function delete()
	{
		$this->query = DB_QUERY_DELETE;
		return $this;
	}

	function update($options = [])
	{
		$this->query = DB_QUERY_UPDATE;

		$fields = array_keys( $options );
		$values = array_values( $options );

		$this->params['fields'] = $fields;
		$this->params['values'] = $values;

		return $this;
	}

	function insert($options = [])
	{
		$table = @ isset( $this->params['tables'][0] ) ? $this->params['tables'][0]: null;

		if ( isset( $table ) ) {
			unset($this->params['tables']);
			$this->params['table'] = $table;
		}

		$this->params['values'] = [];

		foreach ($options as $option) {
			$fields = array_keys( $option );
			$values = array_values( $option );

			$this->params['fields']   = $fields;
			$this->params['values'][] = $values;
		}

		return $this;
	}

	function where($criteria)
	{
		if ( !isset($this->params['where']) ) {
			$this->params['where'] = $criteria;
		} else {
			$this->params['where'] .= ' AND ' . $criteria;
		}
		
		return $this;
	}

	function orderBy($order_by) {
		$this->params['order_by'] = $order_by;
		return $this;
	}

	function get($fields = '*')
	{
		$this->params['fields'] = $fields;
		$result = $this->exec();
		$collection = new Collection;
		
		foreach ($result as $row) {
			$collection->add($row);
		}

		return $collection;
	}

	function join($table)
	{
		$this->params['tables'][] = $table;
		return $this;
	}

	function joinLeft($table, $parent_id, $child_id)
	{
		$joint = [
			'table'    => $table,
			'type'     => 'LEFT',
			'criteria' => ($parent_id == $child_id) ? 'USING(' . $parent_id . ')': 'ON(' . $parent_id . '=' . $child_id . ')'
		];

		if ( isset($this->params['join']) AND is_array($this->params['join'])) {
			$this->params['join'][] = $joint;
		} else {
			$this->params['join'] = [];
			$this->params['join'][] = $joint;
		}

		return $this;
	}

	function joinRight($table, $parent_id, $child_id)
	{
		$joint = [
			'table'    => $table,
			'type'     => 'RIGHT',
			'criteria' => 'ON(' . $parent_id . '=' . $child_id . ')'
		];

		if ( isset($this->params['join']) AND is_array($this->params['join'])) {
			$this->params['join'][] = $joint;
		} else {
			$this->params['join'] = [];
			$this->params['join'][] = $joint;
		}

		return $this;
	}

	function joinInner($table, $parent_id, $child_id)
	{
		$joint = [
			'table'    => $table,
			'type'     => 'INNER',
			'criteria' => 'ON(' . $parent_id . '=' . $child_id . ')'
		];

		if ( isset($this->params['join']) AND is_array($this->params['join'])) {
			$this->params['join'][] = $joint;
		} else {
			$this->params['join'] = [];
			$this->params['join'][] = $joint;
		}

		return $this;
	}
}
?>