<noscript>
  <div style="width: 302px; height: 495px; margin-bottom: 20px; margin-left: -15px;">
    <div style="width: 302px; height: 425px; position: relative;">
      <div style="width: 302px; height: 425px; position: absolute;">
        <iframe src="https://www.google.com/recaptcha/api/fallback?k=<?php echo get_option( 'wr_no_captcha_site_key' ); ?>"
                frameborder="0" scrolling="no"
                style="width: 302px; height:425px; border-style: none;">
        </iframe>
      </div>
      <div style="width: 300px; height: 60px; border-style: none;
                  bottom: 12px; left: 1px; margin: 0px; padding: 0px; right: 1px;
                  background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px; position: absolute; top: 435px;">
        <textarea id="g-recaptcha-response" name="g-recaptcha-response"
                  class="g-recaptcha-response"
                  style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
                         margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
      </div>
    </div>
  </div>
</noscript>