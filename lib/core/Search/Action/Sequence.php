<?php
// (c) Copyright by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_Sequence
{
	private $name;
	private $steps = [];
	private $fields = [];
	private $requiredGroup;

	function __construct($name)
	{
		$this->name = $name;
	}

	function setRequiredGroup($groupName)
	{
		$this->requiredGroup = $groupName;
	}

	function getName()
	{
		return $this->name;
	}

	function getFields()
	{
		return $this->fields;
	}

	function isAllowed(array $groups)
	{
		return empty($this->requiredGroup) || in_array($this->requiredGroup, $groups);
	}

	function addStep(Search_Action_Step $step)
	{
		$this->steps[] = $step;
		$this->fields = array_merge($this->fields, $step->getFields());
	}

	function execute(array $entry)
	{
		foreach ($this->steps as $step) {
			if (! $step->validate($entry)) {
				return false;
			}
		}

		$success = true;
		foreach ($this->steps as $step) {
			$success = $step->execute($entry) && $success;
			if (method_exists($step, 'changeObject')) {
				$entry = $step->changeObject($entry);
			}
		}

		return $success;
	}

	function requiresInput()
	{
		$params = [];

		foreach ($this->steps as $step) {
			$params[] = $step->requiresInput();
		}
		if (empty(array_filter($params))) {
			return false;
		} else {
			return json_encode($params);
		}
	}

	function getSteps()
	{
		return $this->steps;
	}
}
