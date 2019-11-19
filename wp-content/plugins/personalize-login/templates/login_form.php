<div class="login-form-container">
    <form method="post" action="<?php echo wp_login_url(); ?>">
        <!-- Show errors if there are any -->
        <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
            <?php foreach ( $attributes['errors'] as $error ) : ?>
                <p class="login-error">
                    <?php echo $error; ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>
        <!-- Show logged out message if user just logged out -->
        <?php if ( $attributes['logged_out'] ) : ?>
            <p class="login-info">
                <?php _e( 'You have signed out. Would you like to sign in again?', 'personalize-login' ); ?>
            </p>
        <?php endif; ?>
        <?php if ( $attributes['registered'] ) : ?>
            <p class="login-info">
                <?php
                printf(
                    __( 'You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'personalize-login' ),
                    get_bloginfo( 'name' )
                );
                ?>
            </p>
        <?php endif; ?>
        <div class="login-username form-group">
            <label for="user_login"><?php _e( 'Email', 'personalize-login' ); ?></label>
            <input type="text" class="form-control" name="log" id="user_login">
        </div>
        <div class="login-password form-group">
            <label for="user_pass"><?php _e( 'Password', 'personalize-login' ); ?></label>
            <input type="password" class="form-control" name="pwd" id="user_pass">
        </div>
        <div class="login-submit">
            <button type="submit" class="btn btn-primary" value="<?php _e( 'Sign In', 'personalize-login' ); ?>">Sign In</button>
        </div>
        <a href="<?php echo site_url(); ?>/member-register/">Register</a>
    </form>
</div>