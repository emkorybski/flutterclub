<script type="text/javascript">
    

  function vote ( option) {

    if( $type(option) != 'element' ) {
      return;
    }
    option = $(option);
    var value = option.value;
    $('poll_radio_' + option.value).toggleClass('poll_radio_loading');

    var request = new Request.JSON({
      url: '<?php echo $this->url(array('module'=>'news','controller'=>'index','action' => 'vote'),'default') ?>',
      method: 'post',
      data : {
        'format' : 'json',
        'option_id' : value,
        'content_id': <?php echo $this->new_content_id ;?>
      } ,
        onComplete:function(responseObject)
        {	
           
           $('poll_vote_total').innerHTML =  responseObject.vote_count_;
		   $('poll_vote_total_1').innerHTML =  responseObject.pct1;
		   $('poll_vote_total_2').innerHTML =  responseObject.pct2;

      }
    });
    request.send()
  };
</script>

<span class="poll_view_single">
 <form id="poll_form_1" action="<?php echo $this->url() ?>" method="POST" onsubmit="return true;">
    <ul id="poll_options_1" class="poll_options">
	    <?php 
			$new_content_id =$this->new_content_id;
			$option_vote_count_1 = Engine_Api::_()->news()->voteCountOption($new_content_id ,1);
			$option_vote_count_2 = Engine_Api::_()->news()->voteCountOption($new_content_id ,2);
			$pct1 = $this->vote_count ? floor(100*($option_vote_count_1/$this->vote_count))  : 0;
			$pct2 = $this->vote_count ? floor(100*($option_vote_count_2/$this->vote_count))  : 0;
			if (!$pct1) $pct1 = 1;
			if (!$pct2) $pct2 = 1;
	   ?>
      <?php foreach( $this->pollOptions as $i => $option ): ?>
      <li id="poll_item_option_<?php echo $option->poll_option_id ?>" style="padding-bottom:10px;">
        
        <div class="poll_not_voted" style=" margin-top:5px; margin-left:5px;"<?php echo ($this->hasVoted?'style="display:block;"':'') ?> >
        <div class="poll_radio" id="poll_radio_<?php echo $option->poll_option_id ?>">
            <input id="poll_option_<?php echo $option->poll_option_id ?>"
                   type="radio" name="poll_options" value="<?php echo $option->poll_option_id ?>"
                   onClick="vote(this);"
                   <?php if( $this->hasVoted == $option->poll_option_id ): ?>checked="true"<?php endif; ?>               
             />
        </div>
          <label for="poll_option_<?php echo $option->poll_option_id ?>">
            <?php echo $option->poll_option ?>
          </label>
        </div>
		<div class="poll_answer_total" id="poll_vote_total_<?php echo $option->poll_option_id ?>" style="font-weight: bold; margin-left:15px;margin-top:5px; ">
            <?php if($option->poll_option_id == 1){
						echo $this->translate(array('%1$s vote', '%1$s votes', $option_vote_count_1), $this->locale()->toNumber($option_vote_count_1));
						echo " (";
						echo $this->translate('%1$s%%', $this->locale()->toNumber($option_vote_count_1 ? $pct1 : 0));
						echo ")";
					}else{
						echo $this->translate(array('%1$s vote', '%1$s votes', $option_vote_count_2), $this->locale()->toNumber($option_vote_count_2));
						echo " (";
						echo $this->translate('%1$s%%', $this->locale()->toNumber($option_vote_count_2 ? $pct2 : 0));
						echo ")";
					}
			?>
          </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php if( empty($this->hideStats) ): ?>
    <div class="poll_stats">
     
      <span class="poll_vote_total" id="poll_vote_total" style="font-weight: bold;">
        <?php echo "Total: ".$this->translate(array('%s vote', '%s votes',$this->vote_count), $this->locale()->toNumber($this->vote_count)) ?>
      </span>    
    </div>
    <?php endif; ?>
  </form>
</span>
<style type="text/css">
.poll_view_single div.poll-answer-1 {
    background-color: #AAEA4F;
}
.poll_view_single div.poll-answer-2 {
    background-color: #EA4F4F;
}
</style>