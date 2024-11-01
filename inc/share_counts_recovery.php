<?php
//add recovery share counts to existing share counts array
function ultimatesocialshare_add_recovered_share_counts(&$fresh_share_counts, $active_networks, $post_id) {

	//get recovery urls we need for this post
	$recovery_urls = ultimatesocialshare_get_recovery_urls($post_id);

	//exit if there aren't any recovery urls
	if(empty($recovery_urls)) {
		return;
	}

	global $wpdb;

	//get plugin options
	$ultimatesocialshare = get_option('ultimatesocialshare')['general'];
	
	//get existing recovery share counts for post
	$cached_recovery_share_counts_row = $wpdb->get_row($wpdb->prepare("SELECT id, meta_value FROM {$wpdb->prefix}ultimatesocialshare_meta WHERE post_id = %d AND meta_key = 'recovery_share_counts'", $post_id));
	$recovery_share_counts = !empty($cached_recovery_share_counts_row->meta_value) ? maybe_unserialize($cached_recovery_share_counts_row->meta_value) : '';

	//get existing recovery urls for post
	$cached_recovery_urls_row = $wpdb->get_row($wpdb->prepare("SELECT id, meta_value FROM {$wpdb->prefix}ultimatesocialshare_meta WHERE post_id = %d AND meta_key = 'recovery_urls'", $post_id));
	$cached_recovery_urls = !empty($cached_recovery_urls_row->meta_value) ? maybe_unserialize($cached_recovery_urls_row->meta_value) : array();
	
	//get changed items in cached vs current recovery urls
	$recovery_urls_changes = array_merge(array_diff($cached_recovery_urls, $recovery_urls), array_diff($recovery_urls, $cached_recovery_urls));

	///get fresh recovery share counts
	if(!empty($recovery_urls_changes)) {

		//setup recovery share counts array
		$recovery_share_counts = array();

		//networks that treat http + https as separate urls
		$http_https_networks = array('facebook', 'pinterest');

		//loop through active networks
		foreach($active_networks as $key => $network) {

			//add base 0 count for existing recovery shares
			if(!isset($recovery_share_counts[$network])) {
				$recovery_share_counts[$network] = 0;
			}

			//loop through recovery urls and pull share counts
			foreach($recovery_urls as $recovery_url) {

				//get share counts for both http + https
				if(in_array($network, $http_https_networks) && !empty($ultimatesocialshare['combine_http_https'])) {

					//get http + https urls
					$https_check = strpos(strtolower($recovery_url), 'https');
					$recovery_url_http = $https_check === 0 ? substr_replace($recovery_url, 'http', 0, 5) : $recovery_url;
					$recovery_url_https = $https_check === 0 ? $recovery_url : substr_replace($recovery_url, 'https', 0, 4);

					//pull share counts
					$share_count_http = ultimatesocialshare_network_share_count($recovery_url_http, $network);
					$share_count_https = ultimatesocialshare_network_share_count($recovery_url_https, $network);
					$combined_share_count = $share_count_http + $share_count_https;
					
					//add combined share count to recovery array
					if(!empty($combined_share_count) && $combined_share_count > 0) {
						$recovery_share_counts[$network] += $combined_share_count;
					}
				}
				//only get share counts for urls given protocol 
				else {

					//get share count
					$share_count = ultimatesocialshare_network_share_count($recovery_url, $network);

					//add share count to recovery array
					if($share_count !== false) {
						$recovery_share_counts[$network] += $share_count;
					}
				}
			}
		}

		//filter out any empty values
		$recovery_share_counts = array_filter($recovery_share_counts);

		//update/insert new recovery share counts
		$wpdb->replace($wpdb->prefix . 'ultimatesocialshare_meta', array(
				'id'         => (!empty($cached_recovery_share_counts_row->id) ? $cached_recovery_share_counts_row->id : ''),
				'post_id'    => $post_id,
				'meta_key'   => 'recovery_share_counts',
				'meta_value' => (!empty($recovery_share_counts) ? maybe_serialize($recovery_share_counts) : '')
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s'
			)
		);

		global $ultimatesocialshare_failed_response;

		//update/insert new recovery urls
		if($ultimatesocialshare_failed_response == false) {
			$wpdb->replace($wpdb->prefix . 'ultimatesocialshare_meta', array(
					'id'         => (!empty($cached_recovery_urls_row->id) ? $cached_recovery_urls_row->id : ''),
					'post_id'    => $post_id,
					'meta_key'   => 'recovery_urls',
					'meta_value' => (!empty($recovery_urls) ? maybe_serialize($recovery_urls) : '')
				),
				array(
					'%d',
					'%d',
					'%s',
					'%s'
				)
			);
		}
	}

	//add recovery shares to main shares array
	foreach($active_networks as $key => $network) {
		if(!empty($recovery_share_counts[$network])) {
			if(isset($fresh_share_counts[$network])) {
				$fresh_share_counts[$network] += $recovery_share_counts[$network];
			}
			else {
				$fresh_share_counts[$network] = $recovery_share_counts[$network];
			}
		}
	}
}

//return recovery urls for post
function ultimatesocialshare_get_recovery_urls($post_id) {

	//get plugin options
	$ultimatesocialshare = get_option('ultimatesocialshare')['general'];

	//setup recovery urls array
	$recovery_urls = array();

	//permalink structure recovery
	if(!empty($ultimatesocialshare['recover_previous_permalinks']) && !empty($ultimatesocialshare['previous_permalink_structure'])) {

		//setup permalink urls array
		$permalink_urls = array();

		//get permalink structure setting
		$permalink_structure = !empty($ultimatesocialshare['previous_permalink_structure']) ? $ultimatesocialshare['previous_permalink_structure'] : '';
		if(!empty($permalink_structure) && $permalink_structure == 'custom') {
			$permalink_structure = !empty($ultimatesocialshare['previous_permalink_structure_custom']) ? $ultimatesocialshare['previous_permalink_structure_custom'] : '';
		}
		
		//get recovery permalink for post
		$post_recovery_permalink = ultimatesocialshare_get_post_recovery_permalink($post_id, $permalink_structure);

		//add recovery permalink to main array
		if($post_recovery_permalink !== false) {
			$recovery_urls[] = $post_recovery_permalink;
		}
	}

	//domain recovery
	if(!empty($ultimatesocialshare['recover_previous_domain']) && !empty($ultimatesocialshare['previous_domain'])) {

		//get current domain
		$domain = preg_replace('#^www\.(.+\.)#i', '$1', parse_url(get_site_url(), PHP_URL_HOST));

		if(!empty($recovery_urls)) {
			foreach($recovery_urls as $key => $recovery_url) {

				//swap previous domain in url from permalink settings
				$recovery_urls[] = str_replace($domain, $ultimatesocialshare['previous_domain'], $post_recovery_permalink);
			}
		}
		else {

			//swap previous domain in post permalink
			$recovery_urls[] = str_replace($domain, $ultimatesocialshare['previous_domain'], get_permalink($post_id));
		}
	}

	//post specific recovery urls
	$details = ultimatesocialshare_get_post_details($post_id);
	$post_recovery_urls = !empty($details['recovery_urls']) ? $details['recovery_urls'] : array();
	$recovery_urls = array_merge($recovery_urls, $post_recovery_urls);

	//remove current url from array if that was added for any reason
	$current_url = get_permalink($post_id);
	if(in_array($current_url, $recovery_urls)) {
		unset($recovery_urls[array_search($current_url, $recovery_urls)]);
		$recovery_urls = array_values($recovery_urls);
	}

	return $recovery_urls;
}

//get post permalink with the given permalink structure
function ultimatesocialshare_get_post_recovery_permalink($post_id = '', $permalink_structure = '') {

	if(empty($post_id) || empty($permalink_structure)) {
		return false;
	}

	//get post object
    $post = get_post($post_id);
 
    if(empty($post->ID)) {
        return false;
    }
 
    //filters the permalink structure for a post before token replacement occurs
    $permalink_structure = apply_filters('pre_post_link', $permalink_structure, $post, false);
 
    //make sure we actually need a custom permalink
    if($permalink_structure != 'plain' && !in_array($post->post_status, array('draft', 'pending', 'auto-draft', 'future'))) {

    	//rewrite tokens array
    	$rewrite_tokens = array(
	        '%year%',
	        '%monthnum%',
	        '%day%',
	        '%hour%',
	        '%minute%',
	        '%second%',
	        '%postname%',
	        '%post_id%',
	        '%category%',
	        '%author%',
	        '%pagename%',
	    );
 
        //category token
        $category = '';
        if(strpos($permalink_structure, '%category%') !== false) {

        	//get categories for post
            $cats = get_the_category($post->ID);
            if($cats) {
                $cats = wp_list_sort($cats, array(
                    'term_id' => 'ASC',
                ));
 
                //filters the category that gets used in the %category% permalink token
                $category_object = apply_filters('post_link_category', $cats[0], $cats, $post);
 
 				//get category object
                $category_object = get_term($category_object, 'category');
                $category = $category_object->slug;

                //parent category
                if($parent = $category_object->parent) {
                	$category = get_category_parents($parent, false, '/', true) . $category;
                }
            }
            
            //show default category if none was set
            if(empty($category)) {
                $default_category = get_term(get_option('default_category'), 'category');
                if($default_category && ! is_wp_error($default_category)) {
                    $category = $default_category->slug;
                }
            }
        }
 
 		//author token
        $author = '';
        if(strpos($permalink_structure, '%author%') !== false) {
            $authordata = get_userdata($post->post_author);
            $author = $authordata->user_nicename;
        }
 
 		//date tokens
        $date = explode(" ", date('Y m d H i s', strtotime($post->post_date)));

        //token replacements array
        $rewrite_replacements = array(
            $date[0],
            $date[1],
            $date[2],
            $date[3],
            $date[4],
            $date[5],
            $post->post_name,
            $post->ID,
            $category,
            $author,
            $post->post_name,
        );

        //replace tokens and set permalink
        $permalink = home_url(str_replace($rewrite_tokens, $rewrite_replacements, $permalink_structure));
    }
    else {
    	
    	//set basic permalink
        $permalink = home_url('?p=' . $post->ID);
    }
 
   	//filter the permalink for a post and return
    return apply_filters('post_link', $permalink, $post, false);
}