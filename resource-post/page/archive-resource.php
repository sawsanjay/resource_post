 
<?php
get_header(); 
?>
<div class="container">
	<div class="resource_heading">
		<div class="row">
			<div class="col-md-6">
				<div class="bread_with_serch d-flex" style="text-align: right;">
					<form>
                        <div class="resource_serch">
                            <input type="text" class="form-control" name="Search"   id='s' placeholder="Search the resource">
                            <button class="resource_serch_btn" id="resource_search" type="submit" value="Search">Search</button>
                        </div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="resource_cat_main ">
    <label>Categories : </label>
    <?php
        $args = array( 'taxonomy' => 'resource_type', 'orderby' => 'name', 'order' => 'ASC' );
        $categories = get_categories($args);
            foreach($categories as $category) { ?> 
                <a href="#" id="resource_category"  class="cat_name" data-id = "<?php echo $category->term_id; ?>"><?php echo $category->name; ?></a>
    <?php   }  ?> 
	</div>
	<div class="resource_cat_select d-md-none" style="text-align: right;">
		<select id="mobile_category" class="form-select">
                <option selected data-id = "all" >Open All Topics</option>
                <?php
                        $args = array( 'taxonomy' => 'resource_topic', 'orderby' => 'name', 'order' => 'ASC' );
                        $categories = get_categories($args);
                        $n = 1;
                        foreach($categories as $category) { ?> 
                        <option value="<?php echo $n; ?>"  data-id = "<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                    <?php  
                    $n++;
                        }  ?> 
		
		</select>
	</div>
</div>
<!-- resource list section -->
<div class="resource_list">
	<div class="container">
		<div class="row posts-area">
			
		</div>
		<div class="d-flex justify-content-center " style="text-align: center;">
            <a href="#"   id="resource_load_ready"  class="btn-outline-primary resource_loadmore_ready"><span>Load more<i class="fa-solid fa-arrow-down ms-2"></i></span></a>
			<a href="#"  id="resource_search_load" class="btn-outline-primary resource_search_loadmore"><span>Load more<i class="fa-solid fa-arrow-down ms-2"></i></span></a>
			<a href="#"  id="resource_category_load" class="btn-outline-primary cat_loadmore"><span>Load more<i class="fa-solid fa-arrow-down ms-2"></i></span></a>
            <a href="#"  id="mobile_category_load" class="btn-outline-primary mobile_cat_loadmore"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>
		</div>
	</div>
</div>
<script>
    jQuery(document).ready(function() {
        window.rbl_page = 2;
       jQuery('#resource_search_load').hide();
       jQuery('#resource_category_load').hide();
       jQuery('#mobile_category_load').hide();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: "html",
            data: {
                action: 'get_ajax_posts', 'post_type':'resource', 'task': 'ready_resource'
            },
            beforeSend: function(xhr) {
                jQuery('.load_more').toggleClass('d-none d-flex');
                jQuery('.load_more').html('<span class="spinner-grow text-primary" role="status"></span>');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="resource_load_ready" class="btn-outline-primary resource_loadmore_ready"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');
                jQuery('.posts-area').html(response);
                var vs_count = jQuery('.r_count').data('id');
                var only_six = jQuery('.only_six').data('id');
                var for_six_vs = jQuery('.mlc_not_found').data('id');
                if (vs_count < 9) {
                    jQuery('.resource_loadmore_ready').hide();
                } else {
                    if (for_six_vs == 0) {
                        jQuery('.resource_loadmore_ready').hide();
                    } else {
                    if(only_six == 9){
                            jQuery('.resource_loadmore_ready').hide();
                        } else {
                            jQuery('.resource_loadmore_ready').show();
                        }
                    }
                }
            }

        });
        return false;
    });

    jQuery(document).on('click', '#resource_load_ready', function() {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: "html",
            data: {
                action: 'get_ajax_posts', 'post_type':'resource', 'm_page': rbl_page, 'task': 'ready_resource'
            }, 
            beforeSend: function(xhr) {
                jQuery('.load_more').html('<span class="spinner-border text-primary" role="status"></span>');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="resource_load_ready" class="btn-outline-primary resource_loadmore_ready"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');
                jQuery('.posts-area').append(response);
                var vs_count = jQuery('.r_count'+rbl_page).data('id');
                var for_six_vs = jQuery('.mlc_not_found').data('id');
                if (vs_count < 9) {
                    jQuery('.resource_loadmore_ready').hide();
                } else {
                    if (for_six_vs == 0) {
                        jQuery('.resource_loadmore_ready').hide();
                    } else {
                        jQuery('.resource_loadmore_ready').show();
                    }
                }
                rbl_page++;
            }
        });
        return false;
    });

    jQuery( document ).on( 'click', '#resource_category', function() {
        
        window.cat_id = jQuery(this).data('id');
        window.cat_page = 2;
        jQuery('#resource_load_ready').hide();
        jQuery('#resource_search_load').hide();
        jQuery('#mobile_category_load').hide();
        jQuery('.cat_loadmore').show();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php');?>',
            dataType: "html", 
            data: { action : 'get_ajax_posts', 'cate_id': cat_id, 'task': 'resource_cat'},
            beforeSend: function(xhr) {
                jQuery('.posts-area').html('<div class="container text-center"><span class="spinner-grow text-primary" role="status"></span></div>');
                jQuery('.load_more').html('');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="resource_category_load" class="btn-outline-primary cat_loadmore"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');
                jQuery( '.posts-area' ).html( response ); 
                    var bc1_count = jQuery('.r_count').data('id');
                    var only_six = jQuery('.only_six').data('id');
                    var for_six_bc1 = jQuery('.blc_not_found').data('id');
                    if(bc1_count < 9){
                        jQuery('.cat_loadmore').hide();
                    }
                    else{
                        if(for_six_bc1 == 0){
                            jQuery('.cat_loadmore').hide();
                        }
                        else{
                            if(only_six == 9){
                                    jQuery('.cat_loadmore').hide();
                            } else {
                                jQuery('.cat_loadmore').show();
                            }
                        }
                    }
                }
        });
        return false;
    });

    jQuery( document ).on( 'click', '#resource_category_load', function() {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php');?>',
            dataType: "html", 
            data: { action : 'get_ajax_posts', 'cate_id': cat_id, 'cat_page': cat_page, 'task': 'resource_cat'},
            beforeSend: function(xhr) {
                jQuery('.load_more').html('<span class="spinner-border text-primary" role="status"></span>');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="resource_category_load" class="btn-outline-primary cat_loadmore"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');
                jQuery( '.posts-area' ).append( response ); 
                var bc2_count = jQuery('.r_count'+cat_page).data('id');
                    var for_six_bc2 = jQuery('.blc_not_found').data('id');
                    if(bc2_count < 9){
                        jQuery('.cat_loadmore').hide();
                    }
                    else{
                        if(for_six_bc2 == 0){
                            jQuery('.cat_loadmore').hide();
                        }
                        else{
                            jQuery('.cat_loadmore').show();
                        }
                    }
                cat_page++;
                }
        });
        return false;
    });


    jQuery( document ).on( 'click', '#resource_search', function() {
    var search_text = jQuery('#s').val();
    window.page = 2;
        jQuery('#resource_load_ready').hide();
        jQuery('.resource_search_loadmore').show();
        jQuery('#mobile_category_load').hide();
        jQuery('.cat_loadmore').hide();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php');?>',
            dataType: "html", 
            data: { action : 'get_ajax_posts', 'search_value': search_text, 'task': 'search_resource'},
            beforeSend: function(xhr) {
                jQuery('.posts-area').html('<div class="container text-center"><span class="spinner-grow text-primary" role="status"></span></div>');
                jQuery('.load_more').html('');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="resource_search_load" class="btn-outline-primary resource_search_loadmore"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');                
                jQuery('#mobile_category').prop('selectedIndex',0);
                jQuery( '.posts-area' ).html( response );
                jQuery("#resource_all_cat").addClass("active");
                jQuery(".cat_name").removeClass("active");
                var bs1_count = jQuery('.r_count').data('id');
                var only_six = jQuery('.only_six').data('id');
                var for_six_bs1 = jQuery('.bls_not_found').data('id');
                if(bs1_count < 9){
                    jQuery('.resource_search_loadmore').hide();
                }
                else{
                    if(for_six_bs1 == 0){
                        jQuery('.resource_search_loadmore').hide();
                    }
                    else{
                        if(only_six == 9){
                            jQuery('.resource_search_loadmore').hide();
                        } else {
                            jQuery('.resource_search_loadmore').show();
                        }
                    }
                }
            }
        });
        return false;
    });

    jQuery( document ).on( 'click', '#resource_search_load', function() {
    var search_text = jQuery('#s').val();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php');?>',
            dataType: "html", 
            data: { action : 'get_ajax_posts', 'search_value': search_text, 'page': page, 'task': 'search_resource'},
            beforeSend: function(xhr) {
                jQuery('.load_more').html('<span class="spinner-border text-primary" role="status"></span>');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="resource_search_load" class="btn-outline-primary resource_search_loadmore"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');
                jQuery( '.posts-area' ).append( response );
                var bs2_count = jQuery('.r_count'+page).data('id');
                var for_six_bs2 = jQuery('.bls_not_found').data('id');
                if(bs2_count < 9){
                    jQuery('.resource_search_loadmore').hide();
                }
                else{
                    if(for_six_bs2 == 0){
                        jQuery('.resource_search_loadmore').hide();
                    }
                    else{
                        jQuery('.resource_search_loadmore').show();
                    }
                }
                page++;	
            }
        });
        return false;
    });

    jQuery( document ).on( 'change','#mobile_category', function() {
        var cat_id = jQuery(this).find(':selected').data('id');
        window.m_page = 2;
        jQuery('#resource_load_ready').hide();
        jQuery('.resource_search_loadmore').hide();
        jQuery('.mobile_category_load').show();
        jQuery('.cat_loadmore').hide();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php');?>',
            dataType: "html", 
            data: { action : 'get_ajax_posts', 'cate_id': cat_id, 'task': 'resource_topic'},
            beforeSend: function(xhr) {
                jQuery('.posts-area').html('<div class="container text-center"><span class="spinner-grow text-primary" role="status"></span></div>');
                jQuery('.load_more').html('');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="mobile_category_load" class="btn-outline-primary mobile_cat_loadmore"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');
                jQuery( '.posts-area' ).html( response ); 
                    var ms1_count = jQuery('.r_count').data('id');
                    var only_six = jQuery('.only_six').data('id');
                    var for_six_ms1 = jQuery('.mlc_not_found').data('id');
                    if(ms1_count < 9){
                        jQuery('.mobile_cat_loadmore').hide();
                    }
                    else{
                        if(for_six_ms1 == 0){
                            jQuery('.mobile_cat_loadmore').hide();
                        }
                        else{
                            if(only_six == 9){
                                jQuery('.mobile_cat_loadmore').hide();
                            } else {
                                jQuery('.mobile_cat_loadmore').show();
                                }
                        }
                    }
                }
        });
        return false;
    });


    jQuery( document ).on( 'click','#mobile_category_load', function() {
        var cat_id = jQuery("#mobile_category").find(':selected').data('id');
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php');?>',
            dataType: "html", 
            data: { action : 'get_ajax_posts', 'cate_id': cat_id, 'm_page': m_page, 'task': 'resource_topic'},
            beforeSend: function(xhr) {
                jQuery('.load_more').html('<span class="spinner-border text-primary" role="status"></span>');
            },
            success: function(response) {
                jQuery('.load_more').html('<a  id="mobile_category_load" class="btn-outline-primary mobile_cat_loadmore"><span>Load More<i class="fa-solid fa-arrow-down ms-2"></i></span></a>');
                jQuery( '.posts-area' ).append( response );
                var ms2_count = jQuery('.r_count'+m_page).data('id');
                    var for_six_ms2 = jQuery('.mlc_not_found').data('id');
                    if(ms2_count < 6){
                        jQuery('.mobile_cat_loadmore').hide();
                    }
                    else{
                        if(for_six_ms2 == 0){
                            jQuery('.mobile_cat_loadmore').hide();
                        }
                        else{
                            jQuery('.mobile_cat_loadmore').show();
                        }
                    }
                m_page++;
                }
        });
        return false;
    });
    jQuery( document ).on( 'click','.cat_name', function() {
        jQuery("#resource_all_cat").removeClass("active");
        jQuery(".cat_name").removeClass("active");
        jQuery(this).addClass("active");
        return false;
    });
</script>
<?php get_footer();