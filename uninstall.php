<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ){
    exit();
}

function vkct_uninstall(){
	$comments = get_comments();
	$questions = get_option( 'vkct-questions');
	$max = 0;
	foreach($questions as $question){
		$max = count($question) > $max ? count($question) : $max;
	}
	
	for($i=0; $i < $max; $i++){
	
		foreach($comments as $comment) {
			delete_comment_meta($comment->comment_ID, 'vkct-question-' . $i);
		}
	
	}

	delete_option('vkct-questions');
	delete_option('vkct-options');
}

vkct_uninstall();