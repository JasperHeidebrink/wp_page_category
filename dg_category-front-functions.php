<?php
/**
 * @package Jasper
 * @version 1.0
 */
/*
	Required by dg_category.php
	This document contains all the front-end functions for the dragonet category page link.	
*/


if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}


// -------------------------------------------------------------------------------------------- //
// ----------------------------------- START FRONT SIDE --------------------------------------- //
// get an array with the linked cats
function dgcat_get_shop_cats() {
	// get the db
	global $wpdb;

	// get the gategories
	// http://codex.wordpress.org/Function_Reference/get_categories
	$categories = get_categories(
		array(
			'parent'       => DGCAT_PARENTCAT,
			'hide_empty'   => false,
			'orderby'      => 'term_id',
			'order'        => 'ASC',
			'hierarchical' => false,
			'child_of'     => 0,
		)
	);
	//dg_show($categories);

	echo '<ul id="category">';

	foreach ( $categories as $cat ) {
		// create the list
		$catName = str_replace( array( '&amp;', ' ', '&' ), array(
			"_",
			"",
			"_",
		), strToLower( get_cat_name( $cat->cat_ID ) ) );

		echo "\t<li>
				<a href=\"/category/{$cat->slug}\" target=\"_top\">
					<img src=\"" . get_bloginfo( 'template_url' ) . "/images/icons/icon_{$catName}.png\" width=\"66\" height=\"65\" alt=\"" . get_cat_name( $cat->cat_ID ) . "\">
				</a>
			</li>\n";
	}
	echo '</ul>';
}

