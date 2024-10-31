<?php
use Searchlive\Helper;
use Searchlive\Query;


// Start of the Class
class SearchliveBuilder {

	public function __construct() 
	{
		add_action( 'wp_footer', array($this, 'searchliveOutput'));
		add_action( 'wp_enqueue_scripts', array($this, 'searchliveEnqueueScripts'));
		add_action( 'wp_ajax_searchliveAjaxLiveSearch', array($this,'searchliveAjaxLiveSearch'));	
		add_action( 'wp_ajax_nopriv_searchliveAjaxLiveSearch', array($this,'searchliveAjaxLiveSearch'));
	}
	

	public function searchliveEnqueueScripts()
	{
		$trigger = get_option('searchlive_window_trigger', '');
		
		// SCRIPTS
		wp_register_script('searchlive-core', SEARCHLIVE_ASSETS_URL . 'js/core.js',['jquery'], SEARCHLIVE_PLUGIN_NAME, true);
		wp_localize_script(
			'searchlive-core',
			'searchliveConfig',
			[
				'ajaxUrl'						=> admin_url( 'admin-ajax.php' ),
				'trigger' 						=> $trigger,
			]
		);
		wp_enqueue_script( 'searchlive-core' );
		
		
		
		// STYLES
		wp_enqueue_style( 'searchlive-core', SEARCHLIVE_ASSETS_URL . 'css/core.css', array(), SEARCHLIVE_PLUGIN_NAME, 'all' );

	}
	
	public function searchliveOutput()
	{
		$html = '';
		
		$html .= '<div class="searchlive_wrap_all">';
			$html .= '<div class="searchlive_wrap_all_in searchlive_overlay">';
				
				$html .= '<span class="searchlive_window_closer"><img src="'. SEARCHLIVE_ASSETS_URL.'/img/close.svg" class="searchlive_svg_converter" /></span>';
		
				// FILTER
				$html .= '<div class="searchlive_filter_wrap">';
					$html .= '<div class="searchlive_filter_wrap_in">';
						$html .= '<div class="searchlive_filter_elements">';
							$html .= Helper::getFilter();
						$html .= '</div>';
					$html .= '</div>';
					$html .= '<span class="searchlive_filter_closer"><img src="'. SEARCHLIVE_ASSETS_URL.'/img/close.svg" class="searchlive_svg_converter" /></span>';
				$html .= '</div>';
		
				// CONTENT
				$html .= '<div class="searchlive_content_wrap">';
		
					// FORM
					$html .= '<div class="searchlive_form_wrap">';
						$html .= '<div class="searchlive_form_wrap_in">';
							$html .= '<div class="searchlive_form_elements">';
								$html .= '<input type="text" class="searchlive_search" placeholder="'.esc_html__('Type Here', SEARCHLIVE_TEXT_DOMAIN).'" autocomplete="off">';
								$html .= '<span class="slive_form_icons">';
									$html .= '<span class="slive_form_icon icon_loader"><div class="slive_loading_icon"><div></div><div></div><div></div><div></div></div></span>';
									$html .= '<span class="slive_form_icon icon_search"><img src="'. SEARCHLIVE_ASSETS_URL.'/img/search.svg" class="searchlive_svg_converter" /></span>';
									$html .= '<span class="slive_form_icon icon_filter"><img src="'. SEARCHLIVE_ASSETS_URL.'/img/filter.svg" class="searchlive_svg_converter" /></span>';
								$html .= '</span>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';


					// RESULTS
					$html .= '<div class="searchlive_result_wrap searchlive_template_basic">';
						$html .= '<div class="searchlive_result_wrap_in">';
							$html .= '<div class="slive_no_result"><h1>'.esc_html__('Please start searching').'</h1></div>';
						$html .= '</div>';
					$html .= '</div>';
		
		
				$html .= '</div>';
		
		
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	public function searchliveAjaxLiveSearch()
	{
		global $wpdb;
		$html 				= '';
		$postsPerPage		= get_option( 'posts_per_page', 4 );
		
		$data		 		= array();
		$data['text'] 		= esc_sql(sanitize_text_field($_POST["text"]));
		$data['types'] 		= esc_sql($_POST["posttypesIDS"]);
		$data['cats'] 		= esc_sql($_POST["catsIDS"]);
		$data['basic'] 		= esc_sql($_POST["basicIDS"]);
		$data['page'] 		= esc_sql(sanitize_text_field($_POST["page"]));

		$query		= "SELECT 
							p.ID postID, 
							p.post_title postTitle, 
							p.post_content postContent, 
							p.post_excerpt postExcerpt,
							p.post_type postType  
						FROM {$wpdb->prefix}posts p";
		
		$query  	= new Query( $query, 'PostAndPage' );
		$results  	= $query->getData($data['page'], $postsPerPage, $data);
		$posts		= $results->data;
		$total		= $results->total;
		
		if(!empty($posts)){
			
			if($total >= $postsPerPage)
			{
				//$html .= $query->getPagination( 1, 'searchlive_pagination post_list');
			}
			
			$html .= '<div class="searchlive_result_list">';
			foreach($posts as $post){

				$customContent 	= Helper::customContent(wp_strip_all_tags($post->postContent), 15);
				$imgURL 		= get_the_post_thumbnail_url($post->postID, 'large');
				$imgURL 		= $imgURL?$imgURL:Helper::customImage($post->postContent);
				$hasImage 		= $imgURL?'hasImage':'';
				$permalink		= get_permalink($post->postID);
				
				$html .= '<div class="slive_article '.esc_attr($hasImage).'">';
					$html .= '<a href="'.esc_url($permalink).'" class="slive_article_link">';
						$html .= '<span class="slive_img_holder" style="background-image: url('.esc_url($imgURL).')"></span>';
						$html .= '<h1>'.esc_html($post->postTitle).'</h1>';
						$html .= '<p>'.esc_html($customContent).'</p>';
						//$html .= '<p>'.esc_html($post->postExcerpt).'</p>';
						$html .= '<span class="slive_post_type">'.esc_html($post->postType).'</span>';
					$html .= '</a>';
				$html .= '</div>';
				
			}
			$html .= '</div>';
			
			$html .= '<div class="slive_see_more"><a href="'.get_site_url().'/?s='.$data['text'].'">'.esc_html__('More Results', SEARCHLIVE_TEXT_DOMAIN).'</a></div>';
			
		}else{
			$html = '<div class="slive_no_result"><h1>'.esc_html__('No results found!').'</h1></div>';
		}
		
		$buffy = array(
			'result' 	=> $html,
			'console' 	=> $results->console
		);
		die(json_encode($buffy, JSON_UNESCAPED_SLASHES));	
	}

}
new SearchliveBuilder();