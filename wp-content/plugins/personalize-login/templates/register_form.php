<div id="register-form" class="widecolumn">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Register', 'personalize-login' ); ?></h3>
    <?php endif; ?>
    <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
        <?php foreach ( $attributes['errors'] as $error ) : ?>
            <p class="login-error">
                <?php echo $error; ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
    <form id="signupform" action="<?php echo wp_registration_url(); ?>" method="post">
        <div class="form-row form-group">
            <label for="email"><?php _e( 'Email', 'personalize-login' ); ?> <strong>*</strong></label>
            <input type="text" class="form-control" name="email" id="email">
        </div>

        <div class="form-row form-group">
            <label for="first_name"><?php _e( 'First name', 'personalize-login' ); ?></label>
            <input type="text" class="form-control" name="first_name" id="first-name">
        </div>

        <div class="form-row form-group">
            <label for="last_name"><?php _e( 'Last name', 'personalize-login' ); ?></label>
            <input type="text" class="form-control" name="last_name" id="last-name">
        </div>

        <div class="form-row form-group">
            <?php _e( 'Note: Your password will be generated automatically and sent to your email address.', 'personalize-login' ); ?>
        </div>

        <div class="signup-submit">
            <button type="submit" class="btn btn-primary" name="submit" class="register-button"
                   value="<?php _e( 'Register', 'personalize-login' ); ?>">Register</button>
        </div>
    </form>
</div>