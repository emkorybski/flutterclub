<?php
$this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/News/widgets/search-news/styles.css')
?>
<script type="text/javascript">
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>
  <div>
  <?php echo $this->form->render($this) ?>
 
</div>
<script>
    function selectedCategory()
    {    
        var obj = document.getElementById("category");
        for(i=0; i<obj.options.length; i++)
        {
            if(obj.options[i].value == <?php echo $_SESSION['keysearch']['category']?>)
            {
                obj.options[i].selected = true;
            }
        }
        var objInput =document.getElementById('search');
        if (objInput)
            objInput.value =  <?php if($_SESSION['keysearch']['keyword']!=""){ echo "'".$_SESSION['keysearch']['keyword']."'";}else{echo "''";}?>  ;
    }
    selectedCategory();

    function loadFeed(){
      var categoryparent_id = $('categoryparent').options[$('categoryparent').selectedIndex].value;
      //alert (categoryparent_id); die;
      var request = new Request.JSON({
      url: "news/loadfeed/"+categoryparent_id,
      method: 'post',
      data : {
        'format' : 'json',
        'categoryparent': categoryparent_id
      } ,
        onComplete:function(responseObject)
        {
            if(responseObject && responseObject.html != "" && responseObject.html != null)
            {
                document.getElementById('category-element').innerHTML ='<select id= "category" name = "category" style="width:160px;margin-bottom:5px;">' + responseObject.html + '</select>' ;
            }else{
                document.getElementById('category-element').innerHTML ='<select id= "category" name = "category" style="width:160px;margin-bottom:5px;">' + '<option value="-10" label="No Feed" selected= "selected"><?php echo $this->translate('No Feed') ?></option>'+ '</select>' ;
            }
        }
    });
    request.send()
         
        
}

</script>
<br/>
<style type="text/css">
div#filter_form, .filters, form#filter_form div.form-elements {
padding:0px !important;}
</style>