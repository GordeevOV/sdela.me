<?php global $dm_settings; ?>

<div class="container dmbs-container">

<?php if ($dm_settings['show_header'] != 0) : ?>

    <div class="row dmbs-header">

        <?php if ( get_header_image() != '' || get_header_textcolor() != 'blank') : ?>

        <?php if ( get_header_image() != '' ) : ?>
            <div class="col-md-4 dmbs-header-img dmbs-header-text">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" /></a>
            
            <?php if ( get_header_textcolor() != 'blank' ) : ?>
                <h1><a class="custom-header-text-color" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
                <h4 class="custom-header-text-color"><?php bloginfo( 'description' ); ?></h4>
            <?php endif; ?>
            <?php else : ?>
                <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
                <h4><?php bloginfo( 'description' ); ?></h4>
            <?php endif; ?>
        <?php endif; ?>
		</div>
		
		
        <div class="col-md-6 dmbs-header-text">
        
         <?php if ( has_nav_menu( 'main_menu' ) ) : ?>   
		    <div class="row dmbs-top-menu">
		        <nav class="navbar" role="navigation">
		            <div>
		                <div class="navbar-header">
		                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-1-collapse">
		                        <span class="sr-only"><?php _e('Toggle navigation','devdmbootstrap3'); ?></span>
		                        <span class="icon-bar"></span>
		                        <span class="icon-bar"></span>
		                        <span class="icon-bar"></span>
		                    </button>
		                </div>

		                <?php
		                wp_nav_menu( array(
		                        'theme_location'    => 'main_menu',
		                        'depth'             => 2,
		                        'container'         => 'div',
		                        'container_class'   => 'collapse navbar-collapse navbar-1-collapse',
		                        'menu_class'        => 'nav navbar-nav',
		                        'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
		                        'walker'            => new wp_bootstrap_navwalker())
		                );
		                ?>
		            </div>
		        </nav>
		    </div>

		<?php endif; ?>
        </div>

		<div class="col-md-2 dmbs-header-text registration">
			<img src="<?php echo get_template_directory_uri();?>/img/logo2.jpg" alt="" />
			<div>
			<?php if ( is_user_logged_in() ) {
			?>
				<a href="/your-profile/">Профиль</a>
				<a href="/logout/">Выйти</a>
			<?php }
				else {?>
				<a href="javascript:LPopupShow();">Вход</a>
				<a href="javascript:PopupShow();">Регистрация</a>	
			<?php	}
			?>
			</div>
		</div>

    </div>

<?php endif; ?>
</div>