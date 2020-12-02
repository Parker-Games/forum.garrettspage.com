<?php
// (c) Copyright by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * EncryptionLib
 *
 * @uses TikiDb_Bridge
 */
class EncryptionLib extends TikiDb_Bridge
{
	private $encryption_keys;

	/**
	 *
	 */
	function __construct()
	{
		$this->encryption_keys = $this->table('tiki_encryption_keys');
	}

	function get_keys()
	{
 		return $this->encryption_keys->fetchAll();
	}

	function get_key($keyId)
	{
		return $this->encryption_keys->fetchFullRow(['keyId' => $keyId]);
	}

	function set_key($keyId, $data)
	{
		$existing = $this->get_key($keyId);
		if ($existing) {
			$this->encryption_keys->update($data, ['keyId' => $keyId]);
			return $keyId;
		} else {
			return $this->encryption_keys->insert($data);
		}
	}

	function delete_key($keyId)
	{
		$this->encryption_keys->delete(['keyId' => $keyId]);

		$userPreferences = $this->table('tiki_user_preferences', false);
		$userPreferences->deleteMultiple(['prefName' => $userPreferences->expr('$$ LIKE ?', ['%.sk.'.$keyId])]);

		return true;
	}

	function get_encrypted_fields()
	{
		$table = $this->table('tiki_tracker_fields');
		$result = $table->fetchAll(['fieldId', 'trackerId', 'name', 'encryptionKeyId'], ['encryptionKeyId' => $table->expr('$$ IS NOT NULL AND $$ > 0')]);
		$fields = [];
		foreach ($result as $row) {
			$fields[$row['encryptionKeyId']][] = $row;
		}
		return $fields;
	}
}
