<?php
/*
Template Name: articles
*/

get_header('special'); 
$data=ftArt();
$post_ids=$data[0];
$mobileP=0;
if (strpos($_SERVER['HTTP_USER_AGENT'],'ndroid') || strpos($_SERVER['HTTP_USER_AGENT'],'IOS')){
	$mobileP=1;
}
?>



<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
<div id="interviews">
        <div class="container container-spacer">
                <div class="row justify-content-center text-justify interview-div">
				<h1 class="interviews-header-name">Статьи от TOP-100</h1>
				
				<?php 
				if($mobileP==0){
				foreach ($post_ids as $keyg=>$value) { ?> 
                    <div class="col-sm-12">   
                        <div class="row text-justify interview-rows scrolling-wrapper">
						<?php foreach ($post_ids[$keyg] as $key=>$value) { ?>
                            <a href="<?php echo $post_ids[$keyg][$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_ids[$keyg][$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $post_ids[$keyg][$key][5]; ?></h3>
                                        <span class="interview-person-desc"><?php echo $post_ids[$keyg][$key][6];?></span>
                                        <div class="interview-person-details row justify-content-center text-justify">
                                            <span class="interview-person-text col-4"><?php echo $post_ids[$keyg][$key][2];?></span>
                                            <span class="interview-person-text interview-person-view col-4"><?php echo $post_ids[$keyg][$key][4];?></span>
                                            <span class="interview-person-text interview-person-comment col-4"><?php echo $post_ids[$keyg][$key][3];?></span>
                                        </div>
                                        <span class="interview-person-text"><?php echo $post_ids[$keyg][$key][7]; ?></span>
                                    </div>
                            </a>
						<?php } ?>
                        </div>  
                    </div>
				<?php }}else{
				foreach ($post_ids as $keyg=>$value) { ?> 
                    <div class="col-sm-12">   
                        <div class="row justify-content-center text-justify interview-rows scrolling-wrapper">
						<?php foreach ($post_ids[$keyg] as $key=>$value) { ?>
                            <a href="<?php echo $post_ids[$keyg][$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_ids[$keyg][$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $post_ids[$keyg][$key][5]; ?></h3>
                                    </div>
                            </a>
						<?php } ?>
                        </div>  
                    </div>
				<?php }} ?>         
            </div>
        </div>
    </div>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php 
get_footer();
?>