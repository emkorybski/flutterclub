<?php

class Inviter_Plugin_WLLConsentToken
{
    /**
     * Indicates whether the delegation token is set and has not expired.
     */
    public function isValid()
    {
        if (!self::getDelegationToken()) {
            return false;
        }
        
        $now = time();
        return (($now-300) < self::getExpiry());
    }

    /**
     * Refreshes the current token and replace it. If operation succeeds 
     * true is returned to signify success.
     */
    public function refresh()
    {
        $wll = $this->_wll;
        $ct = $wll->refreshConsentToken($this);
        if (!$ct) {
            return false;
        }
        self::copy($ct);
        return true;
    }

    private $_wll;

    /**
     * Initialize the ConsentToken module with the WindowsLiveLogin, 
     * delegation token, refresh token, session key, expiry, offers, 
     * location ID, context, decoded token, and raw token.
     */
public function __construct($wll=null, $delegationtoken=null, $refreshtoken=null,
                                    $sessionkey=null, $expiry=null, $offers=null, $locationID=null, $context=null,
                                    $decodedtoken=null, $token=null)
{
    if($wll) $this->_wll = $wll;
    if($delegationtoken) self::setDelegationToken($delegationtoken);
    if($refreshtoken) self::setRefreshToken($refreshtoken);
    if($sessionkey) self::setSessionKey($sessionkey);
    if($expiry) self::setExpiry($expiry);
    if($offers) self::setOffers($offers);
    if($locationID) self::setLocationID($locationID);
    if($context) self::setContext($context);
    if($decodedtoken) self::setDecodedToken($decodedtoken);
    if($token) self::setToken($token);
}


    public static function initMe($wll, $delegationtoken, $refreshtoken,
$sessionkey, $expiry, $offers, $locationID, $context,
$decodedtoken, $token)
        {
            $o = new Inviter_Plugin_WLLConsentToken($wll, $delegationtoken, $refreshtoken,
                                                $sessionkey, $expiry, $offers, $locationID, $context,
                                                $decodedtoken, $token);
            return $o;
        }

    private $_delegationtoken;

    /**
     * Gets the Delegation token.
     */
    public function getDelegationToken()
    {
        return $this->_delegationtoken;
    }

    /**
     * Sets the Delegation token.
     */
    private function setDelegationToken($delegationtoken)
    {
        if (!$delegationtoken) {
            throw new Exception('Error: WLL_ConsentToken: Null delegation token.');
        }
        $this->_delegationtoken = $delegationtoken;
    }

    private $_refreshtoken;

    /**
     * Gets the refresh token.
     */
    public function getRefreshToken()
    {
        return $this->_refreshtoken;
    }

    /**
     * Sets the refresh token.
     */
    private function setRefreshToken($refreshtoken)
    {
        $this->_refreshtoken = $refreshtoken;
    }

    private $_sessionkey;

    /**
     * Gets the session key.
     */
    public function getSessionKey()
    {
        return $this->_sessionkey;
    }

    /**
     * Sets the session key.
     */
    private function setSessionKey($sessionkey)
    {
        if (!$sessionkey) {
            throw new Exception('Error: WLL_ConsentToken: Null session key.');
        }
        $this->_sessionkey = base64_decode(urldecode($sessionkey));
    }

    private $_expiry;
    
    /**
     * Gets the expiry time of delegation token.
     */
    public function getExpiry()
    {
        return $this->_expiry;
    }

    /**
     * Sets the expiry time of delegation token.
     */
    private function setExpiry($expiry)
    {
        if (!$expiry) {
            throw new Exception('Error: WLL_ConsentToken: Null expiry time.');
        }

        if (!preg_match('/^\d+$/', $expiry) || ($expiry <= 0)) {
            throw new Exception('Error: WLL_ConsentToken: Invalid expiry time: '
                                . $expiry);
        }
        $this->_expiry = $expiry;
    }
    
    private $_offers;

    /**
     * Gets the list of offers/actions for which the user granted consent.
     */
    public function getOffers()
    {
        return $this->_offers;
    }

    private $_offers_string;

    /**
     * Gets the string representation of all the offers/actions for which 
     * the user granted consent.
     */
    public function getOffersString()
    {
        return $this->_offers_string;
    }

    /**
     * Sets the offers/actions for which user granted consent.
     */
    private function setOffers($offers)
    {
        if (!$offers) {
            throw new Exception('Error: WLL_ConsentToken: Null offers.');
        }
        
        $off_s = "";
        $off = array();

        $offers = urldecode($offers);
        $offers = explode(";", $offers);
        foreach ($offers as $offer) {
            $offer = explode(":", $offer);
            $offer = $offer[0];
            if ($off_s) {
                $off_s .= ",";
            }
            $off_s .= $offer;
            $off[] = $offer;
        }

        $this->_offers_string = $off_s;
        $this->_offers = $off;
    }
    
    private $_locationID;
    /**
     * Gets the location ID.
     */
    public function getLocationID()
    {
        return $this->_locationID;
    }

    /**
     * Sets the location ID.
     */
    private function setLocationID($locationID)
    {
        if (!$locationID) {
            throw new Exception('Error: WLL_ConsentToken: Null Location ID.');
        }    
        $this->_locationID = $locationID;
    }

    private $_context;
    /**
     * Returns the application context that was originally passed
     * to the sign-in request, if any.
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Sets the application context.
     */
    private function setContext($context)
    {
        $this->_context = $context;
    }

    private $_decodedtoken;
    /**
     * Gets the decoded token.
     */
    public function getDecodedToken()
    {
        return $this->_decodedtoken;
    }

    /**
     * Sets the decoded token.
     */
    private function setDecodedToken($decodedtoken)
    {
        $this->_decodedtoken = $decodedtoken;
    }

    private $_token;

    /**
     * Gets the raw token.
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Sets the raw token.
     */
    private function setToken($token)
    {
        $this->_token = $token;
    }

    /**
     * Makes a copy of the ConsentToken object.
     */
    private function copy($ct)
    {
        $this->_delegationtoken = $ct->_delegationtoken;
        $this->_refreshtoken = $ct->_refreshtoken;
        $this->_sessionkey = $ct->_sessionkey;
        $this->_expiry = $ct->_expiry;
        $this->_offers = $ct->_offers;
        $this->_offers_string = $ct->_offers_string;
        $this->_locationID = $ct->_locationID;
        $this->_decodedtoken = $ct->_decodedtoken;
        $this->_token = $ct->_token;
    }
}

?>
