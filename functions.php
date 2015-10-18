<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package AME2016
 */

if ( ! function_exists( 'build_jsonld' ) ) :
/*
* build json-ld for SEO
*/
function build_jsonld($return=false){
	require_once(get_template_directory() . '/jsonld.php');
	$output = jsonLD();  
	if ($return)
	{
		return json_encode($output);
	}
	else
	{
		echo '<script type="application/ld+json">' . json_encode($output) . '</script>';
	}
}
endif;
add_action( 'wp_head', 'build_jsonld' );

if ( ! function_exists( 'build_jsonldBreadcrumb' ) ) :

/** 
	build json-ld for SEO items list
	https://builtvisible.com/implementing-json-ld-wordpress/
 **/
function build_jsonldBreadcrumb(){
	$jsonLD["@context"] = "http://schema.org/";
	$jsonLD["@type"] = "BreadcrumbList";
	//---------------------------------
	// pre-define some variables
	//---------------------------------
	$home_title = get_bloginfo('name');
	$home_url = esc_url( home_url( '/' ) );
	$blog_url = get_permalink( get_option( 'page_for_posts' ) );
	$blog_title = get_the_title( get_option('page_for_posts', true) );
	$url = $url ? $url : get_permalink();
	$title = $title ? $title : get_the_title();
	$itemList = array();

	//----------------------------------
	// Output
	//-----------------------------------
	 $itemList[] = array(
						  "@type" => "ListItem",
						  "position" => 1,
						  "item" => array('@id' => $home_url ,'name' => $home_title),
					  );
	//----------------------------------------------
	// figure out the next number
	// <meta property="position" content="{NUM}">
	//----------------------------------------------
	$next = 2;
	if ( !is_page() )
	{
		//-------------------------------------
		// we have a blog "post" 
		// so we added the blog url (not home)
		// so next is 3 not 2!
		//--------------------------------------
		$itemList[] = array(
						  "@type" => "ListItem",
						  "position" => 2,
						  "item" => array('@id' => $blog_url ,'name' => $blog_title),
					  );
		$next = 3;
	}
	
	$itemList[] = array(
						  "@type" => "ListItem",
						  "position" => $next,
						  "item" => array('@id' => $url,'name' => $title),
					  );
					  
	
	$jsonLD["itemListElement"] = $itemList;
	/*echo '<pre>';
	print_r(json_encode($jsonLD));
	echo '</pre>';*/
	if ( (is_page() OR is_single()) AND (! is_front_page()) ) {
	echo '<script type="application/ld+json">';
	echo json_encode($jsonLD);
	echo '</script>';
	}

}
endif;
add_action( 'wp_head', 'build_jsonldBreadcrumb' );

