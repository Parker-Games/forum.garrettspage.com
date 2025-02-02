<?php

class Search_Formatter_Transform_DynamicLoader
{
	private $source;

	function __construct(Search_Formatter_DataSource_Interface $datasource)
	{
		$this->source = $datasource;
	}

	function __invoke($entry)
	{
		return new Search_Formatter_Transform_DynamicLoaderWrapper($entry, $this->source);
	}
}

class Search_Formatter_Transform_DynamicLoaderWrapper extends ArrayObject
{
	private $source;
	private $loaded = [];

	function __construct($entry, $source)
	{
		parent::__construct($entry);
		$this->source = $source;
		$this->loaded['ignored_fields'] = true;
		if (! empty($entry['ignored_fields'])) {
			foreach ($entry['ignored_fields'] as $field) {
				$this->loaded[$field] = true;
			}
		}
	}

	function offsetGet($name)
	{
		$this->load($name);
		if (isset($this[$name])) {
			return parent::offsetGet($name);
		}
	}

	function offsetExists($name)
	{
		return parent::offsetExists($name);
	}

	private function load($name)
	{
		if (isset($this->loaded[$name])) {
			return;
		}

		$this->loaded[$name] = true;
		$data = $this->source->getData($this->getArrayCopy(), $name);

		foreach ($data as $key => $name) {
			$this[$key] = $name;
		}
	}
}
