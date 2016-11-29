<?php
/**
 *
 * @package PBWoW Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paybas\pbwow\migrations;

class profile_fields_3_0_3 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\paybas\pbwow\migrations\characters_3_0_2_schema');
	}

	/**
	 * new Legion data
	 * @var array
	 */
	protected $profilefields = array(
		/* WoW */
		'pb_wow_class' => array(
			'profilefield_oldname'			=> 'pbclass',
			'profilefield_database_type'	=> array('UINT', null),
			'profilefield_lang_name'		=> 'WoW character class',
			'profilefield_lang_explain'		=> '',
			'profilefield_data' 			=> array(
				'field_type'			=> 'profilefields.type.dropdown',
				'field_length'			=> '0',
				'field_minlen'			=> '0',
				'field_maxlen'			=> '12',
				'field_novalue'			=> '1',
				'field_default_value'	=> '1',
				'field_validation'		=> '',
				'field_required'		=> 0,
				'field_show_novalue'	=> 0,
				'field_show_on_reg'		=> 1,
				'field_show_on_pm'		=> 1,
				'field_show_on_vt'		=> 1,
				'field_show_on_ml'		=> 0,
				'field_show_profile'	=> 1,
				'field_hide'			=> 0,
				'field_no_view'			=> 0,
				'field_active'			=> 1,
				'field_is_contact'		=> 0,
				'field_contact_desc'	=> '',
				'field_contact_url'		=> '',
			),
			'profilefield_entries'	=> array(
				12	=> 'Demon Hunter',
			),
		),
		'pb_wow_level' => array(
			'profilefield_oldname'			=> 'pblevel',
			'profilefield_database_type'	=> array('UINT', null),
			'profilefield_lang_name'		=> 'WoW character level',
			'profilefield_lang_explain'		=> '',
			'profilefield_data' 			=> array(
				'field_type'			=> 'profilefields.type.int',
				'field_length'			=> '3',
				'field_minlen'			=> '0',
				'field_maxlen'			=> '110',
				'field_novalue'			=> '0',
				'field_default_value'	=> '',
				'field_validation'		=> '',
				'field_required'		=> 0,
				'field_show_novalue'	=> 0,
				'field_show_on_reg'		=> 1,
				'field_show_on_pm'		=> 1,
				'field_show_on_vt'		=> 1,
				'field_show_on_ml'		=> 0,
				'field_show_profile'	=> 1,
				'field_hide'			=> 0,
				'field_no_view'			=> 0,
				'field_active'			=> 1,
				'field_is_contact'		=> 0,
				'field_contact_desc'	=> '',
				'field_contact_url'		=> ''
			),
			'profilefield_entries'	=> array()
		),
	);

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'update_pbwow_fields'))),
		);
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array($this, 'remove_pbwow_fields'))),
		);
	}

	/**
	 *
	 * insert demon class and update max level to 110
	 *
	 */
	public function update_pbwow_fields()
	{
		
		foreach ($this->profilefields as $profilefield_name => $meta)
		{
			
			$sql = 'SELECT field_id FROM ' . PROFILE_FIELDS_TABLE . " WHERE  field_name = '" . $profilefield_name . "'" ;
			$result = $this->db->sql_query($sql);
			$field_id = (int) $this->db->sql_fetchfield('field_id');
			$this->db->sql_freeresult($result);

			// update profilefield_data for each meta
			$sql_ary = $meta['profilefield_data'];
			$sql_ary['field_name'] = $profilefield_name;
			$sql = 'UPDATE ' . PROFILE_FIELDS_TABLE . ' SET ' .
				$this->db->sql_build_array('UPDATE', $sql_ary) . " WHERE field_name = '" .  $profilefield_name . "'";
			$this->db->sql_query($sql);
			// Insert new profilefield_entries for each meta in profile_fields_lang
			$sql = 'SELECT lang_id FROM ' . PROFILE_LANG_TABLE . ' WHERE field_id = ' . $field_id;
			$result = $this->db->sql_query($sql);
			$lang_id = (int) $this->db->sql_fetchfield('lang_id');

			foreach ($meta['profilefield_entries'] as $option_id => $lang_value)
			{
				$sql_ary = array(
					'field_id'      => $field_id,
					'lang_id'       => $lang_id,
					'option_id'     => $option_id,
					'field_type'    => $meta['profilefield_data']['field_type'],
					'lang_value'    => $lang_value,
				);
				$sql = 'INSERT INTO ' . PROFILE_FIELDS_LANG_TABLE . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
				$this->db->sql_query($sql);
			}
		}
	}

	/**
	 * remove pbwow data
	 */
	public function remove_pbwow_fields()
	{
		foreach($this->profilefields as $profilefield_name => $meta)
		{
			// remove profilefield_entries for each meta in profile_fields_lang
			if ($meta['profilefield_data']['field_type'] == 'profilefields.type.dropdown')
			{
				foreach ($meta['profilefield_entries'] as $key => $entries)
				{
					$sql2 = 'DELETE FROM ' . PROFILE_FIELDS_LANG_TABLE . " WHERE field_type = 'profilefields.type.dropdown' and lang_value= '" . $entries . "'";
					$this->db->sql_query($sql2);
				}
			}
			
		}
		return true;
	}
}
