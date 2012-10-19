<?php

class Core_View_Helper_FeedDescription extends Zend_View_Helper_Abstract
{

    public function feedDescription($str, $length = 125)
    {
        $str = preg_replace('/<br\s*\/>/', ' - ', $str);
        $str = preg_replace('/<a\s+[^>]+>(.*?)<\/a>/im', '', $str);
        $str = strip_tags($str);
        $str = trim($str, ' - ');
        $str = str_replace('--', '-', $str);
        $result = (strlen($str) > $length) ? substr($str, 0, $length - 3) : $str;
        $result = preg_replace('/ [^ ]*$/', ' ...', $result);
        return $result;
    }

}