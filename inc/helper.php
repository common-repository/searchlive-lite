<?php
namespace Searchlive;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }

class Helper{

	public function customContent($content, $limit)
	{
		$excerpt = explode(' ', $content, $limit);
	
		if (count($excerpt)>=$limit) {
			array_pop($excerpt);
			$excerpt = implode(" ",$excerpt);
		} 
		else{
			$excerpt = implode(" ",$excerpt);
		} 
		$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);

		return esc_html($excerpt) . '...';
	}
	
	public function customImage($content) {
		$first_img 	= '';
		$output 	= preg_match_all('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $content, $matches);
		$first_img 	= $matches[1][0];

		return $first_img;
	}
	
	public static function getFilter()
	{
		$html  = '';
		
		$html .= self::getBasic();
		$html .= self::getPostTypes();
		$html .= self::getCats();
		
		return $html;
	}
	
	
	private static function getBasic()
	{
		$html = '';
		
		$html .= '<div class="slive_filter_item slive_filter_basic">';
			$html .= '<h5 class="slive_filter_item_title">'.esc_html__('Basic', SEARCHLIVE_TEXT_DOMAIN).'</h5>';
			$html .= '<div class="slive_filter_item_content">';
				$html .= '<div class="slive_filter_item_content_header">';
					$html .= '<span></span>';
				$html .= '</div>';
				$html .= '<div class="slive_filter_item_content_footer">';
					$html .= '<div class="slive_filter_item_content_footer_in">';
						//$html .= '<div class="slive_all_select"><span class="all_selected">'.esc_html__('Deselect All', SEARCHLIVE_TEXT_DOMAIN).'<span></div>';
						$html .= '<ul class="slive_filter_dropdown_list">';


							$html .= '<li><span class="" data-id="exact_match">'.esc_html__('Exact match', SEARCHLIVE_TEXT_DOMAIN).'<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span></li>';
							$html .= '<li><span class="slive_selected" data-id="in_post_title">'.esc_html__('Search in title', SEARCHLIVE_TEXT_DOMAIN).'<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span></li>';
							$html .= '<li><span class="slive_selected" data-id="in_post_content">'.esc_html__('Search in content', SEARCHLIVE_TEXT_DOMAIN).'<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span></li>';
							$html .= '<li><span class="slive_selected" data-id="in_post_excerpt">'.esc_html__('Search in excerpt', SEARCHLIVE_TEXT_DOMAIN).'<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span></li>';


						$html .= '</ul>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
	
	private static function getPostTypes()
	{
		$html = '';
		
		$posttypes = get_post_types(['public' => true], 'objects');

		$html .= '<div class="slive_filter_item slive_filter_posttypes">';
			$html .= '<h5 class="slive_filter_item_title">'.esc_html__('Post Types', SEARCHLIVE_TEXT_DOMAIN).'</h5>';
			$html .= '<div class="slive_filter_item_content">';
				$html .= '<div class="slive_filter_item_content_header">';
					$html .= '<span>All</span>';
				$html .= '</div>';
				$html .= '<div class="slive_filter_item_content_footer">';
					$html .= '<div class="slive_filter_item_content_footer_in">';
						//$html .= '<div class="slive_all_select"><span class="all_selected">'.esc_html__('Deselect All', SEARCHLIVE_TEXT_DOMAIN).'<span></div>';
						$html .= '<ul class="slive_filter_dropdown_list">';

						foreach ( $posttypes as $posttype )
						{
							if($posttype->name !== 'attachment')
							{
								$html .= '<li><span class="slive_selected" data-id="'.$posttype->name.'">'.$posttype->name.'<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span></li>';
							}
						}

						$html .= '</ul>';
						//$html .= '<span class="searchlive_dd_closer"><img src="'. SEARCHLIVE_ASSETS_URL.'/img/close.svg" class="searchlive_svg_converter" /></span>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
	
	
	private static function getCats()
	{
		$html = '';
		
		$args = array(
			'orderby' 	=> 'name',
			'parent' 	=> 0
		);
		$categories = get_categories( $args );
			
		$html .= '<div class="slive_filter_item slive_filter_cats">';
			$html .= '<h5 class="slive_filter_item_title">'.esc_html__('Categories', SEARCHLIVE_TEXT_DOMAIN).'</h5>';
			$html .= '<div class="slive_filter_item_content">';
				$html .= '<div class="slive_filter_item_content_header">';
					$html .= '<span></span>';
				$html .= '</div>';
				$html .= '<div class="slive_filter_item_content_footer">';
					$html .= '<div class="slive_filter_item_content_footer_in">';
						//$html .= '<div class="slive_all_select"><span class="all_selected">'.esc_html__('Deselect All', SEARCHLIVE_TEXT_DOMAIN).'<span></div>';
						$html .= '<ul class="slive_filter_dropdown_list">';
						foreach ($categories as $key => $cat)
						{
							//if($key < 3){

							$catID = $cat->term_id;

							$html .= '<li><span class="slive_selected" data-id="'.$catID.'">'.$cat->name.'<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span>'.self::getSubCats($catID).'</li>';
							//}
						}
							// just need for experiment
							// $html .= '<li><span class="slive_selected" data-id="1">Uncategorized<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span></li>';

						$html .= '</ul>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
	
	private static function getSubCats($parentID)
	{
		global $wpdb;
		$query 		= "SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE parent=".$parentID;
		$results 	= $wpdb->get_results( $query, OBJECT );
        $children 	= count($results);
		$html 		= '';
		
		if($children > 0)
		{
			$args = array(
				'orderby' 	=> 'name',
				'parent' 	=> $parentID
			);
			$categories = get_categories( $args );
			
			$html .= '<ul>';
			foreach ($categories as $cat)
			{
				$catID = $cat->term_id;
				$html .= '<li><span class="slive_selected" data-id="'.$catID.'">'.$cat->name.'<img src="'. SEARCHLIVE_ASSETS_URL.'/img/check.svg" class="searchlive_svg_converter" /></span>'.self::getSubCats($catID).'</li>';
			}
			$html .= '</ul>';
		}
		
		return $html;
		
	}

}