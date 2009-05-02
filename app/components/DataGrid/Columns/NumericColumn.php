<?php

require_once dirname(__FILE__) . '/../DataGridColumn.php';



/**
 * Representation of numeric data grid column.
 *
 * @author     Roman Sklenář
 * @copyright  Copyright (c) 2009 Roman Sklenář
 * @example    http://nettephp.com/extras/datagrid
 * @package    Nette\Extras\DataGrid
 * @version    $Id$
 */
class NumericColumn extends DataGridColumn
{
	/** @var int */
	public $precision;
	
	
	/**
	 * Checkbox column constructor.
	 * @param  string  column's textual caption
	 * @param  string  number of digits after the decimal point
	 * @return void
	 */
	public function __construct($caption = NULL, $precision = 2)
	{
		parent::__construct($caption);
		$this->precision = $precision;
	}
	
	
	/**
	 * Formats cell's content.
	 * @param  mixed
	 * @return string
	 */
	public function formatContent($value)
	{
		if (is_array($this->replacement) && !empty($this->replacement)) {
			if (in_array($value, array_keys($this->replacement))) {
				$value = $this->replacement[$value];
			}
		}		
		$value = round($value, $this->precision);
	
		foreach ($this->formatCallback as $callback) {
			if (is_callable($callback)) {
        		$value = call_user_func($callback, $value);
			}
		}
		return $value;
	}	
	
	/**
	 * Filters data source.
	 * @param  mixed
	 * @return void
	 */
	public function applyFilter($value)
	{
		if (!$this->hasFilter()) return;
		
		$column = $this->getName();
		$cond = array();
		$operator = '=';
		
		$v = str_replace(',', '.', $value);
		if (preg_match('/^(?<operator>\>|\>\=|\<|\<\=|\=|\<\>)?(?<value>[\.|\d]+)$/', $v, $matches)) {
			if (isset($matches['operator']) && !empty($matches['operator'])) {
				$operator = $matches['operator'];
			}
			$value = $matches['value'];
		}
		$cond[] = array("[$column] $operator %s", $value);

		$datagrid = $this->getDataGrid(TRUE);
		$datagrid->dataSource->where('%and', $cond);
	}
}