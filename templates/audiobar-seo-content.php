<?php
// Alternative content for SEO
while ( have_posts() ) : the_post() ?>

			<div id="post-<?php the_ID() ?>">
				<h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute('echo=0') ?>" rel="bookmark"><?php the_title() ?></a></h2>
				<div class="entry-content">
<?php the_content(  ) ?>

				<?php wp_link_pages('before=<div class="page-link">&after=</div>') ?>
				</div>
			</div><!-- .post -->
<?php get_sidebar() ?>
<?php endwhile; ?>

