<?php echo '<?xml version="1.0"?>'; ?>
<!DOCTYPE cross-domain-policy SYSTEM "http://www.macromedia.com/xml/dtds/cross-domain-policy.dtd">
<cross-domain-policy>
  <?php if( !empty($this->allowedHosts) ): ?>
    <?php foreach( $this->allowedHosts as $allowedHost ): ?>
      <allow-access-from domain="<?php echo $this->escape($allowedHost) ?>" />
    <?php endforeach ?>
  <?php endif ?>
</cross-domain-policy>