<?php
class favicon
{
    var $ver = '1.1';   
    var $site_url = ''; # url of site
    var $if_modified_since = 0; # cache
    var $is_not_modified = false;
    var $ico_type = 'ico'; # ico, gif or png only
    var $ico_url = ''; # full uri to favicon
    var $ico_exists = 'not checked'; # no comments
    var $ico_data = ''; # ico binary data
    var $output_data = ''; # output image binary data

    # main proc
    function favicon($site_url, $if_modified_since = 0)
    {       
        $site_url = trim(str_replace('http://', '', trim($site_url)), '/');
        $site_url = explode('/', $site_url);
        $site_url = 'http://' . $site_url[0] . '/';
        $this->site_url = $site_url;
        $this->if_modified_since = $if_modified_since;
        $this->get_ico_url();
        $this->is_ico_exists();
        $this->get_ico_data();
    }

    # get uri of favicon
    function get_ico_url()
    {
       
        if ($this->ico_url == '')
        {
            $this->ico_url = $this->site_url . 'favicon.ico';

            # get html of page
            $h = @fopen($this->site_url, 'r');
            if ($h)
            {
                
                $html = '';
                while (!feof($h) and !preg_match('/<([s]*)body([^>]*)>/i', $html))
                {
                    $html .= fread($h, 200);
                }
                fclose($h);
                //$html = '<link href="http://vietchinabusiness.vn/phim/skin/anhtrang_org/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />';
                # search need <link> tag
                if (preg_match('/<link([^>]*)(rel="icon"|rel="shortcut icon")([^>]*)\/>/iU', $html, $out))
                //if (preg_match('/<link.*(rel="icon"|rel="shortcut icon") href="(.*)".*\/>/i', $html, $out))  
                {    
                    //echo 'go here1';
                    // print_r($out);
                    foreach($out as $o)
                    {
                        if ( strpos($o,'<link') === false)
                        {
                            if (preg_match('/href([s]*)=([s]*)"([^"]*)"/iU', $o, $link_tag) )
                           {
                              // print_r($link_tag);
                               foreach ($link_tag as $link)
                               {
                                   if ( strpos($link,'href')===false && !empty($link))
                                   {
                                        $this->ico_type = (!(strpos($link, 'png')===false)) ? 'png' : 'ico';
                                        $ico_href = trim($link);
                                        if (strpos($ico_href, 'http://')===false)
                                        {
                                            $ico_href = rtrim($this->site_url, '/') . '/' . ltrim($ico_href, '/');
                                        }
                                        $this->ico_url = $ico_href;
                                        
                                        return $this->ico_url;
                                        //echo $this->ico_url;
                                        //die('0d');
                                        break;    
                                   }
                                      
                               }
                                
                           } 
                        }
                       
                    }
                    //die('1');
                    //$link_tag = $out[2];
                    //echo $link_tag;
                    //if (preg_match('/href([s]*)=([s]*)"([^"]*)"/iU', $link_tag, $out))
                    //{
                    
                       
                    //}
                }
            }           
        }
        return $this->ico_url;
    }

    # check that favicon is exists
    function is_ico_exists()
    {
        if ($this->ico_exists=='not checked')
        {
            $h = @fopen($this->ico_url, 'r');
            $this->ico_exists = ($h) ? true : false;
            if ($h) fclose($h);
        }
        return $this->ico_exists;
    }

    # get ico data
    function get_ico_data()
    {

    if ($this->ico_data=='' && $this->ico_url!=''
&& $this->ico_exists && !$this->is_not_modified)
        {
            # get ico data
            $ico_z = parse_url($this->ico_url);
            $ico_path = $ico_z['path'];
            $ico_host = $ico_z['host'];
            $ico_ims = gmdate('D, d M Y H:i:s', $this->if_modified_since) . ' GMT';

            $fp = @fsockopen($ico_host, 80);
           
            if ($fp)
            {
                $out = "GET {$ico_path} HTTP/1.1\r\n";
                $out .= "Host: {$ico_host}\r\n";

            $out .= "User-Agent=Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13\r\n";
                $out .= "If-Modified-Since: {$ico_ims}\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                $data = '';
                while (!feof($fp)) $data .= fgets($fp, 128);
                fclose($fp);
                //header("Content-type: image/png");
                $response = substr($data, 0, 15);
                $this->is_not_modified = !(strpos($response, '304')===false);
                if ($this->is_not_modified)
                {
                    $this->ico_data = 'not modified';                   
                }
                else
                {
                    $data = explode("\r\n\r\n", $data);
                   
                    if (count($data)>0)
                    {
                        unset($data[0]); 
                        $this->ico_data = implode("\r\n\r\n", $data);
                    }
                }
                $this->output_data = '';
            }
        }
        return $this->ico_data;
    }

    # get output data
    function get_output_data()
    {
        if ($this->output_data=='')
        {
           
            if ($this->ico_data=='not modified')
            {
               
                # icon is not modified since defined time
                $this->output_data = 'not modified';
            }
            elseif($this->ico_data=='')
            {
               
                # error(s) in getting icon data
                $this->output_data = $this->empty_png();
            }
            else
            {
                
                # convert ico to png, gif & return
                if (substr($this->ico_data, 0, 3)==='GIF') $this->ico_type = 'gif';
                $this->output_data = $this->ico_data;
                if ($this->ico_type==='ico')
                {
                    $this->output_data = $this->ico2png($this->output_data);
                }
                if ($this->ico_type==='gif')
                {
                    $this->output_data = $this->gif2png($this->output_data);
                }
            }
        }
        return $this->output_data;
    }    

    # if error or icon is not found we output empty png image
    function empty_png()
    {
        $res = '';
        $im = imagecreatetruecolor(16, 16);
        $color = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 1, 1, $color);

        # output png
        ob_start();

        # imagesavealpha($im, true);
        imagepng($im);
        imagedestroy($im);
        $res = ob_get_clean();
        return $res;   
    }

    # Convert gif to png function,
    # support gif-functions by GD is needed
    function gif2png($gif)
    {
        $im2 = imagecreatefromstring($gif); 

        # background alpha is disabled because IE 5.5 + have bug with alpha-channels
        # by default background color is white
        # imagealphablending($im, false);
        # imagefilledrectangle($im, 0, 0, 16, 16, $color);
        # imagealphablending($im, true);
        $im = imagecreatetruecolor(16, 16);
        $color = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 1, 1, $color);
        imagecopy($im, $im2, 0, 0, 0, 0, 16, 16);        

        # output png
        ob_start();
        # imagesavealpha($im, true);
        imagepng($im);
        imagedestroy($im);
        imagedestroy($im2);
        $res = ob_get_clean();
        return $res;
    }

    # Convert ico to png function,
    # information about ico format is accessible on a site http://kainsk.tomsk.ru/g2003/sys26/oswin.htm,
    function ico2png($ico)
    {
        $res = '';
         
        while(!isset($tmp))
        {
            $tmp = '';

            # get ICONDIR struct & check that it is correct ico format
            $icondir = unpack('sidReserved/sidType/sidCount', substr($ico, 0, 6));
            if ($icondir['idReserved']!=0 || $icondir['idType']!=1 || $icondir['idCount']<1) break;
            $icondir['idEntries'] = array();
            $entry = array();
            for($i=0; $i<$icondir['idCount']; $i++)
            {

            $entry =
unpack('CbWidth/CbHeight/CbColorCount/CbReserved/swPlanes/swBitCount/LdwBytesInRes/LdwImageOffset',
substr($ico, 6 + $i*16, 16));
                $icondir['idEntries'][] = $entry;
            }

            # select need icon & get it raw data
            $iconres = '';
            $bpx = 1; # bits per pixel
            $idx = 0; # index of need icon
            foreach($icondir['idEntries'] as $k=>$entry)
            {

            if ($entry['bWidth']==16 &&
isset($entry['swBitCount']) && $entry['swBitCount']>$bpx
&& $entry['swBitCount']<33)
                {
                    $idx = $k;
                    $bpx = $entry['swBitCount'];
                }
            }           
            $iconres = substr($ico, $icondir['idEntries'][$idx]['dwImageOffset'], $icondir['idEntries'][$idx]['dwBytesInRes']);
            unset($ico);
            unset($icondir);

            # getting bitmap info
            $bitmap_info = array();

        $bitmap_info['header'] =
unpack('LbiSize/LbiWidth/LbiHeight/SbiPlanes/SbiBitCount/LbiCompression/LbiSizeImage/LbiXPelsPerMeter/LbiYPelsPerMeter/LbiClrUsed/LbiClrImportant',
substr($iconres, 0, 40));

            $bitmap_info['header']['biHeight'] = $bitmap_info['header']['biHeight'] / 2;           
            $number_color = 0;

            if ($bitmap_info['header']['biBitCount'] > 16)
            {
                $number_color = 0;

            $sizecolor =
$bitmap_info['header']['biWidth']*$bitmap_info['header']['biBitCount']
* $bitmap_info['header']['biHeight'] / 8;  
            }
            elseif ( $bitmap_info['header']['biBitCount'] < 16)
            {
                $number_color = (int) pow(2, $bitmap_info['header']['biBitCount']);

            $sizecolor =
$bitmap_info['header']['biWidth']*$bitmap_info['header']['biBitCount']
* $bitmap_info['header']['biHeight'] / 8;  
                if ($bitmap_info['header']['biBitCount']=='1') $sizecolor = $sizecolor * 2;
            }
            else return $res;

            $rgb_table_size =  4 * $number_color;       
            for($i=0; $i<$number_color; $i++)
            {
                $bitmap_info['colors'][] = unpack('CrgbBlue/CrgbGreen/CrgbRed/CrgbReserved', substr($iconres, 40 + $i*4, 4));
            }
            $current_offset = 40 + $number_color * 4;

            $arraycolor = array();

            for($i=0; $i<$sizecolor; $i++)
            {
                $value = unpack('Cvalue', substr($iconres, $current_offset, 1));
                $arraycolor[] = $value['value'];
                $current_offset++;
            }

            # background alpha is disabled because IE 5.5 + have bug with alpha-channels
            # by default background color is white
            # imagealphablending($im, false);
            # imagefilledrectangle($im, 0, 0, 16, 16, $color);
            # imagealphablending($im, true);
            $im = imagecreatetruecolor(16, 16);
            $color = imagecolorallocate($im, 255, 255, 255);
            imagefill($im, 1, 1, $color);

            # getting mask
            $alpha = '';
            for($i=0; $i<16; $i++)
            {
                $z = unpack('Cx/Cy', substr($iconres, $current_offset, 2));
                $z = str_pad(decbin($z['x']), 8, '0', STR_PAD_RIGHT)  . str_pad(decbin($z['y']), 8, '0', STR_PAD_LEFT);
                $alpha .= $z;
                $current_offset = $current_offset + 4;
            }

            # drawing image
            $ico_size = 16;   
            $off = 0; # range (0-255)

            # cases for different color depth
            switch ($bitmap_info['header']['biBitCount'])   
            {        

                ###################### for 32 bit icons ######################
                case 32:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $a = round((255-$arraycolor[$off*4+3])/2);
                            $a = ($a<0) ? 0 : $a;
                            $a = ($a>127) ? 127 : $a;

                        $color = imagecolorallocatealpha($im,
$arraycolor[$off*4+2], $arraycolor[$off*4+1], $arraycolor[$off*4], $a);
                            imagesetpixel($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 24 bit icons ######################
                case 24:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;

                        $color = imagecolorallocatealpha($im,
$arraycolor[$off*3+2], $arraycolor[$off*3+1], $arraycolor[$off*3],
$valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 08 bit icons ######################
                case 8:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;
                            $c = $arraycolor[$off];
                            $c = $bitmap_info['colors'][$c];
                            $color = imagecolorallocatealpha($im, $c['rgbRed'], $c['rgbGreen'], $c['rgbBlue'], $valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 04 bit icons ######################
                # 318 = 22 (header) + 40 (bitmap_info) + 16 * 4 (colors) + 128 (pixels) + 64 (mask)
                case 4:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;
                            $c = ($arraycolor[floor($off/2)]);
                            $c = str_pad(decbin($c), 8, '0', STR_PAD_LEFT);
                            $m =  (fmod($off+1, 2)==0) ? 1 : 0;
                            $c = bindec(substr($c, $m*4, 4));
                            $c = $bitmap_info['colors'][$c];
                            $color = imagecolorallocatealpha($im, $c['rgbRed'], $c['rgbGreen'], $c['rgbBlue'], $valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 01 bit icons ######################
                # 198 = 22 (header) + 40 (bitmap_info) + 2 * 4 (colors) + 64 (pixels, but real 32 needed?) + 64 (mask)
                case 1:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;
                            $c = ($arraycolor[floor($off/8)]); # меняем байт каждые 8 пикселей
                            $c = str_pad(decbin($c), 8, '0', STR_PAD_LEFT);
                            $m = fmod($off+8, 8) + 1; # bit number
                            $c = (int) substr($c, $m-1, 1);
                            $c = $bitmap_info['colors'][$c];
                            $color = imagecolorallocatealpha($im, $c['rgbRed'], $c['rgbGreen'], $c['rgbBlue'], $valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                        $off = $off + 16;
                    }           
                break;

                ##############################################################

                default:
                return '';
            }

            # output png
            ob_start();
            # imagesavealpha($im, true);
            imagepng($im);
            imagedestroy($im);
            $res = ob_get_clean();
        }
        return $res;
    }
}


  class YnsRSS{
       private $_dirFiles ;
     /* get Identify RSS type */
      function __construct()
      {
         $this->_dirFiles = dirname(__FILE__) ;
         //echo $this->_dirFiles;
      }
      public function  getIdentify()
      {
          
      }
      /* getRss */
      public function getParse($type = null,$url_rss,$params = array())
      {
          $trust_link = array('yahoo'=>'http://vn.yahoo.com/?p=us','cnn'=>'http://edition.cnn.com/','bbc'=>'http://www.bbc.com/');
           $url_arr = parse_url($url_rss);
            $url_rss = htmlspecialchars_decode($url_rss);  
          if (isset($url_arr['scheme'])&& $url_arr['scheme']!='http')
          {
             $url_rss = str_replace($url_arr['scheme'].'://','http://',$url_rss);
          }
          $data = array();
          $url_log = "";
          $url_log_tmp="";
          try{
              if (isset($url_arr['scheme'])){
                  $parent_url = $url_arr['scheme'].'://'.$url_arr['host'].'/';
                  //echo $parent_url; 
                  //if ( strpos($parent_url,'yahoo')){
                  //    $parent_url = 'http://vn.yahoo.com/?p=us';
                  //}
                  foreach($trust_link as $key=>$url)
                  {
                      if ( strpos($parent_url,$key)){
                          $parent_url = $url;
                          break;
                      }
                  }
                  //$logo = $this->getFavicon($parent_url);
                  
                   $favicon = new favicon($parent_url, 0);
                   $fv = $favicon->get_output_data();
                  
                   //var_dump($fv);
                   if ($fv == "" || strpos($fv,'Request')){
                       // echo 'x';
                       $url_log_tmp = $favicon->get_ico_url();
                       
                   }
                  
                    $url_log = $fv;
                  
                  
                   
              }
          }catch(Exception $ex)
          {
              echo $ex->getMessage();
          }
          if ($type == null){
              require_once( $this->_dirFiles.'/Plugins/base.php');
              $rss = new base();
              $data = $rss->getParse($params,$url_rss);
			
              //print_r($data);echo '1';
              $data['logo_ico']  =   $url_log; 
              $data['logo_ico_url'] = $url_log_tmp;
              return $data;
          }
      }
      
  }
?>
