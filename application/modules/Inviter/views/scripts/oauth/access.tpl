
<?php if (isset($this->denied) && $this->denied) : ?>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      window.setTimeout(function() {
        window.close();
      }, 5000);
    });
  </script>
  
  <h3 style="padding: 15px 10px 0"><?php echo $this->translate('Our Network'); ?></h3>
  <p class="no_content" style=""><?php echo $this->translate("OK, you've denied Our Network access to interact with your account!"); ?></p>
  
<?php endif; ?>

<?php  ?>
<?php if (isset($this->parse_url) && $this->parse_url) : ?>
  <script type="text/javascript">
    var cur_url = window.location.href;

    if (cur_url !== cur_url.replace('#access_token', 'access_token')) {
      window.location.href = cur_url.replace('#access_token', 'access_token');
    }
  </script>

<?php endif; ?>

<?php if (isset($this->hotmail_integration) && $this->hotmail_integration) : ?>
  <script src="https://js.live.net/v5.0/wl.debug.js" type="text/javascript"></script>

  <script type="text/javascript">
    var hotmailDetails = <?php echo $this->jsonInline($this->hotmailDetails); ?>
  </script>

  <h1>Hello Bro!!!</h1>
<?php endif; ?>
<?php  ?>


<html  dir="ltr">
<head>
    <meta  http-equiv="Content-Type"  content="text/html;  charset=utf-8">
    <base  href="http://login.live.com/pp1200/"/>
    <meta  http-equiv="refresh"  content="8;url=http://login.live.com/uilogout.srf?mkt=EN-US&lc=1033&appid=000000004007CE52&nsvis=&ec=1"  />
    <script  type="text/javascript">
        var  aID=new  Array();var  saml  =  0;var  aLIn=new  Array(aID.length);var  fThirdPartyEnabled  =  -1;for(var  i=0;i<aLIn.length;i++){  aLIn[i]=0;}var  iTImg=0;var  iTADFS=0;var  aADFS=new  Array(iTADFS);for(var  i=0;i<aADFS.length;i++){  aADFS[i]=2;}var  fADFSWait=0;if(iTADFS  >  0){  fADFSWait=1;}function  SetImg(idx,fGB){  }function  SO(idx,fGB){  if  (fThirdPartyEnabled  ==  0){      fGB  =  0;}if(aLIn[idx]==0){  iTImg++;if(fGB==1){  aLIn[idx]=1;}else{  aLIn[idx]=2;}SetImg(idx,fGB);if((fADFSWait==0)  &&  (aLIn.length==iTImg)  &&  (fThirdPartyEnabled  !=  -1)){  Ret();}}}function  ThirdChk(fEnabled)
    {  fThirdPartyEnabled  =  fEnabled;for(var  i=0;i<aLIn.length;i++){  if((aLIn[i]==1)  ||  (aLIn[i]==2)){  if  (fThirdPartyEnabled!=1){  aLIn[i]=2;}SetImg(i,  aLIn[i]);}}if((fADFSWait==0)  &&  (aLIn.length==iTImg)){  Ret();}}
    function  ADFSTimeout(){  fADFSWait  =  0;if((aLIn.length==iTImg)  &&  (fThirdPartyEnabled  !=  -1)){  Ret();}}function  ADFSLd(idx){  aADFS[idx]--;if(aADFS[idx]  ==  0){  iTADFS--;}if((iTADFS==0)){  setTimeout('ADFSTimeout()',  2000);}}function  Ret(){  var  szVS="";var  szNS="";var  szAPP="";var  fSUI=false;for(var  i=0;i<aLIn.length;i++){  if(aLIn[i]!=1){  fSUI=true;}if(aID[i].indexOf("SID")!=-1){  var  sSID=aID[i].substring(0,aID[i].length-3);if(szVS!=""){  szVS+="$";}szVS+=sSID;}else{  if(aID[i].indexOf("AID")!=-1){  var  sAID=aID[i].substring(0,aID[i].length-3);if(szAPP!=""){  szAPP+="$";}szAPP+=sAID;}else{  if(szNS!=""){  szNS+="$";}szNS+=aID[i];}}}if(saml<0){  fSUI  =  true;}if(szVS!=""    &&  fSUI  ==  true){  SVis(szVS);}else{  DVis();}if(szNS!=""    &&  fSUI  ==  true){  SPPV(szNS);}else{  DPPV();}if(szAPP!=""    &&  fSUI  ==  true){  SAPPV(szAPP);}else{  DAPPV();}if(fSUI){  window.location.replace("http://login.live.com/uilogout.srf?mkt=EN-US&lc=1033&appid=000000004007CE52&nsvis=&ec=1");}else{  RetRU();}}function  DVis(){  document.cookie="MSPVisNet=+;  expires=Thu,  30  Oct  1980  16:00:00  GMT;  path=/;  domain=login.live.com";}function  DPPV(){  document.cookie="MSPNSVisNet=+;  expires=Thu,  30  Oct  1980  16:00:00  GMT;  path=/;  domain=login.live.com";}function  DAPPV(){  document.cookie="MSPAPPVisNet=+;  expires=Thu,  30  Oct  1980  16:00:00  GMT;  path=/;  domain=login.live.com";}function  SVis(szVS){  var  dPD=new  Date(2037,11,30);document.cookie="MSPVisNet="+szVS+";  expires="+dPD.toGMTString()+";  path=/;  domain=login.live.com";}function  SPPV(szNS){  var  dPD=new  Date(2037,11,30);document.cookie="MSPNSVisNet="+szNS+";  expires="+dPD.toGMTString()+";  path=/;  domain=login.live.com";}function  SAPPV(szAPP){  var  dPD=new  Date(2037,11,30);document.cookie="MSPAPPVisNet="+szAPP+";  expires="+dPD.toGMTString()+";  path=/;  domain=login.live.com";}function  RetRU(){  if(top!=self){  window.location.replace("https://login.live.com/images/Trans1x1.gif");}else{  window.location.replace("http://accountservices.msn.com/gls.srf?urlID=msnhome&lc=1033");}}function  IF(){  var  e;ThirdChk(1);}function  UILog(){  window.location.replace("http://login.live.com/uilogout.srf?mkt=EN-US&lc=1033&appid=000000004007CE52&nsvis=&ec=1");}setTimeout("UILog()",8000);</script><script  type="text/javascript">function  OnBack(){}</script>
    <title>Continue</title>
    <meta  name="PageID"  content="i5041"  />
    <meta  name="SiteID"  content="10"  />
    <meta  name="ReqLC"  content="1033"  />
    <meta  name="LocLC"  content="1033"  />
</head>
<body  onload="IF();">
</body>
</html>