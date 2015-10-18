<?php
/**
 JSON-LD Wordpress JSON-LD Generator
 For Homepage, Articles, Author Pages & Breadcrumbs
 @Author: Amged Osman
 @Link: https://amged.me
*/


function jsonLD(){
	//------------------------
	// Start
	//-----------------------
	$jsonLD["@context"] = "http://schema.org/";

	//--------------------------------------------
	// get post/page data
	//--------------------------------------------
	$postData = get_post_data();
	
	//--------------------------------------------
	// Get Single Tag
	// or category
	//--------------------------------------------
	if ( ( $tags = wp_get_post_tags($postData->ID) ) != null)
	{
		$articleSection = $tags[0]->name;
	} else {
		$category = get_the_category();
		$articleSection = $category[0]->cat_name;
	}
	
	//--------------------------------------------
	//common
	//--------------------------------------------
	$home_title = get_bloginfo('name');
	$home_url   = esc_url( home_url( '/' ) );
	$blog_url   = get_permalink( get_option( 'page_for_posts' ) );
	$blog_title = get_the_title( get_option('page_for_posts', true) );
	

	//--------------------------------------------
	// Is is a post?
	//--------------------------------------------
	if (is_single()) {
	 
	  $authorData = get_userdata($postData->post_author);
	  $postUrl = get_permalink();
	  $postPhoto = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
	  //$postPhoto = get_featured_url('image-1x1');

	  $jsonLD["@type"] = "Article";
	  $jsonLD["url"] = $postUrl;
	  $jsonLD["author"] = array(
		  "@type" => "Person",
		  "name" => $authorData->display_name,
		  );
		  
	  $jsonLD["headline"] = $postData->post_title;
	  
	  $jsonLD["datePublished"] = $postData->post_date;
	  
	  // get the tags if not get the category if not don't even display it!
	  if ( $articleSection !== null )
	  {
		$jsonLD["ArticleSection"] = $articleSection;
	  }
	  
	  // do we have description?
	  if ($postData->post_excerpt != '') 
	  {
		$jsonLD["description"] = $postData->post_excerpt;
	  }
	  // photo?
	  if ( $postPhoto )
	  {
		$jsonLD["image"] = $postPhoto;
	  }
	  
	  $jsonLD["Publisher"] = $home_title;
	}

	//---------------------------
	// @type: Organization
	// set it up manually!!!
	//----------------------------

	if (is_front_page()) {
	  $jsonLD["@type"] = "Organization";
	  $jsonLD["name"] = $home_title;
	  //------------------------------
	  // You can add different name
	  //-------------------------------
	  $jsonLD["alternateName"] = "أمجد عثمان";
	  $jsonLD["logo"] = "https://domain.me/cdn/images/static/logo_a-lato2_300x248.png";
	  $jsonLD["url"] = $home_url;
	  $jsonLD["sameAs"] = array(
		"https://twitter.com/username",
		"https://www.facebook.com/username",
		"https://www.linkedin.com/in/username",
		"https://instagram.com/username/",
		"https://soundcloud.com/username",
		"https://www.pinterest.com/username/",
		"https://plus.google.com/+username/",
		"https://www.youtube.com/user/username",
		
	  );

	//--------------------------------
	// for organizations only
	// they can add their contact info
	//----------------------------------
	  $jsonLD["contactPoint"] = array(
		array(
		  "@type" => "ContactPoint",
		  "telephone" => "+966 500 000 000",
		  "email" => "user@domain.me",
		  "contactType" => "sales",
		  "availableLanguage" => array(
											"English",
											"Arabic",
										  )
		)
	  );
	  //------------------------------------
	  // setup search
	  // read here
	  // https://developers.google.com/structured-data/slsb-overview
	  //-----------------------------------
	  $jsonLD["potentialAction"] = array(
		array(
		  "@type" => "SearchAction",
		  "target" => "https://domain.com/?s={searchTerm}",
		  "query-input" => "required name=searchTerm"
		)
	  );
	  
	}

	if (is_author()) {
	  //------------------------------
	  // get us some authorData
	  //------------------------------
	  $authorData = get_userdata($postData->post_author);
	  
	  //---------------------------------------
	  // set up all networks you want to pull
	  // you don't need to worry if the author
	  // filled it or not
	  // because we're performing a checkup
	  // the "networks" fields are custom field
	  // read here http://davidwalsh.name/add-profile-fields
	  // @usage
	  // $metas = array('facebook', 'googleplus');
	  //----------------------------------------
	  $metas = array('twitter', 'url', 'facebook', 'googleplus', 'linkedin', 'soundcloud', 'tumblr');
	  $sameAs = array();
	  //------------------------------------------------------
	  // append Twitter Url
	  // because davidwalsh created it for the handle only :D!
	  //-------------------------------------------------------
	  $twitterUrl =  "https://twitter.com/";
	  foreach ($metas as $meta)
	  {
		if ( get_the_author_meta($meta) != '' )
		{
			$network = get_the_author_meta($meta);
			if ($meta == 'twitter')
			{
				$network = $twitterUrl . get_the_author_meta($meta);
			}
			$sameAs[] = $network;
		}
		
	  }
	//----------------------------------
	// now add more author info
	// the "jobTitle" field is custom field
	// read here http://davidwalsh.name/add-profile-fields
	//----------------------------------
	  $jsonLD["@type"] = "Person";
	  $jsonLD["name"] = $authorData->display_name;
	  $jsonLD["email"] = $authorData->user_email;
	  if ( get_the_author_meta('title') != '')
	  {
		$jsonLD["jobTitle"] = get_the_author_meta('title');
	  }
	  $jsonLD["sameAs"] = $sameAs;
	}
	return  $jsonLD;
}


/* 
	get some post data
	not sure if we even need it
	@return array();
*/
function get_post_data() {
  global $post;
  return $post;
}

/*
// get get_featured_url 
// In your template ...
// Get src URL from avatar <img> tag (add to functions.php)
// this will also grab a default image if the post has no attachments
// @Usage get_featured_url('thumb-small');
*/
function get_featured_url($size="full"){
    if ( has_post_thumbnail()) {
     $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $size);
     return( $large_image_url[0]);
    } else {
		return get_template_directory_uri()."/images/default.png";
    }
}
