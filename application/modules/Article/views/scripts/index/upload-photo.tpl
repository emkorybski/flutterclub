<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#advanced_dlg.image_title}</title>
	<script type="text/javascript" src="<?php echo $this->baseUrl();?>/externals/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseUrl();?>/externals/tinymce/utils/mctabs.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseUrl();?>/externals/tinymce/utils/form_utils.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseUrl();?>/externals/tinymce/themes/advanced/js/image.js"></script>
</head>
<body id="image" style="display: none">
  <div class="tabs">
    <ul>
      <li id="general_tab" class="<?php if($this->status) echo'current'?>"><span><a href="<?php echo $this->baseUrl().'/externals/tinymce/themes/advanced/image.htm';?>" onmousedown="return false;">{#advanced_dlg.image_title}</a></span></li>
      <li id="general_tab" class="<?php if(!$this->status) echo'current'?>"><span><a href="<?php echo $this->baseUrl().'/externals/tinymce/themes/advanced/upload.htm';?>" onmousedown="return false;">Upload</a></span></li>
    </ul>
  </div>

  <form class="myform" onsubmit="ImageDialog.update();return false;" action="#" style="<?php if(!$this->status) echo'display:none;'?>">
          <div class="panel_wrapper">
                  <div id="general_panel" class="panel current">
       <table border="0" cellpadding="4" cellspacing="0">
            <tr>
              <td class="nowrap"><label for="src">{#advanced_dlg.image_src}</label></td>
              <td><table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><input id="src" name="src" type="text" class="mceFocus" value="<?php if($this->photo_url) echo $this->photo_url;?>" style="width: 200px" onchange="ImageDialog.getImageData();" /></td>
                    <td id="srcbrowsercontainer">&nbsp;</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td class="nowrap"><label for="alt">{#advanced_dlg.image_alt}</label></td>
              <td><input id="alt" name="alt" type="text" value="" style="width: 200px" /></td>
            </tr>
            <tr>
              <td class="nowrap"><label for="align">{#advanced_dlg.image_align}</label></td>
              <td><select id="align" name="align" onchange="ImageDialog.updateStyle();">
                  <option value="">{#not_set}</option>
                  <option value="baseline">{#advanced_dlg.image_align_baseline}</option>
                  <option value="top">{#advanced_dlg.image_align_top}</option>
                  <option value="middle">{#advanced_dlg.image_align_middle}</option>
                  <option value="bottom">{#advanced_dlg.image_align_bottom}</option>
                  <option value="text-top">{#advanced_dlg.image_align_texttop}</option>
                  <option value="text-bottom">{#advanced_dlg.image_align_textbottom}</option>
                  <option value="left">{#advanced_dlg.image_align_left}</option>
                  <option value="right">{#advanced_dlg.image_align_right}</option>
                </select></td>
            </tr>
            <tr>
              <td class="nowrap"><label for="width">{#advanced_dlg.image_dimensions}</label></td>
              <td><input id="width" name="width" type="text" value="" size="3" maxlength="5" />
                x
                <input id="height" name="height" type="text" value="" size="3" maxlength="5" /></td>
            </tr>
            <tr>
              <td class="nowrap"><label for="border">{#advanced_dlg.image_border}</label></td>
              <td><input id="border" name="border" type="text" value="" size="3" maxlength="3" onchange="ImageDialog.updateStyle();" /></td>
            </tr>
            <tr>
              <td class="nowrap"><label for="vspace">{#advanced_dlg.image_vspace}</label></td>
              <td><input id="vspace" name="vspace" type="text" value="" size="3" maxlength="3" onchange="ImageDialog.updateStyle();" /></td>
            </tr>
            <tr>
              <td class="nowrap"><label for="hspace">{#advanced_dlg.image_hspace}</label></td>
              <td><input id="hspace" name="hspace" type="text" value="" size="3" maxlength="3" onchange="ImageDialog.updateStyle();" /></td>
            </tr>
          </table>
                  </div>
          </div>
  </form>

  <div id="upload_panel" class="panel_wrapper" style="height: 200px;<?php if($this->status) echo'display:none;'?>">
    The image you tried to upload failed. Please try <a href="<?php echo $this->baseUrl().'/externals/tinymce/themes/advanced/upload.htm';?>">again</a>.
    <?php echo $this->error?>
  </div>

  <div class="mceActionPanel">
    <input type="submit" id="insert" name="insert" value="{#insert}" onclick="ImageDialog.update();return false;"/>
    <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
  </div>
</body>
</html>
