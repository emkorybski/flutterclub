<script type="text/javascript">
//<![CDATA[
  window.addEvent('domready', function() {
    $('sort').addEvent('change', function(){
      $(this).getParent('form').submit();
    });
    
    var category_id = $('category_id');
    if( category_id != null ){
      category_id.addEvent('change', function(){
        $(this).getParent('form').submit();
      });
    }
  })
//]]>
</script>

<?php echo $this->searchForm->render($this) ?>