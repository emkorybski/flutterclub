<?php

$this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
//  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/all.js');
?>
<div id="fb-root"></div>
<div id="FB_HiddenContainer"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
    <p>
    <input type="button"
      onclick="sendRequestViaMultiFriendSelector(); return false;"
      value="Send Request to Many Users with MFS"
    />
    </p>

<script type="text/javascript">
en4.core.runonce.add(function(){
    /*
    var appId = <?php echo $this->jsonInline($this->appId); ?>;

    window.fbAsyncInit = function() {
        FB.init({
        appId  : appId,
        status : true, // check login status
        cookie : true, // enable cookies to allow the server to access the session
        xfbml  : true  // parse XFBML
      });
    };
*/
    /*
    (function() {
      var e = document.createElement('script'); e.async = true;
      e.src = document.location.protocol + '//connect.facebook.net/<?php //echo $this->getFacebookLocale(); ?>/all.js';
      document.getElementById('fb-root').appendChild(e);
    }());
    */

});
function sendRequestViaMultiFriendSelector() {
    var app_id = <?php echo $this->jsonInline($this->appId); ?>;
    FB.init({
            appId  : 228813370504808,
//            status : true, // check login status
//            cookie : true, // enable cookies to allow the server to access the session
            xfbml  : true,  // parse XFBML
//            oauth: true
          });
    FB.ui({
            method: 'apprequests',
            message: 'Just test invitation'
    }, requestCallback);
}

function requestCallback(response) {
//    console.log(response);
}
</script>

