<div class="wrap">
  <h2>Nest Extend</h2>
  <p>Nest Extend provides a wrapper around the Nest API</p>
  <?php settings_errors(); ?>

  <form method="post" action="options.php">
    <?php
      settings_fields( 'nest_extend_option_group' );
      do_settings_sections( 'nest-extend-admin' );
      submit_button();
    ?>
  </form>
</div>
