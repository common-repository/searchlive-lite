<?php
namespace Searchlive;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Query
 */
class Query {
 
	private $_limit;
	private $_page;
	private $_query;
	private $_total;
	private $_entity;
	
	
	
	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct( $query, $entity ) {
     
		global $wpdb;
		
		$this->_entity 	= $entity;
		$this->_query 	= $query;
		$rs 			= $wpdb->get_results( $query );
		$this->_total 	= $wpdb->num_rows;

	}
	

	
	/**
     * Get Data
	 * @since 1.0.0
     */
	public function getData( $page = 1, $limit = 10, $data = array(), $order='ASC') {
     	global $wpdb;
		
		$this->_limit   = $limit;
		$this->_page    = $page;


		
		// POSTS, PAGES
		if($this->_entity == 'PostAndPage'){
			
			$str 				= "";
			$text				= $data['text'];
			$exactMatch 		= in_array('exact_match', $data['basic'])?1:0;
			$searchInTitle 		= in_array('in_post_title', $data['basic'])?1:0;
			$searchInContent 	= in_array('in_post_content', $data['basic'])?1:0;
			$searchInExcerpt 	= in_array('in_post_excerpt', $data['basic'])?1:0;


			// query by joins
			if(in_array('post', $data['types']) && count($data['types']) == 1){$str .= " INNER JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id AND tr.term_taxonomy_id IN (". implode(",", array_map("intval", $data['cats'])) . ")";}

			// query by where
			$str .= " WHERE";

			// query by post status
			$str .= " p.post_status IN ('publish') AND";

			// query by post type
			$str .= " p.post_type IN ('". implode("', '", $data['types']) . "') AND";

			// query by categories
			//if(!empty($catsIDS)){str .= " tr.term_taxonomy_id IN (". implode(",", array_map("intval", $catsIDS)) . ") AND";}

			// filter by date
			//str .= " (a.start_date BETWEEN '".$startDate."' AND '".$endDate."') AND";

			// query by text
			if($exactMatch == 1)
			{
				// query by exact match
				$jarray = [];
				if($searchInTitle == 1){array_push($jarray, 'p.post_title');}
				if($searchInContent == 1){array_push($jarray, 'p.post_content');}
				if($searchInExcerpt == 1){array_push($jarray, 'p.post_excerpt');}

				$str .= " CONCAT (". implode(", ", $jarray) .") LIKE '%".$text."%' AND";
			}
			else
			{
				$text 	= str_replace(',', ' ', $text);
				$text 	= str_replace('  ', ' ', $text);
				$text 	= rtrim($text, ' ');
				$text 	= explode(' ', $text);
				$jarray = [];
				if($searchInTitle == 1){array_push($jarray, 'p.post_title');}
				if($searchInContent == 1){array_push($jarray, 'p.post_content');}
				if($searchInExcerpt == 1){array_push($jarray, 'p.post_excerpt');}

				foreach($text as $t){
					$str .= " CONCAT (". implode(", ", $jarray) .") LIKE '%".$t."%' AND";
				}
			}


			$str = rtrim($str, 'AND');

			$str .= " GROUP BY postID ORDER BY postTitle";
			
			$this->_query = $this->_query . $str;
			
			// get total posts by filer
			$rs = $wpdb->get_results( $this->_query );
			$this->_total = $wpdb->num_rows;

		}
		
		
		

		// Add LIMIT
		if ( $this->_limit != 'all' ) {
			$this->_query 	= $this->_query . " LIMIT " . ( ( $this->_page - 1 ) * $this->_limit ) . ", $this->_limit";
		} 
		
		$rs = $wpdb->get_results( $this->_query, OBJECT);

		
		$results = [];
		foreach ($rs as $row) {
			$results[]  = $row;
		}

		
		$result         	= new \stdClass();
		$result->page   	= $this->_page;
		$result->limit  	= $this->_limit;
		$result->total  	= $this->_total;
		$result->console	= $this->_query;
		$result->data   	= $results;
		
	
		return $result;
	}
 
	
	/**
     * Create Links
	 * @since 1.0.0
     */
	public function getPagination( $links, $list_class ) {
		if ( $this->_limit == 'all' ) {
			return '';
		}
		$last       = ceil( $this->_total / $this->_limit );

		$start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
		$end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

		$html       = '<ul class="' . $list_class . '" data-entity="'.$this->_entity.'">';

		$class      = ( $this->_page == 1 ) ? "disabled" : "";
		$html       .= '<li class="prev ' . $class . '"><a href="#">&laquo;</a></li>';

		if ( $start > 1 ) {
			$html   .= '<li><a href="#" data-page="1">1</a></li>';
			if(($this->_page - $links - 1) != 1){$html   .= '<li><span>...</span></li>';}
		}

		for ( $i = $start ; $i <= $end; $i++ ) {
			$class  = ( $this->_page == $i ) ? "active" : "";
			if($last > 1){
				$html   .= '<li class="' . $class . '"><a href="#" data-page="'. $i .'">' . $i . '</a></li>';
			}
			
		}

		if ( $end < $last ) {
			if(($this->_page + $links +1) != $last){$html   .= '<li><span>...</span></li>';}
			$html   .= '<li><a href="#" data-page="'. $last .'">' . $last . '</a></li>';
		}

		$class      = ( $this->_page == $last ) ? "disabled" : "";
		$html       .= '<li class="next ' . $class . '"><a href="#">&raquo;</a></li>';

		$html       .= '</ul>';

		if($this->_total != ''){return $html;}
		
	}
	
	
}

