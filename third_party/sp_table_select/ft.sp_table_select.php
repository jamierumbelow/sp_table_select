<?php
/**
 * SP Table Select
 *
 * Populate a dropdown with the contents of any
 * table in your database
 *
 * @author 		Jamie Rumbelow
 * @version 	1.0.0
 * @copyright 	Copyright (c) 2011 Jamie Rumbelow
 **/

class Sp_table_select_ft extends EE_Fieldtype {
	
	/* --------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------ */
	
	public $info = array(
		'name' 			=> 'SP Table Select',
		'version'		=> '1.0.0',
		'description'	=> 'Populate a dropdown with the contents of any table in your database'
	);
	public $data = array();
	public $settings = array();
	
	/* --------------------------------------------------------------
	 * GENERIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->EE =& get_instance();
	}
	
	/* --------------------------------------------------------------
	 * FIELDTYPE API
	 * ------------------------------------------------------------ */
	
	/**
	 * Display the dropdown
	 */
	public function display_field($data) {
		return form_dropdown($this->field_name, $this->_get_options(), $data);
	}
	
	/**
	 * Replaces the tag on the frontend
	 */
	public function replace_tag($data, $params = array(), $tagdata = FALSE) {
		// Get the specific value
		$label = $this->EE->db->select($this->settings['sp_table_select_label'] . ' AS `label`')
							  ->where($this->settings['sp_table_select_value'], $data)
							  ->get($this->settings['sp_table_select_table'])
							  ->row('label');
		
		// Return it
		return $label;
	}
	
	/**
	 * Allows you to get the value through a
	 * template tag
	 */
	public function replace_value($data, $params = array(), $tagdata = FALSE) {
		return $data;
	}
	
	/**
	 * Display the fieldtype settings
	 */
	public function display_settings($data) {
		// Get a list of the tables & fields
		$tables = $this->_get_all_tables_and_fields();
		$tabs   = array();
		$values = '';
		$labels = '';
		$init 	= FALSE;
		
		// Sensible defaults
		$current_table = (isset($data['sp_table_select_table'])) ? $data['sp_table_select_table'] : '';
		$current_value = (isset($data['sp_table_select_value'])) ? $data['sp_table_select_value'] : '';
		$current_label = (isset($data['sp_table_select_label'])) ? $data['sp_table_select_label'] : '';
		$current_sql   = (isset($data['sp_table_select_custom_query'])) ? $data['sp_table_select_custom_query'] : '';
		
		// Build up two dropdowns for each table
		foreach ($tables as $name => $table) {
			$tabs[$name] = $name;
			
			$values .= form_dropdown('sp_table_select_values['.$name.']', $table, $current_value, 
									 'class="sp_table_selector' . ((!$init) ? ' initial' : '') . '" data-table="'.$name.'"');
			$labels .= form_dropdown('sp_table_select_labels['.$name.']', $table, $current_label, 
									 'class="sp_table_selector' . ((!$init) ? ' initial' : '') . '" data-table="'.$name.'"');
			$init = TRUE;
		}
				
		// Build up the settings array
		$settings = array(
			array("Data source's table name <small>(exp_categories, for instance)</small>", form_dropdown('sp_table_select_table', $tabs, $current_table, 'id="sp_table_selector_switch"')),
			array("Column name to use as value <small>(cat_id, for instance)</small>", $values),
			array("Column name to use as label <small>(cat_name, for instance)</small>", $labels),
			array(array('data' => "<strong><em>or...</em></strong>", 'colspan' => 2), null),
			array("Use a custom SQL query <small>(use SELECT AS to get a <i>sp_table_value</i> and <i>sp_table_label</i>)</small>", form_textarea('sp_table_select_custom_query', $current_sql))
		);
		
		// Loop through and add the table row
		foreach ($settings as $setting) {
			$this->EE->table->add_row($setting[0], $setting[1]);
		}
		
		// Output the JS
		$this->EE->javascript->output('
			$("#sp_table_selector_switch").change(function(){
				$(".sp_table_selector").hide();
				$(".sp_table_selector[data-table=\"" + $(this).val() + "\"]").show();
			});
			
			$(".sp_table_selector:not(.initial)").hide();
		');
		
		// Do we already have a table selected?
		if ($current_table) {
			$this->EE->javascript->output('
				$(".sp_table_selector").hide();
				$(".sp_table_selector[data-table=\"' . $current_table . '\"]").show();
			');
		}
	}
	
	/**
	 * Save the fieldtype settings
	 *
	 * @todo Make a little less gross
	 */
	public function save_settings($data) {
		$settings = array(
			'sp_table_select_table' => (isset($data['sp_table_select_table'])) ? $data['sp_table_select_table'] : $_POST['sp_table_select_table'],
			'sp_table_select_custom_query' => (isset($data['sp_table_select_custom_query'])) ? $data['sp_table_select_custom_query'] : $_POST['sp_table_select_custom_query']
		);
		
		return $settings + array(
			'sp_table_select_value' => 
				(isset($data['sp_table_select_values'][$settings['sp_table_select_table']])) ? 
					$data['sp_table_select_values'][$settings['sp_table_select_table']] 
					: $_POST['sp_table_select_values'][$settings['sp_table_select_table']],
			'sp_table_select_label' => 
				(isset($data['sp_table_select_labels'][$settings['sp_table_select_table']])) ? 
					$data['sp_table_select_labels'][$settings['sp_table_select_table']] 
					: $_POST['sp_table_select_labels'][$settings['sp_table_select_table']]
		);
	}
	
	/* --------------------------------------------------------------
	 * HELPER METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Get the dropdown options
	 */
	protected function _get_options() {
		// Do we have a custom SQL query?
		if (isset($this->settings['sp_table_select_custom_query']) && $this->settings['sp_table_select_custom_query']) {
			$result = $this->EE->db->query($this->settings['sp_table_select_custom_query']);
			
			// Ensure the query is formatted correctly.
			if (isset($result->row()->sp_table_value) && isset($result->row()->sp_table_label)) {
				$result = $result->result();
			} else {
				show_error('Your custom SP Table Select query is formatted incorrectly. We can\'t find a sp_table_value and/or sp_table_label value. Please fix in the field settings.');
			}
		} else {
			// Get the data from the database
			$result = $this->EE->db->select($this->settings['sp_table_select_value'] . ' AS `sp_table_value`', TRUE)
								   ->select($this->settings['sp_table_select_label'] . ' AS `sp_table_label`', TRUE)
								   ->get($this->settings['sp_table_select_table'])
								   ->result();
		}
		
		$dropdown = array('' => '---');
		
		// Loop through and build up a dropdown safe
		// array of options
		foreach ($result as $row) {
			$dropdown[$row->sp_table_value] = $row->sp_table_label;
		}
		
		// Return them
		return $dropdown;
	}
	
	/**
	 * Get every table in the database with every
	 * column in that table
	 */
	protected function _get_all_tables_and_fields() {
		$result = $this->EE->db->query('SHOW TABLES')->result();
		$database = "Tables_in_" . $this->EE->db->database;
		$tables = array();
		
		foreach ($result as $row) {
			$name = $row->$database;
			$res = $this->EE->db->query("SELECT COLUMN_NAME FROM information_schema.`COLUMNS` WHERE TABLE_NAME = '$name' AND table_schema = '{$this->EE->db->database}'")->result();
			
			foreach ($res as $rw) {
				$tables[$name][$rw->COLUMN_NAME] = $rw->COLUMN_NAME;
			}
		}
		
		return $tables;
	}
}