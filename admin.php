<?php
class vkctSettings{
	public static function initialize(){
		add_action('admin_menu', array('vkctSettings', 'admin_pages'));
		add_action('admin_init',array('vkctSettings', 'register_settings'));	
		add_action('plugins_loaded', array('vkctSettings', 'option_setup'));
	}
	
	function admin_pages(){	
	
		add_plugins_page('Voight-Kampff Comment Test', 
			'Voight-Kampff Comment Test', 
			'edit_plugins', 
			'vkct-settings', 
			array('vkctSettings','options_page')); 
		
			
		add_settings_section('general_settings', 
				'Shortcode Defaults', 
				array('vkctSettings', 'general_settings'), 
				'vkct-gen-settings'); 
			
			add_settings_field('field-gen', 
				'Require answer', 
				array('vkctSettings', 'field_gen'), 
				'vkct-gen-settings', 
				'general_settings' 
				);

			add_settings_field('field-display', 
				'Display with comment', 
				array('vkctSettings', 'field_display'), 
				'vkct-gen-settings', 
				'general_settings' 
				);

			add_settings_field('field-question', 
				'Default Question', 
				array('vkctSettings', 'field_question'), 
				'vkct-gen-settings', 
				'general_settings' 
				);

			add_settings_field('field-answer', 
				'Default Answer', 
				array('vkctSettings', 'field_answer'), 
				'vkct-gen-settings', 
				'general_settings' 
				);

					
		add_settings_section('global_settings', 
				'Global Defaults', 
				array('vkctSettings', 'global_settings'), 
				'vkct-gen-settings'); 
				
			add_settings_field('field-use-default', 
				'Use default question', 
				array('vkctSettings', 'field_use_default'), 
				'vkct-gen-settings', 
				'global_settings' 
				);
		
			add_settings_field('field-logged-in-users', 
				'Ask logged in users to answer', 
				array('vkctSettings', 'field_logged_in'), 
				'vkct-gen-settings', 
				'global_settings' 
				);
				
			add_settings_field('field-req-text', 
				'Required text indicator', 
				array('vkctSettings', 'field_req_text'), 
				'vkct-gen-settings', 
				'global_settings' 
				);
				
			add_settings_field('field-qa-display', 
				'Question / answer display format', 
				array('vkctSettings', 'field_qa_display'), 
				'vkct-gen-settings', 
				'global_settings'
				);
	}
	
	/* Sections */
	
	
	function general_settings(){ ?>
		<p>The settings below will change the default options of the [vkct] shortcode. You can override any of these options by passing new options through the vkct shortcode.</p>
	<?php }
	
	function global_settings(){ ?>
		<p>These settings affect all of the VKCT questions posted on your site, and cannot be overriden by shortcode options.</p>
	<?php }
	
	function field_use_default(){ 
		$options = get_option('vkct-options'); ?>
		<input type="checkbox" id="option-use-default" name="vkct-options[use_default]" value="1" <?php checked( true, $options['use_default'] ); ?> />
		<label for="option-use-default" class="description">Check to add the default question to every comment section on your site. This can be useful if you want to ask one required question to prevent spam, or one optional question for feedback on all of your posts.</label>
	<?php }
	
	function field_gen(){ 
		$options = get_option('vkct-options'); ?>
		<input type="checkbox" id="option-required" name="vkct-options[required]" value="1" <?php checked( true, $options['required'] ); ?> />
		<label for="option-required" class="description">Check to require an answer to the question.</label>
	<?php }

	function field_logged_in(){ 
		$options = get_option('vkct-options');
		$logged = vkctSettings::logged_in_options(); ?>
		<select name ="vkct-options[logged_in]" id="option-logged-in">
		<?php
			foreach($logged as $value){ ?>				
				<option <?php selected($value == $options['logged_in']);?> value="<?php _e($value);?>"><?php _e($value);?></option>
				
		<?php } ?>
		</select>
		<label for="option-logged-in" class="description">Ask the question to logged in users.</label>
	<?php }


	function field_display(){ 
		$options = get_option('vkct-options');
		$display = vkctSettings::display_options(); ?>
		<select name ="vkct-options[display]" id="option-display">
		<?php
			foreach($display as $value){ ?>				
				<option <?php selected($value == $options['display']);?> value="<?php _e($value);?>"><?php _e($value);?></option>
				
		<?php } ?>
				
		</select>
		<label for="option-display">Chose whether to display the question and answer above the comment, below the comment, or not at all.</label>
	<?php }

	function field_question(){ 
		$options = get_option('vkct-options'); ?>
		<input type="text" id="option-question" name="vkct-options[question]" value="<?php esc_attr_e($options['question']); ?>" style="width: 300px;"/>
		<label for="option-question" class="description">Change the default question.</label>
	<?php }

	function field_answer(){ 
		$options = get_option('vkct-options'); ?>
		<input type="text" id="option-answer" name="vkct-options[answer]" value="<?php esc_attr_e($options['answer']); ?>" style="width: 300px;"/>
		<label for="option-answer" class="description">The expected answer to the default question. If this field is empty, any answer will be accepted.</label>
	<?php }

	function field_req_text(){ 
		$options = get_option('vkct-options'); ?>
		<input type="text" id="option-req-text" name="vkct-options[req_text]" value="<?php esc_attr_e($options['req_text']); ?>" style="width: 300px;"/>
		<label for="option-req-text" class="description">Change the required indicator on the comments form. Accepts &lt;span&gt;, &lt;small&gt;, &lt;b&gt;, and &lt;i&gt; as HTML markup.</label>
	<?php }
	
	function field_qa_display(){ 
		$options = get_option('vkct-options'); ?>
		<input type="text" id="option-qa-display" name="vkct-options[qa_display]" value="<?php esc_attr_e($options['qa_display']); ?>" style="width: 300px;"/>
		<label for="option-qa-display" class="description">Change the HTML markup of the displayed question and answer. The actual question and answer will appear where 'question' and 'answer' are in this field.</label>
	<?php }
	
	function default_options(){
	
		$options = array(  
			'logged_in' => 'When using shortcode',
			'use_default' => true,
			'required' => true,
			'question' => 'Do androids dream of electric sheep?',
			'display' => 'Below',
			'answer' => '',
			'req_text' => '<span class="required"> *</span>',
			'qa_display' => '<p><b>question</b> answer</p>',
		);
		return $options;
	}
	
	function display_options(){
		$display = array(
			'Above',
			'Below',
			'None',
		);

		return $display;
	}
	
	function logged_in_options(){
		$logged = array(
			'Never',
			'When using shortcode',
			'Always',
		);
		
		return $logged;
	}
	
	function options_page() { ?>
	     <div class="wrap">
	     <form action="options.php" method="post" enctype="multipart/form-data">
	     <?php
	     settings_fields('vkct-options');
	     do_settings_sections('vkct-gen-settings');
	     ?>
	     <input name="vkct-options[submit]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'vkct'); ?>" />
	     <input name="vkct-options[reset]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'vkct'); ?>" />
	     </form>
	     </div>
<?php }	

	function option_setup(){
		// get the current set of options from the Wordpress database with get_option. If they don't exist, write the default options to the database
		$options = get_option('vkct-options');
		$default_options = vkctSettings::default_options();
		
		// check if the options have been set, if not get the defaults
		if ($options == false){
			$options = vkctSettings::default_options();
		}
			
			// if they have been set, verify that they're the current version. If not, check if there are any new options and add them to the $options array for updating
			
				
			
		foreach($default_options as $key => $value){
			if(!isset($options[$key])){
				$options[$key] = $value;	
			}
		}
			
						
		update_option('vkct-options', $options);
	}
	
	
	function register_settings(){
		register_setting( 'vkct-options', // name of the option group
			'vkct-options', // name of the option group as saved in the database
			array('vkctSettings', 'validate') ); // callback to validation function
	}
	
	function validate($input){
		
		$default_options = vkctSettings::default_options();
		$options_input = get_option('vkct-options');

		$submit_general = ( ! empty( $input['submit']) ? true : false );
		$reset_general = ( ! empty($input['reset']) ? true : false );
	
		if($submit_general){
			$options_input['use_default'] = (isset($input['use_default']) && true == $input['use_default'] ? true : false);
			$options_input['required'] = (isset($input['required']) && true == $input['required'] ? true : false);
			$options_input['question'] = !empty($input['question']) ? sanitize_text_field($input['question']) : $options_input['question'];
			$options_input['answer'] = !empty($input['answer']) ? sanitize_text_field($input['answer']) : '';
			$options_input['req_text'] = !empty($input['req_text']) ? wp_kses($input['req_text'], array('span' => array('class' => array() ), 'small' => array('class' => array()), 'b' => array('class' => array()), 'i' => array('class' => array()), ) ) : $options_input['req_text'];
			$options_input['qa_display'] = !empty($input['qa_display']) ? wp_kses($input['qa_display'], array('span' => array('class' => array() ), 'p' => array('class' => array()), 'small' => array('class' => array()), 'b' => array('class' => array()), 'i' => array('class' => array()), ) ) : $options_input['qa_display'];


			$valid_displays = vkctSettings::display_options();
			$options_input['display'] = (in_array($input['display'], $valid_displays) ? $input['display'] : $options_input['display']);
			
			$valid_log_in = vkctSettings::logged_in_options();
			$options_input['logged_in'] = (in_array($input['logged_in'], $valid_log_in)) ? $input['logged_in'] : $options_input['logged_in'];
		}
		
		if($reset_general){
			$options_input['req_text'] = $default_options['req_text'];
			$options_input['qa_display'] = $default_options['qa_display'];
 			$options_input['logged_in'] = $default_options['logged_in'];
			$options_input['use_default'] = $default_options['use_default'];
			$options_input['required'] = $default_options['required'];
			$options_input['question'] = $default_options['question'];
			$options_input['answer'] = $default_options['answer'];
			$options_input['display'] = $default_options['display'];
		}	
		

		return $options_input;	 
	}
} // end vkctSettings


	
	