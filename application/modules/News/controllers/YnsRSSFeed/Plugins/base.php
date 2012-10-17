<?php
   require_once('core/rss_fetch.inc');
   class base
   {
      
       public function getParse($params= array(),$url_rss)     
       {
           
            if ( $url_rss ) {
                $rss = fetch_rss($url_rss);
                //echo ":<pre>".print_r($rss,true)."</pre>";die(); 
                if ($rss != null)
                    $type = $rss->is_atom() ; 
               
                else{
                    $type =null;
                    //return null;
                }
                 
                // print_r($rss->image); die();
               if ($type ==""){
                   //DublinCore1.0.xml  
                    if (isset($rss->channel['dc']) && isset($rss->feed_version) && $rss->feed_version == '1.0' )
                    {
                         $data = array(        
                                  'title'        => isset($rss->channel['title'])?$rss->channel['title']:"",        
                                  'link'         => isset($rss->channel['link'])?$rss->channel['link']:"",        
                                  'dateModified' => isset($rss->last_modified)?$rss->last_modified:"",        
                                  'description'  => isset($rss->channel['description'])?$rss->channel['description']:"",        
                                  'author'       => isset($rss->channel['dc']['rights'])?$rss->channel['dc']['rights']:"", 
                                  'pubDate'      => isset($rss->channel['dc']['date'])?$rss->channel['dc']['date']:"",
                                  'entries'      => array(),
                                  'image_logo'   => isset($rss->image['url'])?$rss->image['url']:""
                                  );
                    }
                    else
                    {
                                
                        $data = array(        
                                  'title'        => isset($rss->channel['title'])?$rss->channel['title']:"",        
                                  'link'         => isset($rss->channel['link'])?$rss->channel['link']:"",        
                                  'dateModified' => isset($rss->channel['updated'])?$rss->channel['updated']:"",        
                                  'description'  => isset($rss->channel['description'])?$rss->channel['description']:"",        
                                  'author'       => isset($rss->channel['author_name'])?$rss->channel['author_name']:"", 
                                  'pubDate'      => isset($rss->channel['updated'])?$rss->channel['updated']:"",
                                  'entries'      => array(),
                                  'image_logo'   => isset($rss->image['url'])?$rss->image['url']:""
                                  );
                    }   
               }
               else{//Atom RSS
                    $data = array(        
                              'title'        => isset($rss->channel['title'])?$rss->channel['title']:"",        
                              'link'         => isset($rss->channel['link'])?$rss->channel['link']:"",        
                              'dateModified' => isset($rss->channel['lastbuilddate'])?$rss->channel['lastbuilddate']:"",        
                              'description'  => isset($rss->channel['description'])?$rss->channel['description']:"",        
                              'author'       => isset($rss->channel['copyright'])?$rss->channel['copyright']:"", 
                              'pubDate'      => isset($rss->channel['lastbuilddate'])?$rss->channel['lastbuilddate']:"",
                              'entries'      => array(),
                              'image_logo'   => isset($rss->image['url'])?$rss->image['url']:""
                              );
                   }
                // print_r($data);
                $count_feed = count($rss->items);//echo $count_feed; 
               
               foreach ($rss->items as $item)
               {
                    //echo "<pre>".print_r($item,true)."</pre>";
                    
                    if ($type =="")
                    {
                        if (isset($rss->channel['dc']) && isset($rss->feed_version) && $rss->feed_version == '1.0' )
                        {//DublinCore1.0.xml   
                            if (!isset($item['date_timestamp']) || empty($item['date_timestamp']))
                            {
                    
                                $pubdate = empty($data['pubDate'])?date('Y-m-d H:i:s'): $data['pubDate'];
                                $time = strtotime($pubdate)+ $count_feed;
                                $count_feed--;
                                $pub = ""; 
                            }
                            else
                            {
                  
                              // echo $data['pubDate'];
                                $time = $item['date_timestamp'];//strtotime($data['pubDate']);
                                $pub = $item['pubdate'];
                             }
                             $media_files = "";
                            if (isset($item['media']['content'])) 
                            {
                            
                               foreach ($item['media']['content'] as $me) 
                               {
                                   //print_r($me);
                                   $media_files.="<li id ='item'><a href='".$me['url']."'>".substr( basename($me['url']),0,40)."</a><li>";
                               }
                            }
                              if (isset($item['enclosure']))
                              {
                                  foreach ($item['enclosure'] as $me) 
                               {
                                   $media_files.="<li id ='item'><a href='".$me['url']."'>".substr( basename($me['url']),0,40)."</a><li>";
                               } 
                              }
                         //$media_files .=;                       
                           $edata = array(        
                                          'title'        => isset($item['title'])?$item['title']:"",        
                                          'description'  => isset($item['dc']['description'])?$item['dc']['description']:"",
                                          'content'      => isset($item['dc']['content'])?$item['dc']['content']:"",
                                          'image'         => "",
                                          'link_detail'  => isset($item['link'])?$item['link']:"",
                                          'author'      =>isset($item['dc']['rights'])?$item['dc']['rights']:"",         
                                          'pubDate' => $time,
                                          'pubDate_parse'=>$pub,
                                          'posted_date'  => date('Y-m-d H:i:s'),
                                          
                                      ); 
                            //if ($media_files !="")
                             //  $edata['description']  .= "<ul id='media_file_content_yn'>".$media_files."</ul>";
                            $data['entries'][] = $edata; 
                }
                       else
                       {
                            if (!isset($item['date_timestamp']) || empty($item['date_timestamp']))
                            {
                                
                                $pubdate = empty($data['pubDate'])?date('Y-m-d H:i:s'): $data['pubDate'];
                                $time = strtotime($pubdate)+ $count_feed;
                                $count_feed--;
                                $pub = ""; 
                            }
                            else
                            {
                  
                              // echo $data['pubDate'];
                                $time = $item['date_timestamp'];//strtotime($data['pubDate']);
                                $pub = $item['pubdate'];
                             }
                              $media_files = "";
                            if (isset($item['media']['content'])) 
                            {
                            
                               foreach ($item['media']['content'] as $me) 
                               {
                                   //print_r($me);
                                   $media_files.="<li id ='item'><a href='".$me['url']."'>".basename($me['url'])."</a><li>";
                               }
                            }
                              if (isset($item['enclosure']))
                              {
                                  foreach ($item['enclosure'] as $me) 
                               {
                                   $media_files.="<li id ='item'><a href='".$me['url']."'>".basename($me['url'])."</a><li>";
                               } 
                              }
                         //$media_files .=;                       
                           $edata = array(        
                                          'title'        => isset($item['title'])?$item['title']:"",        
                                          'description'  => isset($item['description'])?$item['description']:"",
                                          'content'      => isset($item['content'])?$item['content']:"",
                                          'image'         => "",
                                          'link_detail'  => isset($item['link'])?$item['link']:"",
                                          'author'      =>isset($item['author'])?$item['author']:"",         
                                          'pubDate' => $time,
                                          'pubDate_parse'=>$pub,
                                          'posted_date'  => date('Y-m-d H:i:s'),
                                          
                                      ); 
                            //if ($media_files !="")
                              // $edata['description']  .= "<ul id='media_file_content_yn'>".$media_files."</ul>";
                            $data['entries'][] = $edata;            
                     }
                     }
                     else{
                //print_r($item);die();
                           if (!isset($item['date_timestamp']) || empty($item['date_timestamp'])){
                            $pubdate = empty($data['pubDate'])?date('Y-m-d H:i:s'): $data['pubDate'];
                            $time = strtotime($pubdate)+ $count_feed;
                            $count_feed--;
                            $pub = ""; 
                        }else{
                          
                          // echo $data['pubDate'];
                            $time = $item['date_timestamp'];//strtotime($data['pubDate']);
                            $pub = $item['updated'];
                         }
                          
                           $edata = array(        
                                          'title'        => isset($item['title'])?$item['title']:"",        
                                          'description'  => isset($item['description'])?$item['description']:"",
                                          'content'      => isset($item['atom_content'])?$item['atom_content']:"",
                                          'image'         => "",
                                          'link_detail'  => isset($item['link'])?$item['link']:"",
                                          'author'      =>isset($item['author'])?$data['author']:"",         
                                          'pubDate' => $time,
                                          'pubDate_parse'=>$pub,
                                          'posted_date'  => date('Y-m-d H:i:s'),
                                          
                                      ); 
                                                
                            $data['entries'][] = $edata;                  
                        }
               }
               // die('ss');
               
               return $data;
                
            }
            return null;
       }
   }
?>
