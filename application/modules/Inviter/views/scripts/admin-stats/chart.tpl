<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h2><?php echo $this->translate('INVITER_View Statistics') ?></h2>
<p><?php echo $this->translate("INVITER_VIEWS_SCRIPTS_ADMINSTATS_CHART_DESCRIPTION") ?></p>
<br/>

<div class="admin_search">
  <div class="search">
    <?php echo $this->filterForm->render($this) ?>
  </div>
</div>
<br />

<div class="admin_statistics">
  <div class="admin_statistics_nav">
    <a id="admin_stats_offset_previous" onclick="processStatisticsPage(-1);"><?php echo $this->translate("Previous") ?></a>
    <a id="admin_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
  </div>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>

<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(onloadGoogle);

    var currentArgs = {};
    var processStatisticsFilter = function(formElement) {
      var vals = formElement.toQueryString().parseQueryString();
      vals.offset = 0;
      buildStatisticsSwiff(vals);
      return false;
    }

    var processStatisticsPage = function(count) {
      var args = $merge(currentArgs);
      args.offset += count;
      buildStatisticsSwiff(args);
    }

    var buildStatisticsSwiff = function(args) {
      currentArgs = args;

      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

      var url = '<?php echo $this->url(array('module'=>'inviter', 'controller'=>'stats', 'action' => 'chart-data'), 'admin_default', true) ?>';
			currentArgs.format = 'json';
			
			new Request.JSON({
				'url':url,
				'method':'post',
				'data':currentArgs,
				'onSuccess':function($resp){
				
					if ($resp != null) {
						var data = new google.visualization.DataTable();
						data.addColumn('string', en4.core.language.translate('INVITER_Invites Date'));

						switch(currentArgs.type)
						{
							case 'inviter.sents,inviter.referreds':
									data.addColumn('number', en4.core.language.translate('INVITER_Sent Invites'));
									data.addColumn('number', en4.core.language.translate('INVITER_Referred Invites'));
									$colors = ['#3366CC', 'red'];
								break;

							case 'inviter.sents':
									data.addColumn('number', en4.core.language.translate('INVITER_Sent Invites'));
									$colors = ['#3366CC'];
								break;

							case 'inviter.referreds':
									data.addColumn('number', en4.core.language.translate('INVITER_Referred Invites'));
									$colors = ['red'];
								break;
						}

						data.addRows($resp.data);
						var chart = new google.visualization.AreaChart(document.getElementById('my_chart'));
						chart.draw(data, {width: 1000,
															height: 400,
															title: $resp.title,
															legend: 'bottom',
															pointSize: 4,
															colors:$colors,
											 });
					}
				},
			}).send();
    }

    /* OFC */
    var ofcIsReady = false;
    function ofc_ready()
    {
      ofcIsReady = true;
    }
 

		function onloadGoogle(){
      buildStatisticsSwiff({
        'type' :'inviter.sents,inviter.referreds',
        'mode' : 'normal',
        'chunk' : 'dd',
        'period' : 'ww',
        'start' : 0,
        'offset' : 0
      });
    }
  </script>

  <div id="my_chart"></div>
</div>