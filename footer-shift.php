<?php
				if( is_page() ){
					// Top Slider Part				
					
					
					// Under Slider Area
					if(get_post_meta( $post->ID, 'page-option-enable-bottom-slider', true) == 'Yes'){
						$stunning_title = get_post_meta( $post->ID, 'page-option-under-slider-title', true);
						$stunning_caption = get_post_meta( $post->ID, 'page-option-under-slider-caption', true);
						$stunning_button_text = get_post_meta( $post->ID, 'page-option-under-slider-button-text', true);
						$stunning_button_link = get_post_meta( $post->ID, 'page-option-under-slider-button-link', true);
						
						$button_class = (!empty($stunning_button_text) && !empty($stunning_button_link))? 'button-on': '';
						
						echo '<div class="under-slider-wrapper">';
						echo '<div class="under-slider-container container">';
						
						echo '<div class="under-slider-inner-wrapper ' . $button_class . '">';
						echo '<h2 class="under-slider-title">' . $stunning_title . '</h2>';
						echo '<div class="under-slider-caption">' . $stunning_caption . '</div>';
						if( !empty($stunning_button_text) && !empty($stunning_button_link) ){
							echo '<a href="' . $stunning_button_link . '" class="under-slider-button gdl-button large">';
							echo $stunning_button_text . '</a>';
						}
						echo '</div>';
						
						echo '</div>';
						echo '</div>';
					}
					
				}else if( is_single() ){
					if( $post->post_type == 'portfolio' ){
						$single_title = get_the_title();
						$single_caption = get_post_meta( $post->ID, "post-option-blog-header-caption", true);
						print_page_header($single_title, $single_caption);					
					}else if($post->post_type == 'package'){
						$single_title = get_the_title();
						$single_caption = get_post_meta( $post->ID, "post-option-blog-header-caption", true);
						print_page_header($single_title, $single_caption);
					}else{
						$single_title = get_post_meta( $post->ID, "post-option-blog-header-title", true);
						$single_caption = get_post_meta( $post->ID, "post-option-blog-header-caption", true);
						if(empty( $single_title )){
							$single_title = get_option(THEME_SHORT_NAME . '_default_post_header','Blog Post');
							$single_caption = get_option(THEME_SHORT_NAME . '_default_post_caption');
						}
						print_page_header($single_title, $single_caption);			
					}	
				}else if( is_404() ){
					global $gdl_admin_translator;
					if( $gdl_admin_translator == 'enable' ){
						$translator_404_title = get_option(THEME_SHORT_NAME.'_404_title', 'Page Not Found');
					}else{
						$translator_404_title = __('Page Not Found','gdl_front_end');		
					}			
					print_page_header($translator_404_title);
				}else if( is_search() ){
					global $gdl_admin_translator;
					if( $gdl_admin_translator == 'enable' ){
						$title = get_option(THEME_SHORT_NAME.'_search_header_title', 'Search Results');
					}else{
						$title = __('Search Results', 'gdl_front_end');
					}		
					
					$caption = get_search_query();
					print_page_header($title, $caption);
				}else if( is_archive() ){
					
					if( is_category() || is_tax('portfolio-category') || is_tax('product_cat') ||
						is_tax('package-category')){
						$title = __('Category','gdl_front_end');
						$caption = single_cat_title('', false);
					}else if( is_tag() || is_tax('portfolio-tag') || is_tax('product_tag') ||
						is_tax('package-tag') ){
						$title = __('Tag','gdl_front_end');
						$caption = single_cat_title('', false);
					}else if( is_day() ){
						$title = __('Day','gdl_front_end');
						$caption = get_the_date('F j, Y');
					}else if( is_month() ){
						$title = __('Month','gdl_front_end');
						$caption = get_the_date('F Y');
					}else if( is_year() ){
						$title = __('Year','gdl_front_end');
						$caption = get_the_date('Y');
					}else if( is_author() ){
						$title = __('By','gdl_front_end');
						
						$author_id = get_query_var('author');
						$author = get_user_by('id', $author_id);
						$caption = $author->display_name;					
					}else{
						$title = __('Shop','gdl_front_end');
					}
							
					print_page_header($title, $caption);				
				} 
			?>



		<?php 
			$gdl_show_twitter = (get_option(THEME_SHORT_NAME.'_show_twitter_bar','enable') == 'enable')? true: false; 
			$gdl_homepage_twitter = (get_option(THEME_SHORT_NAME.'_show_twitter_only_homepage','disable') == 'enable')? true: false; 
			
			if( $gdl_show_twitter && ( ($gdl_homepage_twitter && is_front_page()) || !$gdl_homepage_twitter )){
				$twitter_id = get_option(THEME_SHORT_NAME.'_twitter_bar_id'); 
				$num_fetch = get_option(THEME_SHORT_NAME.'_twitter_num_fetch'); 
				$consumer_key = get_option(THEME_SHORT_NAME.'_twitter_bar_consumer_id'); 
				$consumer_secret = get_option(THEME_SHORT_NAME.'_twitter_bar_consumer_secret'); 
				$access_token = get_option(THEME_SHORT_NAME.'_twitter_bar_access_token'); 
				$access_token_secret = get_option(THEME_SHORT_NAME.'_twitter_bar_access_token_secret'); 
				$cache_time = get_option(THEME_SHORT_NAME.'_twitter_bar_cache_time', '1'); 			

				$last_cache_time = get_option(THEME_SHORT_NAME . '_twitter_bar_last_cache_time', 0);
				$diff = time() - $last_cache_time;
				$crt = $cache_time * 3600;		
				if(empty($last_cache_time) || $diff >= $crt){
				
					$connection = getConnectionWithAccessToken($consumer_key, $consumer_secret, $access_token, $access_token_secret);
					$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitter_id."&count=" . $num_fetch) or die('Couldn\'t retrieve tweets! Wrong username?');
					
					if(!empty($tweets->errors)){
						if($tweets->errors[0]->message == 'Invalid or expired token'){
							echo '<strong>'.$tweets->errors[0]->message.'!</strong><br />You\'ll need to regenerate it <a href="https://dev.twitter.com/apps" target="_blank">here</a>!' . $after_widget;
						}else{
							echo '<strong>'.$tweets->errors[0]->message.'</strong>' . $after_widget;
						}
						return;
					}

					$tweets_data = array();
					for($i = 0;$i <= count($tweets); $i++){
						if(!empty($tweets[$i])){
							$tweets_data[$i]['created_at'] = $tweets[$i]->created_at;
							$tweets_data[$i]['text'] = $tweets[$i]->text;			
							$tweets_data[$i]['status_id'] = $tweets[$i]->id_str;			
						}	
					}			
					
					update_option(THEME_SHORT_NAME . '_twitter_bar_tweets',serialize($tweets_data));							
					update_option(THEME_SHORT_NAME . '_twitter_bar_last_cache_time',time());	
				}else{
					$tweets_data = maybe_unserialize(get_option(THEME_SHORT_NAME . '_twitter_bar_tweets'));
				}					
		?>
		<div class="footer-twitter-wrapper boxed-style">
			<div class="container twitter-container">
				<i class="gdl-twitter-icon icon-twitter"></i>
				<div class="gdl-twitter-wrapper">
					<div class="gdl-twitter-navigation">
						<a class="prev icon-angle-left"></a>
						<a class="next icon-angle-right"></a>
					</div>					
					<ul id="gdl-twitter" >
					<?php
						foreach( $tweets_data as $each_tweet ){
							echo '<li>';
							echo '<span>' . convert_links($each_tweet['text']) . '</span>';
							echo '<a class="date" target="_blank" href="http://twitter.com/'.$twitter_id.'/statuses/'.$each_tweet['status_id'].'">'.relative_time($each_tweet['created_at']).'</a>';
							echo '</li>';
						}	
					?>					
					</ul>	
					<script type="text/javascript">
						jQuery(document).ready(function(){
							var twitter_wrapper = jQuery('ul#gdl-twitter');
							twitter_wrapper.each(function(){
						
								var fetch_num = jQuery(this).children().length;
								var twitter_nav = jQuery(this).siblings('div.gdl-twitter-navigation');

								if( fetch_num > 0 ){ 
									gdl_cycle_resize(twitter_wrapper);
									twitter_wrapper.cycle({ fx: 'fade', slideResize: 1, fit: true, width: '100%', timeout: 4000, speed: 1000,
										next: twitter_nav.children('.next'),  prev: twitter_nav.children('.prev') });
								}
							});	

							jQuery(window).resize(function(){ 
								if( twitter_wrapper ){ gdl_cycle_resize(twitter_wrapper); }
							});								
						});	
					</script>				
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php 
				wp_deregister_script('jquery-cycle');
				wp_register_script('jquery-cycle', GOODLAYERS_PATH.'/javascript/jquery.cycle.js', false, '1.0', true);
				wp_enqueue_script('jquery-cycle');	
			} // $gdl-show-twitter
		?>		
		
		<div class="footer-wrapper boxed-style">

		<!-- Get Footer Widget -->
		<?php $gdl_show_footer = get_option(THEME_SHORT_NAME.'_show_footer','enable'); ?>
		<?php if( $gdl_show_footer == 'enable' ){ ?>
			<div class="container footer-container">
				<div class="footer-widget-wrapper">
					<div class="row">
						<?php
							$gdl_footer_class = array(
								'footer-style1'=>array('1'=>'three columns', '2'=>'three columns', '3'=>'three columns', '4'=>'three columns'),
								'footer-style2'=>array('1'=>'six columns', '2'=>'three columns', '3'=>'three columns', '4'=>''),
								'footer-style3'=>array('1'=>'three columns', '2'=>'three columns', '3'=>'six columns', '4'=>''),
								'footer-style4'=>array('1'=>'four columns', '2'=>'four columns', '3'=>'four columns', '4'=>''),
								'footer-style5'=>array('1'=>'eight columns', '2'=>'four columns', '3'=>'', '4'=>''),
								'footer-style6'=>array('1'=>'four columns', '2'=>'eight columns', '3'=>'', '4'=>''),
								);
							$gdl_footer_style = get_option(THEME_SHORT_NAME.'_footer_style', 'footer-style1');
						 
							for( $i=1 ; $i<=4; $i++ ){
								$footer_class = $gdl_footer_class[$gdl_footer_style][$i];
									if( !empty($footer_class) ){
									echo '<div class="' . $footer_class . ' gdl-footer-' . $i . ' mb0">';
									dynamic_sidebar('Footer ' . $i);
									echo '</div>';
								}
							}
						?>
						<div class="clear"></div>
					</div> <!-- close row -->
					
					<!-- Get Copyright Text -->
					<?php $gdl_show_copyright = get_option(THEME_SHORT_NAME.'_show_copyright','enable'); ?>
					<?php if( $gdl_show_copyright == 'enable' ){ ?>
						<div class="copyright-wrapper">
							<div class="copyright-border"></div>
							<div class="copyright-left">
								<?php echo do_shortcode( __(get_option(THEME_SHORT_NAME.'_copyright_left_area'), 'gdl_front_end') ); ?>
							</div>
						</div>
					<?php } ?>					
				</div>
			</div> 
		<?php } ?>

		</div><!-- footer wrapper -->
	</div> <!-- body wrapper -->
</div> <!-- body outer wrapper -->
	
<?php wp_footer(); ?>

</body>
</html>