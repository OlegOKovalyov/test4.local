<!-- Создание login формы для входа с помощью WordPress функции wp_login_form() -->
<!--<div class="login-form-container">
    <?php /*if ( $attributes['show_title'] ) : */?>
        <h2><?php /*_e( 'Sign In', 'personalize-login' ); */?></h2>
    <?php /*endif; */?>

    <?php
/*    wp_login_form(
        array(
            'label_username' => __( 'Email', 'personalize-login' ),
            'label_log_in' => __( 'Sign In', 'personalize-login' ),
            'redirect' => $attributes['redirect'],
        )
    );
    */?>

    <a class="forgot-password" href="<?php /*echo wp_lostpassword_url(); */?>">
        <?php /*_e( 'Forgot your password?', 'personalize-login' ); */?>
    </a>
</div>-->

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
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck1">
            <label class="form-check-label" for="exampleCheck1">Remember Me</label>
        </div>
        <div class="login-submit">
<!--            <input type="submit" value="--><?php //_e( 'Sign In', 'personalize-login' ); ?><!--">-->
            <button type="submit" class="btn btn-primary" value="<?php _e( 'Sign In', 'personalize-login' ); ?>">Sign In</button>
        </div>
        <a class="forgot-password" href="<?php echo wp_lostpassword_url(); ?>">
            <?php _e( 'Forgot your password?', 'personalize-login' ); ?></a>
        <a href="/member-register/">Register</a>
    </form>
</div>