<?php

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

/* 
Date and Night Functions 
*/

function mathilda_day($date) {
	$day=substr($date, 6, 2);
	return $day;
}

function mathilda_year($date) {
	$year=substr($date, 0, 4);
	return $year;
}

function mathilda_hours($date) {
	$hours=substr($date, 8, 2);	
	return $hours;
}

function mathilda_minutes($date) {
	$minutes=substr($date, 10, 2);
	return $minutes;
}

function mathilda_month($date) {

	$month=substr($date, 4, 2);
	$this_wp_language=get_locale();

	if($this_wp_language=='de_DE') {
		if( strcasecmp ( $month , "01" ) == 0) { $m="Januar"; } ;
		if( strcasecmp ( $month , "02" ) == 0) { $m="Februar"; } ;
		if( strcasecmp ( $month , "03" ) == 0) { $m="März"; } ;
		if( strcasecmp ( $month , "04" ) == 0) { $m="April"; } ;
		if( strcasecmp ( $month , "05" ) == 0) { $m="Mai"; } ;
		if( strcasecmp ( $month , "06" ) == 0) { $m="Juni"; } ;
		if( strcasecmp ( $month , "07" ) == 0) { $m="Juli"; } ;
		if( strcasecmp ( $month , "08" ) == 0) { $m="August"; } ;
		if( strcasecmp ( $month , "09" ) == 0) { $m="September"; } ;
		if( strcasecmp ( $month , "10" ) == 0) { $m="Oktober"; } ;
		if( strcasecmp ( $month , "11" ) == 0) { $m="November"; } ;
		if( strcasecmp ( $month , "12" ) == 0) { $m="Dezember"; } ;
	} else {
		if( strcasecmp ( $month , "01" ) == 0) { $m="January"; } ;
		if( strcasecmp ( $month , "02" ) == 0) { $m="February"; } ;
		if( strcasecmp ( $month , "03" ) == 0) { $m="March"; } ;
		if( strcasecmp ( $month , "04" ) == 0) { $m="April"; } ;
		if( strcasecmp ( $month , "05" ) == 0) { $m="May"; } ;
		if( strcasecmp ( $month , "06" ) == 0) { $m="June"; } ;
		if( strcasecmp ( $month , "07" ) == 0) { $m="July"; } ;
		if( strcasecmp ( $month , "08" ) == 0) { $m="August"; } ;
		if( strcasecmp ( $month , "09" ) == 0) { $m="September"; } ;
		if( strcasecmp ( $month , "10" ) == 0) { $m="October"; } ;
		if( strcasecmp ( $month , "11" ) == 0) { $m="November"; } ;
		if( strcasecmp ( $month , "12" ) == 0) { $m="December"; } ;
	}

	return $m;
}

function mathilda_month_number($date) {
	$month=substr($date, 4, 2);
	return $month;
}

function mathilda_timezone_convert($date) {
	
	$day=mathilda_day($date);
	$month=mathilda_month_number($date);
	$year=mathilda_year($date);
	$hours=mathilda_hours($date);
	$minutes=mathilda_minutes($date);

	$date_str=''.$year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':00';

	date_default_timezone_set('UTC');

	$datetime = new DateTime($date_str);
	$wpdate = get_option('timezone_string');
	if($wpdate == '') 
	{
		$wpdate = get_option('gmt_offset');
		if($wpdate == '') 
		{
		// Fallback if TimeZone String or GMT Offset is not defined
		$wpdate='Atlantic/Reykjavik';
		}
		else {
		// Get TimeZone String from GMT Offset
		$wpdate=$wpdate*60*60;
		$wpdate=timezone_name_from_abbr("", $wpdate, 0);
		}
	} 
	$ber_time = new DateTimeZone($wpdate);
	$datetime->setTimezone($ber_time);

	return $datetime->format('YmdHi');

}

/* 
Pages
*/

function mathilda_pages() {
	
	$number_of_tweets=get_option('mathilda_select_amount');
	$tweets_on_page=get_option('mathilda_tweets_on_page');
	$number_of_pages=$number_of_tweets/$tweets_on_page;
	$number_of_pages=ceil($number_of_pages);
	
	return $number_of_pages;
}

/* 
Mathilda Menu
*/

function mathilda_create_menu($number_of_pages, $mathilda_show_page) {

	if($number_of_pages==1) {
		$menu_html='<!-- No Bottom Navigation required -->';
		return $menu_html;
		}
	
	$tweet_count_all=get_option('mathilda_tweets_count');
	$tweets_on_page=get_option('mathilda_tweets_on_page');

	if( $tweet_count_all >= $tweets_on_page ) {
		
		$menu_html='';
		$menu_html.='<div class="mathilda_bottom_forward">';

		$mathilda_menu_type=get_option('mathilda_navigation');
		if ($mathilda_menu_type=='Numbering')
		{

			$menu_html.=mathilda_menu_numbering($number_of_pages, $mathilda_show_page);

		} 
		elseif ($mathilda_menu_type=='Limited Numbering') 
		{

			$menu_html.=mathilda_menu_numbering_limit($number_of_pages, $mathilda_show_page);

		}
		elseif ($mathilda_menu_type=='Standard') 
		{

			$menu_html.=mathilda_menu_standard($number_of_pages, $mathilda_show_page);

		}
		
		$menu_html.='</div>';
		return $menu_html;
	}
	
}

/* 
Mathilda Numbering Menu
*/

function mathilda_menu_numbering ($number_of_pages, $mathilda_show_page) {

	$slug=get_option('mathilda_slug');
	$is_perma_enabled=mathilda_is_pretty_permalink_enabled();
	$menu_html='';

	for($i=0; $i<$number_of_pages; $i++) {
					
			$mathilda_page_number=$i+1;
			
			/* URL Mathilda */ 
			
			$current_url=get_page_link();
			$slug_pos=strpos($current_url, $slug );
			$path_to_mathilda=substr($current_url, 0, $slug_pos);
			
			/* Output */
			
			$menu_html.= '<a ';
			
			if ($i==0)
			{
				if($is_perma_enabled) {
						$menu_html.='href="'.$path_to_mathilda . $slug.'/" class="mathilda_bottom_forward_number';
					}
					else {
						$menu_html.='href="index.php?pagename='.$slug.'&mathilda='.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
					}
			} 
			else 
			{
				
				if($is_perma_enabled) {
						$menu_html.='href="'.$path_to_mathilda .$slug.'/'.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
					}
					else {
						$menu_html.='href="index.php?pagename='.$slug.'&mathilda='.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
					}
			}
			if($mathilda_show_page==$mathilda_page_number) {$menu_html.=' active'; }
			$menu_html.='">'.$mathilda_page_number.'</a>';
			
	}
	return $menu_html;
}

/* 
Mathilda Numbering Menu Limit
*/

function mathilda_menu_numbering_limit ($number_of_pages, $mathilda_show_page) {

		$slug=get_option('mathilda_slug');
		$is_perma_enabled=mathilda_is_pretty_permalink_enabled();
		$menu_html='';

		for($i=0; $i<$number_of_pages; $i++) {
						
				$mathilda_page_number=$i+1;
				
				/* URL Mathilda */ 
				
				$current_url=get_page_link();
				$slug_pos=strpos($current_url, $slug );
				$path_to_mathilda=substr($current_url, 0, $slug_pos);
				

				/* Output */
				
				
				if ($i==0) // 1
				{
					$menu_html.= '<a ';
					if($is_perma_enabled) {
							$menu_html.='href="'.$path_to_mathilda . $slug.'/" class="mathilda_bottom_forward_number';
						}
						else {
							$menu_html.='href="index.php?pagename='.$slug.'&mathilda='.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
					if($mathilda_show_page==$mathilda_page_number) {$menu_html.=' active'; }
					$menu_html.='">'.$mathilda_page_number.'</a>';
				} 
				elseif($i==($number_of_pages-1)) // Highest Page
				{
					$menu_html.= '<a ';
					if($is_perma_enabled) {
							$menu_html.='href="'.$path_to_mathilda .$slug.'/'.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
						else {
							$menu_html.='href="index.php?pagename='.$slug.'&mathilda='.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
					if($mathilda_show_page==$mathilda_page_number) {$menu_html.=' active'; }
					$menu_html.='">'.$mathilda_page_number.'</a>';
				}
				elseif ( $i <= 4 AND $mathilda_show_page < 5 ) // First 5 
				{
					$menu_html.= '<a ';
					if($is_perma_enabled) {
							$menu_html.='href="'.$path_to_mathilda .$slug.'/'.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
						else {
							$menu_html.='href="index.php?pagename='.$slug.'&mathilda='.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
						if($mathilda_show_page==$mathilda_page_number) {$menu_html.=' active'; }
						$menu_html.='">'.$mathilda_page_number.'</a>';
				}
				elseif ( $i >= ($number_of_pages-5) AND $mathilda_show_page > ($number_of_pages-4) ) // Last 5 
				{
					$menu_html.= '<a ';
					if($is_perma_enabled) {
							$menu_html.='href="'.$path_to_mathilda .$slug.'/'.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
						else {
							$menu_html.='href="index.php?pagename='.$slug.'&mathilda='.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
						if($mathilda_show_page==$mathilda_page_number) {$menu_html.=' active'; }
						$menu_html.='">'.$mathilda_page_number.'</a>';
				}
				elseif ( ($i>=($mathilda_show_page-3) AND $i<($mathilda_show_page+2)) ) // Others
				{
					$menu_html.= '<a ';
					if($is_perma_enabled) {
							$menu_html.='href="'.$path_to_mathilda .$slug.'/'.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
						else {
							$menu_html.='href="index.php?pagename='.$slug.'&mathilda='.$mathilda_page_number.'/" class="mathilda_bottom_forward_number';
						}
						if($mathilda_show_page==$mathilda_page_number) {$menu_html.=' active'; }
						$menu_html.='">'.$mathilda_page_number.'</a>';
				}

				if($i==0 && $mathilda_show_page>3) {
					$menu_html.='<span class="mathilda_extend">...</span>';
				}

				if($i==($number_of_pages-2) && $number_of_pages>7 AND $mathilda_show_page < ($number_of_pages-2)) {
					$menu_html.='<span class="mathilda_extend">...</span>';
				}
				
		}

		return $menu_html;
}

/* 
Mathilda Standard Menu
*/

function mathilda_menu_standard ($number_of_pages, $mathilda_show_page) {

	$slug=get_option('mathilda_slug');
	$is_perma_enabled=mathilda_is_pretty_permalink_enabled();
	$menu_html='';

	/* URL Mathilda */ 
				
	$current_url=get_page_link();
	$slug_pos=strpos($current_url, $slug );
	$path_to_mathilda=substr($current_url, 0, $slug_pos);

	/* Output */

	$this_wp_language=get_locale();

	if($is_perma_enabled) {

		if($mathilda_show_page<$number_of_pages) {
		$menu_html.='<a href="'.$path_to_mathilda . $slug.'/'.($mathilda_show_page+1).'/" class="mathilda_bottom_previous_tweets';
			if($this_wp_language=='de_DE') {
			$menu_html.='">Ältere Tweets</a>';
			}
			else {
			$menu_html.='">Older Tweets</a>';	
			}
		}
		
		if($mathilda_show_page>1) {
			if($mathilda_show_page==2)
			{
			$menu_html.='<a href="'.$path_to_mathilda . $slug.'/" class="mathilda_bottom_new_tweets';
			}
			else
			{
			$menu_html.='<a href="'.$path_to_mathilda . $slug.'/'.($mathilda_show_page-1).'/" class="mathilda_bottom_new_tweets';	
			}
			if($this_wp_language=='de_DE') {
			$menu_html.='">Neuere Tweets</a>';
			}
			else {
			$menu_html.='">Newer Tweets</a>';	
			}
		}

	}
	else {

		if($mathilda_show_page<$number_of_pages) {
		$menu_html.='<a href="index.php?pagename='.$slug.'&mathilda='.($mathilda_show_page+1).'/" class="mathilda_bottom_previous_tweets';
		if($this_wp_language=='de_DE') {
			$menu_html.='">Ältere Tweets</a>';
			}
			else {
			$menu_html.='">Older Tweets</a>';	
			}
		}

		if($mathilda_show_page>1) {
		$menu_html.='<a href="index.php?pagename='.$slug.'&mathilda='.($mathilda_show_page-1).'/" class="mathilda_bottom_new_tweets';
		if($this_wp_language=='de_DE') {
			$menu_html.='">Neuere Tweets</a>';
			}
			else {
			$menu_html.='">Newer Tweets</a>';	
			}
		}
	
	}
	return $menu_html;

}

/* 
Tweet Painting Function 
*/

function mathilda_tweet_paint($date,$tweet,$id,$me,$image,$mention,$url,$hashtag) {
	
	$mathilda_content='';
	
	/* Date Formatting */

	$newdate=mathilda_timezone_convert($date);

    $day=mathilda_day($newdate);
	$month=mathilda_month($newdate);
	$year=mathilda_year($newdate);
	$hours=mathilda_hours($newdate);
	$minutes=mathilda_minutes($newdate);

	$pos_zero = strpos($day, '0');
	if ($pos_zero === 0 ) {
		$day=substr_replace ( $day , '' , 0, 1);
	}
	 
	/* Get Image */
	$tweet_images=array();
	$image_follows_class_url='';
	$image_follows_class_tweet='';
	if ($image=='TRUE')
	{
	$tweet_images=mathilda_read_image($id);
	$image_follows_class_tweet=' mathilda-image-follows-tweet';
	$image_follows_class_url=' mathilda-image-follows-url';
	}
	
	/* Get Hashtag */
	$tweet_hashtags=array();
	if ($hashtag=='TRUE')
	{$tweet_hashtags=mathilda_read_hashtag($id);}
	
	/* Get Mention */
	$tweet_mentions=array();
	if ($mention=='TRUE')
	{$tweet_mentions=mathilda_read_mention($id);}
	
	/* Get URL */
	$tweet_urls=array();
	$url_follows_class='';
	if ($url=='TRUE')
	{$tweet_urls=mathilda_read_url($id);$url_follows_class=' mathilda-url-follows';}
	
	/* Tweet Area */
	$mathilda_content.='<div class="mathilda-tweet">';
	
	/* Twitter Logo & Tweet Hyperlink */
	$tweet_backlink=get_option('mathilda_tweet_backlink');
	if($tweet_backlink==1){
			$mathilda_content.='<a href="https://twitter.com/'.$me.'/status/'.$id.'" target="_blank" rel="nofollow" class="mathilda-tweet-link">';
	}
	$mathilda_content.='<img class="mathilda-tweet-symbol" src="'. plugins_url() .'/mathilda/mathilda_tweet.png" alt="Tweet"/>';
	if($tweet_backlink==1){
			$mathilda_content.='</a>';
	}
	
	/* Date */
	
	$mathilda_content.='<p class="mathilda-tweet-dateandtime"><span class="mathilda-tweet-date"><span class="mathilda-tweet-date-day">'.$day.'.</span> <span class="mathilda-tweet-date-month">'.$month.'</span> <span class="mathilda-tweet-date-year">'.$year.'</span></span>';
	if( $year > '2010' ) // Very Old Tweets do not have time 
	{ $mathilda_content.='&nbsp;&nbsp;<span class="mathilda-tweet-time"><span class="mathilda-tweet-date-hours">'.$hours.'</span>:<span class="mathilda-tweet-date-minutes">'.$minutes.'</span></span></p>'; }
	
	/* Hashtag Transformation @ Tweet */
	
	if ($hashtag=='TRUE')
	{
		$num_hashtags=count($tweet_hashtags);
		for($i=0; $i<$num_hashtags; $i++)
		{
			$hashtag_html='<a href="https://twitter.com/search?q=%23'.$tweet_hashtags[$i][1].'" class="mathilda-hashtag" rel="nofollow" target="_blank">#'.$tweet_hashtags[$i][1].'</a> ';
			$hashtag_search='#'.$tweet_hashtags[$i][1].' ';
			$tweet=str_replace ( $hashtag_search, $hashtag_html , $tweet ) ;
		}
		
	}
	
	/* Mention Transformation @ Tweet */
	
	if ($mention=='TRUE')
	{
		$num_mentions=count($tweet_mentions);
		for($i=0; $i<$num_mentions; $i++)
		{
			$mention_html='<a href="https://twitter.com/'.$tweet_mentions[$i][2].'" class="mathilda-mention" target="_blank" rel="nofollow">@'.$tweet_mentions[$i][2].'</a>';
			$mention_search='@'.$tweet_mentions[$i][2];
			$tweet=str_replace ( $mention_search, $mention_html , $tweet ) ;	
		}
	}
	
	/* URL Transformation @ Tweet */
	
	$url_handling=get_option('mathilda_hyperlink_rendering');
	$embed_content=get_option('mathilda_embed');
	
	if ($url=='TRUE')
	{
		foreach($tweet_urls as $tweet_url) {

		if ($url_handling=='Longlink') {
			
				$tweet=str_replace ( $tweet_url[1], ' ' , $tweet ) ;	
		
		}
		elseif ($url_handling=='Shortlink') {
			if($embed_content) {
				$embed_value=mathilda_embed_rendering($tweet_url[0],$tweet_url[2]);
				if($embed_value) {
					$url_handling="Longlink";
					$tweet=str_replace ( $tweet_url[1], ' ' , $tweet ) ;
				}
				else {
					$shortlink='<a class="mathilda-tweet-url-link mathilda-shortlink" href="'.$tweet_url[1].'" target="_blank">'.$tweet_url[3].'</a>';$tweet=str_replace ( $tweet_url[1], $shortlink , $tweet ) ;
				}
			} else {
				$shortlink='<a class="mathilda-tweet-url-link mathilda-shortlink" href="'.$tweet_url[1].'" target="_blank">'.$tweet_url[3].'</a>';	
				$tweet=str_replace ( $tweet_url[1], $shortlink , $tweet ) ;
			}
		}

		}
	}
		  
	/* Media Transformation @ Tweet */  
	if ($image=='TRUE') 
	{		
		$tweet=str_replace ( $tweet_images[0][4], '' , $tweet ) ;	
	}
		  
	/* Paint Tweet */

	$tweet = nl2br($tweet);
	if ( ( ($url=='TRUE') AND ($image=='TRUE') ) )  { $image_follows_class_tweet='X'; }
	$mathilda_content.='<p class="mathilda-tweet-text'.$url_follows_class.' '.$image_follows_class_tweet.'">' . $tweet . '</p>';
	
	/* Paint URL */  
	if ($url=='TRUE') 
	{

		// Initialize URL Counter
		$number_of_urls=count($tweet_urls);
		$current_url=1;
		
		foreach($tweet_urls as $tweet_url)
		{	
			// Margin Bottom More As 1 URL
			if($number_of_urls > $current_url ) {
				$current_url_style=' style="margin-bottom:5px"'; 
			}
			else {
				$current_url_style="";
			}
			
			$current_url=$current_url+1;

			if ($url_handling=='Longlink') {

				if($embed_content) { 

					$embed_value=mathilda_embed_rendering($tweet_url[0],$tweet_url[2]);

					if ($embed_value) {

						if (is_ssl()) {
							if (strpos($tweet_url[2], 'https://') !== false) {
								$mathilda_content.='<div class="mathilda-tweet-url mathilda-embed mathilda-longlink'.$image_follows_class_url.'"> ';
								$mathilda_content.=$embed_value;
								$mathilda_content.=' </div>';
							}
							else {
								$mathilda_content.='<p class="mathilda-tweet-url mathilda-longlink'.$image_follows_class_url.'"><a class="mathilda-tweet-url-link" '.$current_url_style.' href="'.$tweet_url[2].'" target="_blank">'.$tweet_url[2].'</a></p>';
							}
						} else {
							$mathilda_content.='<div class="mathilda-tweet-url mathilda-embed mathilda-longlink'.$image_follows_class_url.'"> ';
							$mathilda_content.=$embed_value;
							$mathilda_content.=' </div>';
						}

					} else {
						$mathilda_content.='<p '.$current_url_style.' class="mathilda-tweet-url mathilda-longlink'.$image_follows_class_url.'"><a class="mathilda-tweet-url-link" href="'.$tweet_url[2].'" target="_blank">'.$tweet_url[2].'</a></p>';
					}

				}
				else {
					$mathilda_content.='<p '.$current_url_style.' class="mathilda-tweet-url mathilda-longlink'.$image_follows_class_url.'"><a class="mathilda-tweet-url-link" href="'.$tweet_url[2].'" target="_blank">'.$tweet_url[2].'</a></p>';
				} 
			}	
		}
	}
		  
	/* Paint Image */
	if ($image=='TRUE')
	{

		// Initialize Image Counter for Line Break
		$number_of_images=count($tweet_images);
		$current_image=1;

		foreach($tweet_images as $tweet_image)
		{	

		$mathilda_tweet_image=$tweet_image[14];
		$upload_dir = wp_upload_dir();
		$mathilda_images_dirname="mathilda-images";
		$mathilda_images_dirwithpath = $upload_dir['baseurl'].'/'.$mathilda_images_dirname .'/';
		$mathilda_content.='<img src="'.$mathilda_images_dirwithpath.$mathilda_tweet_image.'" alt="Tweet Image" class="mathilda-tweet-image"/>';	 

		// Identify Video Tweet
		$video_extendedurl=$tweet_image[6];
		$is_video_url = strpos($video_extendedurl, 'video');
		if($is_video_url!==false) {
			$mathilda_content.='<div class="mathilda-tweet-video-link">';
			$mathilda_content.='Video: <a href="'.$tweet_image[6].'">'.$tweet_image[6].'</a>';
			$mathilda_content.="</div>";
			$mathilda_content.="&nbsp;<br/>";  
		}

			// Line Break for Tweet Galerys
			if($number_of_images>1) {
				if($number_of_images != $current_image ) {
					$mathilda_content.="<br/>&nbsp;<br/>"; 
				}
			}

		$current_image=$current_image+1;

		}

	}
	
	/* Close */
	$mathilda_content.='
	<!-- ' . $id . ' -->
	</div>';
	
	return $mathilda_content;
		  
}

/*
Mathilda Embed Rendering
*/

// Retrieves and Delivers the Embed Code 
// Input: URL-ID, URL
// Output: Embedded Code or FALSE

function mathilda_embed_rendering($mathilda_url_id,$mathilda_tweet_url) {

	$embed_value=mathilda_get_embed($mathilda_url_id);

	if($embed_value=='OPEN') {

		return false;

	} elseif ($embed_value=='NOEMBED') {

		return false;
	
	} else {
	
		$embed_value = html_entity_decode($embed_value, ENT_QUOTES);
		return $embed_value;
	
	}
}

?>