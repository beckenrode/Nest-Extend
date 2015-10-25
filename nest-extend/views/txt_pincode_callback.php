<?php
  printf(
   '<input class="regular-text" type="text" name="nest_extend_settings[txt_pincode]" id="txt_pincode" value="%s">',
   isset( $this->nest_extend_options['txt_pincode'] ) ? esc_attr( $this->nest_extend_options['txt_pincode']) : ''
  );
