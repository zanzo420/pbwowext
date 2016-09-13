<?php
/**
 *
 * migration file for v3.0.3
 *
 * @package PBWoW Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paybas\pbwow\migrations;

class release_3_0_3 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['pbwow3_version']) && version_compare($this->config['pbwow3_version'], '3.0.3', '>=');
	}

	static public function depends_on()
	{
		return array('\paybas\pbwow\migrations\characters_3_0_2_schema');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'insert_demon_hunter'))),
		);
	}

	public function revert_schema()
	{
		return array(
			array('custom', array(array($this, 'remove_demon_hunter'))),
		);
	}

	public function insert_demon_hunter()
	{

		$sql = 'SELECT field_id, lang_id, option_id, field_type from ' . PROFILE_FIELDS_LANG_TABLE . " WHERE lang_value = 'Death Knight'";
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$newclass[] = array(
				'field_id' => $row['field_id'],
				'lang_id'  => $row['lang_id'],
				'option_id' => '12',
				'field_type' => $row['field_type'],
				'lang_value' => 'Demon Hunter');
		}

		$this->db->sql_multi_insert(PROFILE_FIELDS_LANG_TABLE, $newclass);

	}

	public function remove_demon_hunter()
	{
		$sql = "DELETE FROM " . PROFILE_FIELDS_LANG_TABLE . " WHERE option_id = '12' AND lang_value = 'Demon Hunter'";
		$this->db->sql_query($sql);
	}
}
