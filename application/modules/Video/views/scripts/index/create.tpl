<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: create.tpl 9675 2012-04-11 23:22:46Z richard $
 * @author     Jung
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';
    var validationUrl = '<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>';
    var validationErrorMessage = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"); ?>";
    var checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')) ?>';
    
    var current_code;
    
    var ignoreValidation = window.ignoreValidation = function() {
      $('upload-wrapper').style.display = "block";
      $('validation').style.display = "none";
      $('code').value = current_code;
      $('ignore').value = true;
    }
    
    var updateTextFields = window.updateTextFields = function() {
      var video_element = document.getElementById("type");
      var url_element = document.getElementById("url-wrapper");
      var file_element = document.getElementById("file-wrapper");
      var submit_element = document.getElementById("upload-wrapper");

      // clear url if input field on change
      //$('code').value = "";
      $('upload-wrapper').style.display = "none";

      // If video source is empty
      if( video_element.value == 0 ) {
        $('url').value = "";
        file_element.style.display = "none";
        url_element.style.display = "none";
        return;
      } else if( $('code').value && $('url').value ) {
        $('type-wrapper').style.display = "none";
        file_element.style.display = "none";
        $('upload-wrapper').style.display = "block";
        return;
      } else if( video_element.value == 1 || video_element.value == 2 ) {
        // If video source is youtube or vimeo
        $('url').value = "";
        $('code').value = "";
        file_element.style.display = "none";
        url_element.style.display = "block";
        return;
      } else if( video_element.value == 3 ) {
        // If video source is from computer
        $('url').value = "";
        $('code').value = "";
        file_element.style.display = "block";
        url_element.style.display = "none";
        return;
      } else if( $('id').value ) {
        // if there is video_id that means this form is returned from uploading 
        // because some other required field
        $('type-wrapper').style.display = "none";
        file_element.style.display = "none";
        $('upload-wrapper').style.display = "block";
        return;
      }
    }
    
    var video = window.video = {
      active : false,

      debug : false,

      currentUrl : null,

      currentTitle : null,

      currentDescription : null,

      currentImage : 0,

      currentImageSrc : null,

      imagesLoading : 0,

      images : [],

      maxAspect : (10 / 3), //(5 / 2), //3.1,

      minAspect : (3 / 10), //(2 / 5), //(1 / 3.1),

      minSize : 50,

      maxPixels : 500000,

      monitorInterval: null,

      monitorLastActivity : false,

      monitorDelay : 500,

      maxImageLoading : 5000,

      attach : function() {
        var bind = this;
        $('url').addEvent('keyup', function() {
          bind.monitorLastActivity = (new Date).valueOf();
        });

        var url_element = document.getElementById("url-element");
        var myElement = new Element("p");
        myElement.innerHTML = "test";
        myElement.addClass("description");
        myElement.id = "validation";
        myElement.style.display = "none";
        url_element.appendChild(myElement);

        var body = $('url');
        var lastBody = '';
        var lastMatch = '';
        var video_element = $('type');
        (function() {
          // Ignore if no change or url matches
          if( body.value == lastBody || bind.currentUrl ) {
            return;
          }

          // Ignore if delay not met yet
          if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay ) {
            return;
          }

          // Check for link
          var m = body.value.match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
          if( $type(m) && $type(m[0]) && lastMatch != m[0] ) {
            if (video_element.value == 1){
              video.youtube(body.value);
            } else {
              video.vimeo(body.value);
            }
          }
          lastBody = body.value;
        }).periodical(250);
      },

      youtube : function(url) {
        // extract v from url
        var myURI = new URI(url);
        var youtube_code = myURI.get('data')['v'];
        if( youtube_code === undefined ) {
          youtube_code = myURI.get('file');
        }
        if (youtube_code){
          (new Request.HTML({
            'format': 'html',
            'url' : validationUrl,
            'data' : {
              'ajax' : true,
              'code' : youtube_code,
              'type' : 'youtube'
            },
            'onRequest' : function(){
              $('validation').style.display = "block";
              $('validation').innerHTML = checkingUrlMessage;
              $('upload-wrapper').style.display = "none";
            },
            'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
              if( valid ) {
                $('upload-wrapper').style.display = "block";
                $('validation').style.display = "none";
                $('code').value = youtube_code;
              } else {
                $('upload-wrapper').style.display = "none";
                current_code = youtube_code;
                $('validation').innerHTML = validationErrorMessage;
              }
            }
          })).send();
        }
      },

      vimeo : function(url) {
        var myURI = new URI(url);
        var vimeo_code = myURI.get('file');
        if( vimeo_code.length > 0 ) {
          (new Request.HTML({
            'format': 'html',
            'url' : validationUrl,
            'data' : {
              'ajax' : true,
              'code' : vimeo_code,
              'type' : 'vimeo'
            },
            'onRequest' : function() {
              $('validation').style.display = "block";
              $('validation').innerHTML = checkingUrlMessage;
              $('upload-wrapper').style.display = "none";
            },
            'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
              if( valid ) {
                $('upload-wrapper').style.display = "block";
                $('validation').style.display = "none";
                $('code').value = vimeo_code;
              } else {
                $('upload-wrapper').style.display = "none";
                current_code = vimeo_code;
                $('validation').innerHTML = validationErrorMessage;
              }
            }
          })).send();
        }
      }
    }
    
    
    // Run stuff
    updateTextFields();
    video.attach();
    
    var autocompleter = new Autocompleter.Request.JSON('tags', tagsUrl, {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    
  });
</script>


<div class="headline">
  <h2>
    <?php echo $this->translate('Videos');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already uploaded the maximum number of videos allowed.');?>
      <?php echo $this->translate('If you would like to upload a new video, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'video_general'));?>
    </span>
  </div>
  <br/>
<?php else:?>
  <?php echo $this->form->render($this);?>
<?php endif; ?>
