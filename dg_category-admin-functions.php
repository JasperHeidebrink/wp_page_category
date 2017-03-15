<?php
/**
 * @package Jasper
 * @version 1.0
 */
/*
	Required by dg_category.php
	This document contains all the admin functions for the dragonet category page link.	
*/


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }



// -------------------------------------------------------------------------------------------- //
// ----------------------------------- START OPTIONS ------------------------------------------ //
// the options page
function _dgcat_option_page ()
{
	// get the old var
	$sPage = esc_attr( get_option('dgcat_single_page') );
	
	// create the output
	echo "<div id=\"wrap\">";
	screen_icon();
	echo "<h2>Categorie opties</h2>
	<p>Welkom bij de categorie plugin, hier zijn de instellingen voor de categorie-pagina koppeling aan te passen.<br />
	<a href=\"edit.php?page=category-pages\">Categorie management panel</a>.</p>
	<form action=\"options.php\" method=\"post\" id=\"dgcat_option_page-form\">";
	settings_fields('dgcat_options');
	echo "<table class=\"form-table\">
	<tr valign=\"top\">
		<th scope=\"row\"><label for\"dgcat_single_page\">Meerdere pagina's koppelen aan een categorie?</label></th>
		<td><select name=\"dgcat_single_page\">
				<option value=\"false\"". ( ('false'==$sPage)? "selected=\"selected\"" : "") ."> Nee </option>
				<option value=\"true\"". ( ('true'==$sPage)? "selected=\"selected\"" : "") ."> Ja </option>
			</select>
			<small>(Het is mogelijk om meerdere pagina's te koppelen aan een categorie als je wilt).</small>
		</td>
	</tr>
	</table>
	<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" value=\"Opslaan\" /></p>
	</div>";
}
// -------------------------------------------------------------------------------------------- //
// ----------------------------------- END OPTIONS -------------------------------------------- //




// -------------------------------------------------------------------------------------------- //
// ----------------------------------- START MANAGE ------------------------------------------- //
// the manage page
function dgcat_manage_page ()
{
	// create the output
	echo "<div id=\"wrap\">";
	screen_icon();
	echo "<h2>Categorie beheer</h2>
	<p>Nog niet beschikbaar<br />";
	echo "</div>";
}
// ----------------------------------- END MANAGE --------------------------------------------- //
// -------------------------------------------------------------------------------------------- //





// -------------------------------------------------------------------------------------------- //
// ----------------------------------- START META BOX ----------------------------------------- //
// the select box at the bottom of the edit page
function dgcat_add_meta_box( $post_id )
{
	// get the db
	global $wpdb, $post;
	
	// generate the table name
	$table_name = $wpdb->prefix . DGCAT_TABLENAME;
	
	// get the current post id
	$post_id = $post->ID;
	
	// header
	echo "<p>Indien een winkel, geef aan onder welke categorieën deze winkel valt.</p>\n";
	
	// check for single page
	$compCatArr	= array();
	$sPage		= esc_attr( get_option('dgcat_single_page') );
	if ( $sPage == "true" ) {
		$tmpArr = $wpdb->get_results( "SELECT cat_ID FROM `{$table_name}` GROUP BY cat_ID", ARRAY_A );
		foreach($tmpArr as $cat)
		{
			$compCatArr[] = $cat['cat_ID'];
		}
	}
	
	// get the old data
	$pageCatArr = array();
	$tmpArr 	= $wpdb->get_results( "SELECT cat_ID FROM `{$table_name}` WHERE page_ID='{$post_id}'", ARRAY_A );
	foreach($tmpArr as $cat)
	{
		$pageCatArr[] = $cat['cat_ID'];
	}
		

	// get the gategories
	// http://codex.wordpress.org/Function_Reference/get_categories
	$categories = get_categories( array('parent'=>DGCAT_PARENTCAT, 'hide_empty'=>false, 'orderby'=>'name', 'order'=>'ASC', 'hierarchical'=>false, 'child_of'=>0 ) );
	// echo "<pre>";var_dump($categories);
	$catParent	= 0;
	echo "<div id='dgcat-scroller'>
	<ul id='dgcat-holder'>";
	foreach( $categories as $cat )
	{
		// check for the old value
		$checked = ( in_array($cat->cat_ID, $pageCatArr, true) )? " checked=\"checked\"" : "";
		
		// create the list
		echo "<li>";
		if($sPage == 'true' && in_array($cat->cat_ID, $compCatArr, true) ) {
			$title = "Bezet";
		} else {
			$title = "<input type=\"checkbox\" name=\"dgcat_bind[]\" id=\"dgcat_{$cat->cat_ID}\" value=\"{$cat->cat_ID}\" class=\"parent\" {$checked}/>".get_cat_name($cat->cat_ID);
		}
		if ( 0 == $cat->category_parent ) {
			echo "<strong>$title</strong>";
			echo "<ol>".dgcat_getSubCat( $cat->cat_ID, $pageCatArr )."</ol>";
		} else {
			echo "<ol>$title</ol>";
		}
		echo "</li>";
	}
	
	echo "</ul></div>";
}

// get the sub categories, only one level
function dgcat_getSubCat( $parentId, $pageCatArr)
{
	$categories = get_categories( array( 'parent'=>$parentId, 'hide_empty'=>false, 'orderby'=>'name', 'order'=>'ASC', 'hierarchical'=>true, 'hierarchical'=>false, 'child_of'=>0 ) );
	$catParent	= 0;
	foreach( $categories as $cat )
	{
		// check for the old value
		$checked = ( in_array($cat->cat_ID, $pageCatArr, true) )? " checked=\"checked\"" : "";
		
		// create the list
		if($sPage == 'true' && in_array($cat->cat_ID, $compCatArr, true) ) {
			$title = "Bezet";
		} else {
			$title = "<input type=\"checkbox\" name=\"dgcat_bind[]\" id=\"dgcat_{$parentId}_{$cat->cat_ID}\" value=\"{$cat->cat_ID}\"{$checked}/>".get_cat_name($cat->cat_ID);
		}
		echo "<ol>$title</ol>";
	}
}
// ----------------------------------- END META BOX ------------------------------------------- //
// -------------------------------------------------------------------------------------------- //





// -------------------------------------------------------------------------------------------- //
// ----------------------------------- START SPECIAL FUNCTIONS -------------------------------- //
// check if the table exists
function dgcat_categorylink_activate( )
{
	// get the db
	global $wpdb;
	
	// generate the table name
	$table_name = $wpdb->prefix . DGCAT_TABLENAME;

	// create the new table
	if ( $wpdb->get_var("SHOW TABLES LIKE $table_name") != $table_name ) {
		$sql = "CREATE TABLE `$table_name` (
			`ID` 		INTEGER(10) AUTO_INCREMENT ,
			`page_ID`	BIGINT(20) NOT NULL ,
			`cat_ID`	BIGINT(20) NOT NULL ,
			PRIMARY KEY (  `ID` ) ,
			INDEX (  `page_ID` ,  `cat_ID` )
			);";
			
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
		
		add_option('dgcat_categorylink_version', DGCAT_VERSION);
	}
}



// save the posted data
function dbcat_save( $page_id=0 )
{
	// is there enough info?
	if ( 0==$page_id ) return false;

	// get the db
	global $wpdb;
	
	// generate the table name
	// $table_name = $wpdb->prefix . DGCAT_TABLENAME;
	$table_name = $wpdb->prefix . 'term_relationships';
	
	// delete the old info
	// $wpdb->query("DELETE FROM `$table_name` WHERE page_ID='". $wpdb->escape($page_id) ."'");
	$wpdb->query("DELETE FROM `$table_name` WHERE object_id='". $wpdb->escape($page_id) ."'");
	

	// is there new input?
	if ( ! isset( $_POST['dgcat_bind'] ) && count($_POST['dgcat_bind']) < 1 ) return false;
	
	$vals = $_POST['dgcat_bind'];
	
	// insert data
	foreach( $vals as $cat_id )
	{
		$wpdb->query( $wpdb->prepare( "
			INSERT INTO `$table_name`	( object_id, term_taxonomy_id ) 
			VALUES ( %d, %d )", 
	        array( $page_id, $cat_id) 
		) );
		$wpdb->query("UPDATE `". $wpdb->prefix . "term_taxonomy` SET count='(SELECT id FROM `$table_name` WHERE term_taxonomy_id=$cat_id)' WHERE term_taxonomy_id='$cat_id'");
	}
	

	$wpdb->flush();
}

// ----------------------------------- END SPECIAL FUNCTIONS ---------------------------------- //
// -------------------------------------------------------------------------------------------- //