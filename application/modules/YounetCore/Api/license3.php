<?php
  $_SESSION['l3'] = "l3";
  $vars = <<<EOF
        <div class="yn_verify">
   <div class="settings">  
       
        <form method="post" action="" class="global_form" enctype="application/x-www-form-urlencoded" onsubmit="return false;" name="yn_verify_f" id="yn_verify_f">
        <div>
        <div>
            
             <h3>Enter your License Key for: {$this->token->m}</h3>
                    <div class="form-elements">
                        <div class="form-wrapper" id="controllersettings-wrapper">
                            <div class="form-label" id="controllersettings-label" style="width: 90px;">
                                <label class="required" for="controllersettings">License Key</label>
                            </div>
                            <div class="form-element" id="controllersettings-element">
                                <p class="description">Please enter your license key that was provided to you when you purchased this plugin. </p>
                                <span><input type="text" value="" id="l" name="l"><span class="mes unknown" name="m_yn" id="m_yn"></span></span>
                                <div id="loadding_yn" style="display:none;">
                                    <img src="externals/images/loading.gif" align="left" style="display: inline-block;float: left;height: 25px;margin-right: 15px;vertical-align: middle;" id="img_loadding"/>
                                </div>
                                <input type="hidden" value="{$this->token->m}" id="ynm" name="m">
                                <input type="hidden" value="{$this->token->tk}" id="yntk" name="tk">
                                <input type="hidden" value="{$this->token->d}" id="ynd" name="d">
                                <input type="hidden" value="{$this->token->ep}" id="ynep" name="ep">
                                <input type="hidden" value="{$this->token->time}" id="yntime" name="time">
                                <input type="hidden" value="" id="ls" name="ls">
                                <input type="hidden" value="license" id="t" name="t">
                                <div id="verify_status" style="margin-top:15px">
                                    
                                </div>
                                <div id="submit_yn" style="margin-top:15px">
                                <button name="done_verify" id="done_verify" type="submit">Verify</button> <button name="cancel_verify" id="cancel_verify" type="submit" onclick="return false;">Cancel</button>
                                
                                </div>
                            </div>
                        </div>
                        
                    </div>
                        </div>
                    </div>
        </form> 
   </div>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
  $('done_verify').removeEvents('click');
  $('done_verify').addEvent('click', function(event) {
         event.stop();
         $('ls').value = $('l').value;
         var req = new Request.JSON({
          method: 'post',
          url: '{$this->urlverify}',
          data: $('yn_verify_f'),
          onRequest: function() {request();  },
          onComplete: function(response) { 
              viewrequest();viewmessage(response.key); 
          },
          onFailure: function(){
              viewmessage(false);  
          },

        }).send();
      });
      $('cancel_verify').addEvent('click', function(event)
    {
        if(confirm("Are you sure to quit install?"))
        {
            window.location.href = "./install/manage"; 
        }
    });
  
});
function request()
{
     $('loadding_yn').show();$('l').hide();$('done_verify').hide();
     $('submit_yn').set('style','margin:0');
     $('m_yn').hide();
     $('cancel_verify').removeEvents('click');
     $('cancel_verify').addEvent('click', function(event)
     {
            viewrequest();  
     });
}    
function viewrequest()
{
    $('loadding_yn').hide();$('l').show();$('done_verify').show();
    $('submit_yn').set('style','margin-top:15px');
    $('m_yn').show();
    $('cancel_verify').removeEvents('click');
    $('cancel_verify').addEvent('click', function(event)
    {
        if(confirm("Are you sure to quit install?"))
        {
            window.location.href = "./install/manage"; 
        }
    });
}
function viewmessage(mes)
{
    if(mes == false)
    {
        $('m_yn').set('text','Invalid Key.');
        $('m_yn').set('class','mes invalid');
    }
    else
    {
        var svl = '<input type="hidden" name="svl" id="svl" value="'+mes+'">';
        $('yn_verify_f').set("html", $('yn_verify_f').get("html") + svl);
        $('m_yn').set('text','Validated.');
        $('m_yn').set('class','mes validated');
        $('done_verify').set('text','Continue');
        $('l').set('disabled', true);
        $('l').set('style', 'display:inline-block;background:#FAF8E9');
        $('l').value = $('ls').value;
        $('done_verify').removeEvents('click'); 
        $('done_verify').addEvent('click', function(event)
        {
            $('yn_verify_f').submit();
        });
    }
    
}
</script>
<style type="text/css">
    span.mes{
       font-weight: bold;
        padding-left:20px;
        margin-left:5px;
        font-size: 12px; 
    }
    span.validated{
        background:url('externals/images/notice.png') no-repeat center left;
       
    }
    span.invalid{
        background:url('externals/images/error.png') no-repeat center left;
        
    }
    .settings {
        clear: both;
        overflow: hidden;
    }
    .settings form {
        background-color: #E9F4FA;
        border-radius: 7px 7px 7px 7px;
        float: left;
        overflow: hidden;
        padding: 10px;
    }
    .settings form > div {
        background: none repeat scroll 0 0 #FFFFFF;
        border: 1px solid #D7E8F1;
        overflow: hidden;
        padding: 20px;
        max-width: 580px;
    }
    .settings h3 {
        margin-bottom: 12px;
        margin-left: -1px;
    }
    .settings .form-elements {
    overflow: hidden;
    }
    .settings .form-wrapper {
    border-top: 1px solid #EEEEEE;
    clear: both;
    overflow: hidden;
    padding: 15px 0;
}
.settings .form-label {
    clear: left;
    float: left;
    font-weight: bold;
    overflow: hidden;
    padding-right: 15px;
    width: 180px;
}
.settings .form-element {
    float: left;
    overflow: hidden;
}
.settings .form-element .description {
    margin: 0 0 10px;
    max-width: 400px;
    min-width: 300px;
    overflow: hidden;
}
.settings div.form-element input[type="text"] {
    width: 200px;
}
.package_query_title
{
    display:none;
}
.package_query_results ul
{
   margin:0;
   border :0;
}
.package_query_results li.error
{
    background:none;
}
</style>
EOF;
  if(!isset($this->_f))
  {
      $this->_error($vars);
  }
  
?>
