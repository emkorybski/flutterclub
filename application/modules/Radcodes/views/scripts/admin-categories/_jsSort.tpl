<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('load', function() {
    SortablesInstance = new Sortables('div.radcodes_categories_lists ul', {
      clone: true,
      constrain: true,
      handle: 'img.move-me',
      opacity: 0.3, //default is 1
      
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

  var reorder = function(e) {
    
	     var categoryitems = e.parentNode.childNodes;
	     var ordering = {};
       
	     var i = 1;
	     for (var categoryitem in categoryitems)
	     {
	       var child_id = categoryitems[categoryitem].id;

	       if ((child_id != undefined) && (child_id.substr(0, 5) == 'admin'))
	       {
	         ordering[child_id] = i;
	         i++;
	       }
	     }
	    ordering['parent_id'] = e.parentNode.id.replace("admin_category_parent_","");
	    ordering['format'] = 'json';

	    // Send request
	    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
	    var request = new Request.JSON({
	      'url' : url,
	      'method' : 'POST',
	      'data' : ordering,
	      onSuccess : function(responseJSON) {
	      }
	    });

	    request.send();

	  }

  function ignoreDrag()
  {
    event.stopPropagation();
    return false;
  }

</script>

