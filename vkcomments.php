<?php
/*
Plugin Name: Voight Kampff Comment Test
Plugin URI: http://www.scientiaest.com/projects/wordpress-plugin-vkct/
Description: The Voight Kampff Comment Test adds additional optional or required fields to your comment form. This can be used to prevent spam, survey your users, or quiz them before they leave a comment. Allows for the additional fields to be implemented site wide, or on individual pages using the [vkct] shortcode.
Author: Joseph Martucci
Version: 0.1.1
Author URI: http://scientiaest.com/
*/

//[vkct]
class vkctShortcode{
	
	public $question, $required, $display, $answer, $answer_type, $post;
	public $question_ID = false;
	
	function __construct($opts, $post, $type){

		$options = get_option('vkct-options');
		
		extract( shortcode_atts( array(
		'question' => $options['question'],
		'required' => $options['required'],
		'display' => $options['display'], // 'above', 'below', or 'none'
		'answer' => $options['answer'],
		), $opts ) );

		$this->question = $question != $options['question'] ? sanitize_text_field($question) : $options['question'];
		$this->required = strtolower($required) === 'false' ? false : true;
		$this->display = strtolower($display) == 'above' || strtolower($display) == 'below' || strtolower($display) == 'none' ? $display : $options['display'];
		$this->answer = $answer != $options['answer'] ? sanitize_text_field($answer) : $options['answer'];
		$this->answer_type = substr_count($this->answer, '#!') > 1 ? 'dropdown' : 'text';
		$this->post = $post;
		// check if this question already exists
		if(($vkct_data = get_option( 'vkct-questions')) && isset($vkct_data[$post->ID]) ){
			foreach( $vkct_data[$post->ID] as $key => $value){
				if ($value == $this->question){
					$this->question_ID = $key;
				}
			}

			if($this->question_ID === false){
				array_push($vkct_data[$post->ID], $this->question);
				$this->question_ID = (count($vkct_data[$post->ID])) - 1; 
				update_option('vkct-questions', $vkct_data);
			}
		}

		else if($vkct_data = get_option('vkct-questions') ){
			$vkct_data[$post->ID] = array($this->question);
			update_option('vkct-questions', $vkct_data);
			$this->question_ID = 0;
		}

		else{ 
			$vkct_data = array($post->ID => array($this->question) );
			add_option( 'vkct-questions', $vkct_data); 
			$this->question_ID = 0;
		}
		
		if(($options['logged_in'] === 'Always')|| ($options['logged_in'] === 'When using shortcode' && $type == 'shortcode')) {
			add_action( 'comment_form_logged_in_after', array($this, 'vkct_fields' ) );
		}		
		add_action( 'comment_form_after_fields', array($this, 'vkct_fields' ) );
		if ($this->display != "none"){
			add_filter( 'comment_text', array($this, 'show_vkct') );
		}

	}
	


	public function vkct_fields(){ 
		$options = get_option('vkct-options'); 
		$previous_comment = false;
		$user_email = false;
	      if(!is_user_logged_in()) {
	         if(($commenter = wp_get_current_commenter()) && !empty($commenter['comment_author_email'])){
			$user_email = $commenter['comment_author_email'];        
		}
	      }
	      
	      else{
	      	get_currentuserinfo();
		global $current_user;
		$user_email = $current_user->data->user_email;
	      }
	      
	$comments = get_comments(array('author_email' => $user_email, 'post_id' => $this->post->ID, ));
		foreach($comments as $comment){
		$previous_comment = get_comment_meta( $comment->comment_ID, 'vkct-question-' . $this->question_ID, true ) ? true : false;
		if($previous_comment)
			break;
		}

		if(!$previous_comment) :
		?>

			<p class="comment-form-url">
			<label for="vkct-response-<?php echo $this->question_ID; ?>"><?php echo $this->question; $this->required ? print($options['req_text']) : ''; ?></label>
			<?php if($this->answer_type == 'text') : ?>
			<input id="vkct-response-<?php echo $this->question_ID; ?>" name="vkct-response-<?php echo $this->question_ID; ?>" type="text" /></p>
			<?php elseif($this->answer_type == 'dropdown') : 
			$dd_options = explode('#!', $this->answer); ?>
			<select id="vkct-response-<?php echo $this->question_ID; ?>" name="vkct-response-<?php echo $this->question_ID; ?>">
			<?php foreach($dd_options as $value) : ?>
				<option value="<?php echo $value;?>"><?php echo $value;?></option>
			<?php endforeach; ?>
			</select>
			<?php endif; ?>
			
			<input id="vkct-question-<?php echo $this->question_ID; ?>" name="vkct-question-<?php echo $this->question_ID; ?>" type="hidden" value="<?php echo $this->question; ?>" />
			
			<?php if($this->required) : ?>
			<input id="vkct-req-<?php echo $this->question_ID; ?>" name="vkct-req-<?php echo $this->question_ID; ?>" type="hidden" value="true" />
			<?php endif; ?>
			<?php if(!empty($this->answer) && $this->answer_type !='dropdown') : ?>
			<input id="vkct-answer-<?php echo $this->question_ID; ?>" name="vkct-answer-<?php echo $this->question_ID; ?>" type="hidden" value="<?php echo $this->answer; ?>" />
			<?php endif;
		endif;
	}
	
	function show_vkct( $text ){
		$options = get_option('vkct-options');
		if( ($vkct = get_comment_meta( get_comment_ID(), 'vkct-question-' . $this->question_ID, true )) && (strcasecmp($this->display, 'none') !== 0) ) {
			// check that the question stored matches the question asked in the shortcode. 
			$print_qa = str_replace('question', $vkct['question'], $options['qa_display']);
			$print_qa = str_replace('answer', $vkct['response'], $print_qa);
			$text = strtolower($this->display) == 'below' ? $text . $print_qa : $print_qa . $text;
		}

		return $text;
	} 
		
		
}

class vkctRun {
	public static $post;

	function admin() {
		define('VKCT_DIR', plugin_dir_path( __FILE__ ));
		require_once(VKCT_DIR .  'admin.php'); 
		vkctSettings::initialize();
	}

	function start() {
		global $post;
		static::$post = $post;
		$options = get_option('vkct-options');

		if(static::check_shortcode()){
			add_shortcode( 'vkct', array('vkctRun', 'vkct_shortcode') );
		}
		else if($options['use_default']){
			$vkct = new vkctShortcode('', $post, 'default');
		}
		else{
			die;
		}
	}
	
	function check_shortcode() {
		  
		$shortcode = false;
		 if ( stripos(static::$post->post_content, '[vkct') !== false ) {  
	        	$shortcode = true;  
	        }
	    return $shortcode;
   	 }  
   	 
	function vkct_shortcode($opts){
		$vkct = new vkctShortcode($opts, static::$post, 'shortcode');
	}
	
	function verify_required_fields($commentdata) {
		global $post;

		$vkct_data = get_option( 'vkct-questions');
		foreach( $vkct_data[$post->ID] as $key => $value){
			
			if( (!isset($_POST['vkct-response-' . $key] ) || $_POST['vkct-response-' . $key] == '' ) && isset($_POST['vkct-req-' . $key]) ) {
				wp_die( __('You forgot to answer: ' . $_POST['vkct-question-' . $key]) );
			}

			if(isset($_POST['vkct-answer-' . $key])){
			  if(! static::qa_compare($_POST['vkct-answer-' . $key], $_POST['vkct-response-' . $key])){
				wp_die( __('Robot detected! Gonna have to try harder than that to fool us!'));
				}
			}
		}

		return $commentdata;
	}

	static function qa_compare($answer, $response){
		// remove all white space and place in lower case
		$answer = strtolower(preg_replace('/\s+/', '', $answer));
		$response= strtolower(preg_replace('/\s+/', '', $response));
		// remove all punctuation  
		$answer= str_replace(array("?","!",",",";", ".",":",), "", $answer);
		$response = str_replace(array("?","!",",",";", ".",":",), "", $response);
		
		return $answer === $response;

	}
	
	function save_comment_meta_data($comment_id){
		// store the question to the comment metadata
	
		// add something to note whether the answer was an exact match or not. 
		global $post;
		$vkct_data = get_option( 'vkct-questions');
		foreach( $vkct_data[$post->ID] as $key => $value){
			if ( ( isset( $_POST['vkct-response-' . $key] ) ) && $_POST['vkct-response-' . $key] != '')
			$vkquestion = array('question' => wp_filter_nohtml_kses($_POST['vkct-question-' . $key]) , 'response' => wp_filter_nohtml_kses($_POST['vkct-response-' . $key]) ); 
			add_comment_meta( $comment_id, 'vkct-question-' . $key, $vkquestion, false );
		}	
	}
	
	function display_answers_admin($text) {
		if(!is_admin()){
			return $text;
		}
		global $post;
		$options = get_option('vkct-options');
		$vkct_data = get_option( 'vkct-questions');
		
		if(isset($vkct_data[$post->ID]) && !empty($vkct_data[$post->ID]) ){
			foreach($vkct_data[$post->ID] as $key=>$value){
				$meta = get_comment_meta( get_comment_ID(), 'vkct-question-' . $key, true );
				$text = $text . '<p><b>' . $meta['question'] . '</b> ' . $meta['response'] .'</p>';
			}
		}
		
		return $text;
		
	}
}

vkctRun::admin();
add_action('wp',array('vkctRun', 'start'));
add_filter('preprocess_comment', array('vkctRun', 'verify_required_fields'), 1 );
add_action( 'comment_post', array('vkctRun', 'save_comment_meta_data') );
add_filter( 'comment_text', array('vkctRun', 'display_answers_admin') );