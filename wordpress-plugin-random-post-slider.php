<?php
/*
Plugin Name: Wordpress plugin random post slider
Plugin URI: http://www.gopiplus.com/work/2011/05/28/wordpress-plugin-random-post-slider/
Description: Wordpress plugin random post slider create a post slider on the wordpress website.
Author: Gopi Ramasamy
Version: 9.5
Author URI: http://www.gopiplus.com/work/
Donate link: http://www.gopiplus.com/work/2011/05/28/wordpress-plugin-random-post-slider/
Tags: wordpress, plugin, random, post, slider
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

global $wpdb, $wp_version;

function gopiplushome() 
{
	global $wpdb;
	$displaydate = get_option('gopiplus_displaydate');
	$displaycategory = get_option('gopiplus_displaycategory');
	$displaycomment = get_option('gopiplus_displaycomment');
	$displayimage = get_option('gopiplus_displayimage');
	$displaytag = get_option('gopiplus_displaytag');
	$displaydesc = get_option('gopiplus_displaydesc');
	$displayreadmore = get_option('gopiplus_displayreadmore');
	$qp_showposts = get_option('gopiplus_query_posts_showposts');
	$qp_orderby = get_option('gopiplus_query_posts_orderby');
	$qp_order = get_option('gopiplus_query_posts_order');
	$qp_category = get_option('gopiplus_query_posts_category');
	$slider = get_option('gopiplus_query_slider');
	
	if(!is_numeric($displaydesc)) { $displaydesc = 300; } 
	if(!is_numeric($qp_showposts)) { $qp_showposts = 3; } 
	
	$myfilter = "";
	
	if($qp_showposts <> ""){ $myfilter = "showposts=".$qp_showposts; }
	if($qp_orderby <> ""){ $myfilter = $myfilter."&orderby=".$qp_orderby; }
	if($qp_order <> ""){ $myfilter = $myfilter."&order=".$qp_order; }
	if($qp_category <> ""){ $myfilter = $myfilter."&cat=".$qp_category; }
	
	if($slider == ""){ $slider = "scrollLeft"; }
	//echo $myfilter;
	?>
	<!-- begin gopiplushome -->
	<div id="gopiplushome">
	<?php
	$sSql = query_posts($myfilter);
	if ( ! empty($sSql) ) 
	{
		foreach ( $sSql as $sSql ) 
		{ 
			$post_title = $sSql->post_title;
			$post_link =  get_permalink($sSql->ID);
			$post_content = $sSql->post_content;
			$post_thumbnail = gopiplus_attachment_image_filter($sSql->ID, 'thumbnail', 'alt="' . $sSql->post_title . '"');
			$posttags = get_the_tags($sSql->ID);
			if ($posttags) 
			{
				$t= 1;
				foreach($posttags as $tag) 
				{
					if( $t == 1 )
					{
						$post_tag = "Tag: ".  $tag->slug;;
					}
					else
					{
						$post_tag = $post_tag  . ", " . $tag->slug;	
					}
					$t = $t+1;
				}
			}
			?>
			<div class="post">
            <h2><a href="<?php echo $post_link; ?>"><?php echo $post_title; ?></a></h2>
            <p class="details"><?php if($displaydate == "YES") { the_time('F j, Y'); ?> | <?php } ?><?php if($displaycategory == "YES") { ?>  <?php the_category(', '); ?> | <?php } ?> <?php if($displaycomment == "YES") { ?>  <?php comments_popup_link('No Comments', '1 Comment', '% Comments'); } ?></p>
            <?php 
			if ($displayimage == "YES") 
			{ 
				if($post_thumbnail <> "")
				{
					?>
					<div class="thumb"><a href="<?php the_permalink(); ?>"><?php echo $post_thumbnail; ?></a></div>
					<?php
				}
			} 
			?>
            <p><?php echo gopiplus_clean($post_content, $displaydesc); ?>...</p>
            <?php if ($displayreadmore == "YES") { ?>
            <p class="readmore">[ <a href="<?php the_permalink(); ?>">read more</a> ]</p>
            <?php } ?>
            <?php if ($displaytag == "YES") { ?>
            <p class="tags"><?php echo $post_tag; ?></p>
            <?php } ?>
            <div class="break"></div>
			</div>
			<?php 
		}
	}
	wp_reset_query();
	?>
	</div>
    <script type="text/javascript">
    jQuery(function() {
	jQuery('#gopiplushome').cycle({
		fx: '<?php echo @$slider; ?>',
		speed: 700,
		timeout: 5000
	});
	});
	</script>
    <!-- end gopiplushome -->
    <?php
}


# Removes tags and trailing dots from excerpt
function gopiplus_clean($excerpt, $substr=0) {
	$string = strip_tags(str_replace('[...]', '...', $excerpt));
	if ($substr>0) {
		$string = substr($string, 0, $substr);
	}
	return $string;
}

# Displays post image attachment (sizes: thumbnail, medium, full)
function gopiplus_attachment_image($postid=0, $size='thumbnail', $attributes='') {
	if ($postid<1) $postid = get_the_ID();
	if ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',)))
		foreach($images as $image) {
			$attachment=wp_get_attachment_image_src($image->ID, $size);
			?><img src="<?php echo $attachment[0]; ?>" <?php echo $attributes; ?> /><?php
		}
}


# Displays post image attachment (sizes: thumbnail, medium, full)
function gopiplus_attachment_image_filter($postid=0, $size='thumbnail', $attributes='') {
	if ($postid<1) $postid = get_the_ID();
	if ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',)))
		foreach($images as $image) 
		{
			$attachment=wp_get_attachment_image_src($image->ID, $size);
			
			return "<img src='". $attachment[0] . "' " . $attributes . " />";
		}
}

# Plugin installation default value
function gopiplus_install() 
{
	add_option('gopiplus_displaydate', "YES");
	add_option('gopiplus_displaycategory', "YES");
	add_option('gopiplus_displaycomment', "YES");
	add_option('gopiplus_displayimage', "YES");
	add_option('gopiplus_displaytag', "YES");
	add_option('gopiplus_displaydesc', "300");
	add_option('gopiplus_displayreadmore', "YES");
	add_option('gopiplus_query_posts_showposts', "3");
	add_option('gopiplus_query_posts_orderby', "rand");
	add_option('gopiplus_query_posts_order', "DESC");
	add_option('gopiplus_query_posts_category', "");
	add_option('gopiplus_query_slider', "scrollLeft");
}

# Admin update option for default value
function gopiplus_admin_options() 
{
	?>
	<div class="wrap">
  <div class="form-wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br>
    </div>
    <h2><?php _e('Random post slider', 'random-post-slider'); ?></h2>
	<h3><?php _e('Plugin setting', 'random-post-slider'); ?></h3>
	 <?php
	$gopiplus_displaydate = get_option('gopiplus_displaydate');
	$gopiplus_displaycategory = get_option('gopiplus_displaycategory');
	$gopiplus_displaycomment = get_option('gopiplus_displaycomment');
	$gopiplus_displayimage = get_option('gopiplus_displayimage');
	$gopiplus_displaytag = get_option('gopiplus_displaytag');
	$gopiplus_displaydesc = get_option('gopiplus_displaydesc');
	$gopiplus_displayreadmore = get_option('gopiplus_displayreadmore');
	$gopiplus_query_posts_showposts = get_option('gopiplus_query_posts_showposts');
	$gopiplus_query_posts_orderby = get_option('gopiplus_query_posts_orderby');
	$gopiplus_query_posts_order = get_option('gopiplus_query_posts_order');
	$gopiplus_query_posts_category = get_option('gopiplus_query_posts_category');
	$gopiplus_query_slider = get_option('gopiplus_query_slider');

	if (isset($_POST['gopiplus_submit'])) 
	{
		
		//	Just security thingy that wordpress offers us
		check_admin_referer('gopiplus_form_setting');
		
		$gopiplus_displaydate = stripslashes($_POST['gopiplus_displaydate']);
		$gopiplus_displaycategory = stripslashes($_POST['gopiplus_displaycategory']);
		$gopiplus_displaycomment = stripslashes($_POST['gopiplus_displaycomment']);
		$gopiplus_displayimage = stripslashes($_POST['gopiplus_displayimage']);
		$gopiplus_displaytag = stripslashes($_POST['gopiplus_displaytag']);
		$gopiplus_displaydesc = stripslashes($_POST['gopiplus_displaydesc']);
		$gopiplus_displayreadmore = stripslashes($_POST['gopiplus_displayreadmore']);
		$gopiplus_query_posts_showposts = stripslashes($_POST['gopiplus_query_posts_showposts']);
		$gopiplus_query_posts_orderby = stripslashes($_POST['gopiplus_query_posts_orderby']);
		$gopiplus_query_posts_order = stripslashes($_POST['gopiplus_query_posts_order']);
		$gopiplus_query_posts_category = stripslashes($_POST['gopiplus_query_posts_category']);
		$gopiplus_query_slider = stripslashes($_POST['gopiplus_query_slider']);
		
		update_option('gopiplus_displaydate', $gopiplus_displaydate );
		update_option('gopiplus_displaycategory', $gopiplus_displaycategory );
		update_option('gopiplus_displaycomment', $gopiplus_displaycomment );
		update_option('gopiplus_displayimage', $gopiplus_displayimage );
		update_option('gopiplus_displaytag', $gopiplus_displaytag );
		update_option('gopiplus_displaydesc', $gopiplus_displaydesc );
		update_option('gopiplus_displayreadmore', $gopiplus_displayreadmore );
		update_option('gopiplus_query_posts_showposts', $gopiplus_query_posts_showposts );
		update_option('gopiplus_query_posts_orderby', $gopiplus_query_posts_orderby );
		update_option('gopiplus_query_posts_order', $gopiplus_query_posts_order );
		update_option('gopiplus_query_posts_category', $gopiplus_query_posts_category );
		update_option('gopiplus_query_slider', $gopiplus_query_slider );
		
		?>
		<div class="updated fade">
			<p><strong><?php _e('Details successfully updated.', 'random-post-slider'); ?></strong></p>
		</div>
		<?php
	}
	 ?>
	 <form name="gopiplus_form" method="post" action="">
	  
	  <label for="tag-title"><?php _e('Display Date', 'random-post-slider'); ?></label>
      <input name="gopiplus_displaydate" id="gopiplus_displaydate" type="text" value="<?php echo $gopiplus_displaydate; ?>" />
      <p><?php _e('Enter YES or NO only ', 'random-post-slider'); ?> (YES/NO)</p>
	  
	  <label for="tag-title"><?php _e('Display Category', 'random-post-slider'); ?></label>
      <input name="gopiplus_displaycategory" id="gopiplus_displaycategory" type="text" value="<?php echo $gopiplus_displaycategory; ?>" />
      <p><?php _e('Enter YES or NO only ', 'random-post-slider'); ?> (YES/NO)</p>
	  
	  <label for="tag-title"><?php _e('Display Comment', 'random-post-slider'); ?></label>
      <input name="gopiplus_displaycomment" id="gopiplus_displaycomment" type="text" value="<?php echo $gopiplus_displaycomment; ?>" />
      <p><?php _e('Enter YES or NO only ', 'random-post-slider'); ?> (YES/NO)</p>
	  
	  <label for="tag-title"><?php _e('Display Image', 'random-post-slider'); ?></label>
      <input name="gopiplus_displayimage" id="gopiplus_displayimage" type="text" value="<?php echo $gopiplus_displayimage; ?>" />
      <p><?php _e('Enter YES or NO only ', 'random-post-slider'); ?> (YES/NO)</p>
	  
	  <label for="tag-title"><?php _e('Display Tag', 'random-post-slider'); ?></label>
      <input name="gopiplus_displaytag" id="gopiplus_displaytag" type="text" value="<?php echo $gopiplus_displaytag; ?>" />
      <p><?php _e('Enter YES or NO only ', 'random-post-slider'); ?> (YES/NO)</p>
	  
	  <label for="tag-title"><?php _e('Display Readmore', 'random-post-slider'); ?></label>
      <input name="gopiplus_displayreadmore" id="gopiplus_displayreadmore" type="text" value="<?php echo $gopiplus_displayreadmore; ?>" />
      <p><?php _e('Enter YES or NO only ', 'random-post-slider'); ?> (YES/NO)</p>
	  
	  <label for="tag-title"><?php _e('Display Content Length', 'random-post-slider'); ?></label>
      <input name="gopiplus_displaydesc" id="gopiplus_displaydesc" type="text" value="<?php echo $gopiplus_displaydesc; ?>" />
      <p><?php _e('Enter only number ', 'random-post-slider'); ?> (Ex: 300)</p>
	  
	  <label for="tag-title"><?php _e('Number of post to display', 'random-post-slider'); ?></label>
      <input name="gopiplus_query_posts_showposts" id="gopiplus_query_posts_showposts" type="text" value="<?php echo $gopiplus_query_posts_showposts; ?>" />
      <p><?php _e('Enter only number ', 'random-post-slider'); ?> (Ex: 3)</p>
	  
	  <label for="tag-title"><?php _e('Display post orderby', 'random-post-slider'); ?></label>
      <input name="gopiplus_query_posts_orderby" id="gopiplus_query_posts_orderby" type="text" value="<?php echo $gopiplus_query_posts_orderby; ?>" />
      <p><?php _e('Enter any one from the list ', 'random-post-slider'); ?> (ID / author / title / rand / date / category / modified)</p>
	  
	  <label for="tag-title"><?php _e('Display post order', 'random-post-slider'); ?></label>
      <input name="gopiplus_query_posts_order" id="gopiplus_query_posts_order" type="text" value="<?php echo $gopiplus_query_posts_order; ?>" />
      <p><?php _e('Enter ASC or DESC only ', 'random-post-slider'); ?> (ASC/DESC)</p>
	  
	  <label for="tag-title"><?php _e('Display post Categories', 'random-post-slider'); ?></label>
      <input name="gopiplus_query_posts_category" id="gopiplus_query_posts_category" type="text" value="<?php echo $gopiplus_query_posts_category; ?>" />
      <p><?php _e('Category IDs, separated by commas', 'random-post-slider'); ?> (Ex: 1, 2, 3)</p>
	  
	  <label for="tag-title"><?php _e('Slider Direction', 'random-post-slider'); ?></label>
      <input name="gopiplus_query_slider" id="gopiplus_query_slider" type="text" value="<?php echo $gopiplus_query_slider; ?>" />
      <p><?php _e('Enter any one from the list', 'random-post-slider'); ?> (scrollLeft / scrollRight / scrollUp / scrollDown)</p>
	  <input name="gopiplus_submit" id="gopiplus_submit" class="button-primary" value="<?php _e('Submit', 'random-post-slider'); ?>" type="submit" />
	  <?php wp_nonce_field('gopiplus_form_setting'); ?>
	 </form>
	 </div>
  <br />
<p class="description">
	<?php _e('Check official website for more information', 'random-post-slider'); ?>
	<a target="_blank" href="http://www.gopiplus.com/work/2011/05/28/wordpress-plugin-random-post-slider/"><?php _e('click here', 'random-post-slider'); ?></a>
</p>
</div>
	<?php
}

# gopiplus shortcode
function gopiplus_shortcode( $atts ) 
{
	global $wpdb;
	//[wp-post-slider]
	
	$sSqlMin = "";
	$gopiplushome = "";
	$post_tag = "";
	$displaydesc = "";
	$qp_showposts = "";
	
	$displaydate = get_option('gopiplus_displaydate');
	$displaycategory = get_option('gopiplus_displaycategory');
	$displaycomment = get_option('gopiplus_displaycomment');
	$displayimage = get_option('gopiplus_displayimage');
	$displaytag = get_option('gopiplus_displaytag');
	$displaydesc = get_option('gopiplus_displaydesc');
	$displayreadmore = get_option('gopiplus_displayreadmore');
	$qp_showposts = get_option('gopiplus_query_posts_showposts');
	$qp_orderby = get_option('gopiplus_query_posts_orderby');
	$qp_order = get_option('gopiplus_query_posts_order');
	$qp_category = get_option('gopiplus_query_posts_category');
	$slider = get_option('gopiplus_query_slider');
	
	if(!is_numeric($displaydesc)) { $displaydesc = 300; } 
	if(!is_numeric($qp_showposts)) { $qp_showposts = 10; } 
	
	$myfilter = "";
	
	if($slider == ""){ $slider = "scrollLeft"; }
	
	$gopiplushome = $gopiplushome . '<div id="gopipluspages">';
	//echo $myfilter;
	
	$sSqlMin = $sSqlMin . "select p.ID, p.post_title, p.post_content, p.comment_count, wpr.object_id, ". $wpdb->prefix . "terms.name , ". $wpdb->prefix . "terms.term_id ";
	$sSqlMin = $sSqlMin . "from ". $wpdb->prefix . "terms ";
	$sSqlMin = $sSqlMin . "inner join ". $wpdb->prefix . "term_taxonomy on ". $wpdb->prefix . "terms.term_id = ". $wpdb->prefix . "term_taxonomy.term_id ";
	$sSqlMin = $sSqlMin . "inner join ". $wpdb->prefix . "term_relationships wpr on wpr.term_taxonomy_id = ". $wpdb->prefix . "term_taxonomy.term_taxonomy_id ";
	$sSqlMin = $sSqlMin . "inner join ". $wpdb->prefix . "posts p on p.ID = wpr.object_id ";
	$sSqlMin = $sSqlMin . "where taxonomy= 'category' and p.post_type = 'post' and p.post_status = 'publish' ";
	
	if($qp_category <> "")
	{
		$sSqlMin = $sSqlMin . " and ". $wpdb->prefix . "terms.term_id in ($qp_category)";
	}
	
	if($qp_orderby == "date")
	{
		$sSqlMin = $sSqlMin . " order by p.post_date";
	}
	elseif($qp_orderby == "modified")
	{
		$sSqlMin = $sSqlMin . " order by p.post_modified";
	}
	elseif($qp_orderby == "title")
	{
		$sSqlMin = $sSqlMin . " order by p.post_title";
	}
	elseif($qp_orderby == "ID")
	{
		$sSqlMin = $sSqlMin . " order by p.ID";
	}
	elseif($qp_orderby == "category")
	{
		$sSqlMin = $sSqlMin . " order by " . $wpdb->prefix . "terms.name";
	}
	elseif($qp_orderby == "author")
	{
		$sSqlMin = $sSqlMin . " order by p.post_author";
	}
	else
	{
		$sSqlMin = $sSqlMin . " order by rand()";
	}
	
	$sSqlMin = $sSqlMin . " DESC";
	
	if($qp_showposts > 0 )
	{
		$sSqlMin = $sSqlMin . " LIMIT 0 , $qp_showposts";
	}
	else
	{
		$sSqlMin = $sSqlMin . " LIMIT 0 , 10";
	}
	
	$sSql = $wpdb->get_results($sSqlMin);
	
	if ( ! empty($sSql) ) 
	{
		$i = 0;
		foreach ( $sSql as $sSql ) 
		{ 
			$i = $i + 1;
			$post_title = $sSql->post_title;
			
			$post_link =  get_permalink($sSql->ID);
			$post_content = $sSql->post_content;
			
			$post_comment_count = $sSql->comment_count;
			
			$gopiplushome = $gopiplushome . '<div class="post">';
			$gopiplushome = $gopiplushome . '<h2><a href="'.$post_link.'">'.$post_title.'</a></h2>';
			
			foreach((get_the_category($sSql->ID)) as $category) 
			{ 
				if($i = 1)
				{
					$post_category = " | " . $category->cat_name . ' '; 
				}
				else
				{
					$post_category = $post_category . $category->cat_name; 
				}
			} 
			
			$posttags = get_the_tags($sSql->ID);
			if ($posttags) 
			{
			  foreach ($posttags as $tag) 
			  {
				 $tagnames[count($tagnames)] = $tag->name;
			  }
			  $comma_separated_tagnames = implode(", ", $tagnames);
			  $post_tag = "Tag : " . $comma_separated_tagnames;
			}
		
			$gopiplushome = $gopiplushome . '<p class="details">';
			
			if($displaydate == "YES") 
			{ 
				$gopiplushome = $gopiplushome . get_the_time('F j, Y');
            }
			
			if($displaycategory == "YES") 
            { 
            	$gopiplushome = $gopiplushome . $post_category;
            } 
			
			if($displaycomment == "YES") 
			{ 
				$gopiplushome = $gopiplushome . " | Comments ($post_comment_count)";
            }
			
			$gopiplushome = $gopiplushome . '</p>';

            if ($displayimage == "YES") 
			{ 
            	$img = gopiplus_attachment_image_filter($sSql->ID, 'thumbnail', 'alt="' . $sSql->post_title . '"');
				if($img <> "")
				{
					$gopiplushome = $gopiplushome . '<div class="thumb"><a href="'.$post_link. '">'.$img.'</a></div>';
				}
            }
			
			$gopiplushome = $gopiplushome . '<p>'. gopiplus_clean($post_content, $displaydesc) . '</p>';
			
			if ($displayreadmore == "YES") 
			{ 
            	$gopiplushome = $gopiplushome . '<p class="readmore">[ <a href="'.$post_link.'">read more</a> ]</p>';
            }
			
			if ($displaytag == "YES") 
			{ 
            	$gopiplushome = $gopiplushome . '<p class="tags"> ' . $post_tag . '</p>';
            }
			
			$gopiplushome = $gopiplushome . '<div class="break"></div>';
			$gopiplushome = $gopiplushome . '</div>';
			$post_category = "";
			$post_tag = "";
		}
	}
	$gopiplushome = $gopiplushome . "</div>";
	
    $gopiplushome = $gopiplushome . '<script type="text/javascript">';
    $gopiplushome = $gopiplushome . 'jQuery(function() {';
	$gopiplushome = $gopiplushome . "jQuery('#gopipluspages').cycle({fx: '".$slider."',speed: 700,timeout: 5000";
	$gopiplushome = $gopiplushome . '});';
	$gopiplushome = $gopiplushome . '});';
	$gopiplushome = $gopiplushome . '</script>';
	
	return $gopiplushome;
}

function gopiplus_add_to_menu() 
{
	add_options_page( __('Random post slider', 'random-post-slider'),  
				__('Random post slider', 'random-post-slider'), 'manage_options', __FILE__, 'gopiplus_admin_options' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'gopiplus_add_to_menu');
}

function gopiplus_deactivation() 
{
	// No action required.
}

function gopiplus_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery.cycle.all.latest', get_option('siteurl').'/wp-content/plugins/wordpress-plugin-random-post-slider/js/jquery.cycle.all.latest.js');
		wp_enqueue_style( 'wordpress-plugin-random-post-slider', get_option('siteurl').'/wp-content/plugins/wordpress-plugin-random-post-slider/wordpress-plugin-random-post-slider.css');
	}	
}

function gopiplus_textdomain() 
{
	  load_plugin_textdomain( 'random-post-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('plugins_loaded', 'gopiplus_textdomain');
add_shortcode( 'wp-post-slider', 'gopiplus_shortcode' );
add_action('init', 'gopiplus_add_javascript_files');
register_activation_hook(__FILE__, 'gopiplus_install');
register_deactivation_hook(__FILE__, 'gopiplus_deactivation');
?>