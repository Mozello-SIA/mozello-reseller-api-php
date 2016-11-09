<?php

/**
 * Mozello API for PHP
 * @see http://www.mozello.com/developers/reseller-api/
 */
class MozelloApi
{
    private $_resellerEmail;
    private $_resellerPassword;

    private $_apiToken;

    private $_rawResponse;
    private $_rawResponseInfo;

    private $_lastErrorMessage;
    private $_lastResponse;

    /**
     * Initializes a new instance of this class.
     *
     * @param string $email : Reseller email address.
     * @param string $password : Reseller password (plaintext).
     */
    public function __construct($email, $password)
    {
        $this->_resellerEmail = $email;
        $this->_resellerPassword = $password;

        $this->_apiToken = null;

        $this->_rawResponse = '';
        $this->_rawResponseInfo = [];
        
        $this->_lastErrorMessage = '';
        $this->_lastResponse = [];
    }

    /**
     * Obtains an API token.
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-authorization
     * @return bool
     */
    public function authorize()
    {
        $response = $this->_request('/api/authorize/', array(
            'email'    => $this->_resellerEmail,
            'password' => $this->_resellerPassword
        ));

        if ($response) {
            $this->_apiToken = $response['apiToken'];
            return true;
        }
        else {
            $this->_apiToken = null;
            return false;
        }
    }

    /**
     * Invalidates the API token.
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-authorization-logout
     * @return bool
     */
    public function logout()
    {
        if ($this->_apiToken == null) {
            return false;
        }
        
        $response = $this->_request('/api/authorize/logout/', array(
            'apiToken' => $this->_apiToken
        ));

        return is_array($response);
    }

    /**
     * Retrieves all websites.
     * Returns an array of websites or False on error.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-websites
     * @return mixed
     */
    public function websites()
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/websites/', array(
            'apiToken' => $this->_apiToken
        ));

        if ($response) {
            return $response['websites'];
        }
        else {
            return false;
        }
    }

    /**
     * Enables a disabled website.
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-website-enable
     * @param int $websiteID : Website identifier.
     * @return bool
     */
    public function websiteEnable($websiteID)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/enable/', array(
            'apiToken' => $this->_apiToken
        ));

        return is_array($response);
    }

    /**
     * Disables a website.
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-website-disable
     * @param int $websiteID : Website identifier.
     * @return boolean
     */
    public function websiteDisable($websiteID)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/disable/', array(
            'apiToken' => $this->_apiToken
        ));

        return is_array($response);
    }

    /**
     * Enables paid website subscription (Premium account).
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-website-enable-premium
     * @param int $websiteID : Website identifier.
     * @return boolean
     */
    public function websiteEnablePremium($websiteID)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/enable_premium/', array(
            'apiToken' => $this->_apiToken
        ));

        return is_array($response);
    }

    /**
     * Stops paid website subscription.
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-website-disable-premium
     * @param int $websiteID : Website identifier.
     * @return boolean
     */
    public function websiteDisablePremium($websiteID)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/disable_premium/', array(
            'apiToken' => $this->_apiToken
        ));

        return is_array($response);
    }

    /**
     * Creates a one-time auto-login link for a website.
     * Returns the link on success of False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-website-login
     * @param int $websiteID : Website identifier.
     * @return string
     */
    public function websiteLogin($websiteID)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/login/', array(
            'apiToken' => $this->_apiToken
        ));

        if ($response) {
            return $response['loginUrl'];
        }
        else {
            return false;
        }
    }

    /**
     * Lists domains attached to a website.
     * Returns an array containing website domains or False on error.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-domains
     * @param int $websiteID : Website identifier.
     * @return array
     */
    public function domains($websiteID)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/domains/', array(
            'apiToken' => $this->_apiToken
        ));

        if ($response) {
            return $response['domains'];
        }
        else {
            return false;
        }
    }

    /**
     * Attaches a new domain to a website.
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-domain-add
     * @param int $websiteID : Website identifier.
     * @param string $domain : Domain name without www.
     * @return array
     */
    public function domainAdd($websiteID, $domain)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/domain/add/', array(
            'apiToken' => $this->_apiToken,
            'domain' => $domain
        ));

        return is_array($response);
    }

    /**
     * Removes the domain previously attached to a website.
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-domain-remove
     * @param int $websiteID : Website identifier.
     * @param int $domainID : Domain identifier.
     * @return bool
     */
    public function domainRemove($websiteID, $domainID)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/website/' . $websiteID . '/domain/' . $domainID . '/remove/', array(
            'apiToken' => $this->_apiToken,
        ));

        return is_array($response);
    }

    /**
     * Retrieves reseller account data.
     * Returns an array containing settings on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-settings
     * @return array
     */
    public function getSettings()
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/settings/', array(
            'apiToken' => $this->_apiToken
        ));

        if ($response) {
            unset($response['error']);
            return $response;
        }
        else {
            return false;
        }
    }

    /**
     * Changes reseller account settings (only the allowed settings).
     * Returns True on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-settings-set
     * @param array $newSettings : Associative array containing new settings.
     * @return bool 
     */
    public function setSettings(array $newSettings)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $newSettings['apiToken'] = $this->_apiToken;
        $response = $this->_request('/api/settings/set/', $newSettings);

        return is_array($response);
    }

    /**
     * Retrieves financial information.
     * Returns an array containing financial information on success or False otherwise.
     *
     * @see http://www.mozello.com/developers/reseller-api/#api-balance
     * @return array
     */
    public function balance()
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/balance/', array(
            'apiToken' => $this->_apiToken
        ));

        if ($response) {
            unset($response['error']);
            return $response;
        }
        else {
            return false;
        }
    }

    /**
     * Changes reseller account password.
     * Returns True on success or False otherwise.
     * @see http://www.mozello.com/developers/reseller-api/#api-password
     *
     * @param string $newPassword : New password.
     * @return bool
     */
    public function password($newPassword)
    {
        if ($this->_apiToken == null) {
            return false;
        }

        $response = $this->_request('/api/password/set/', array(
            'apiToken' => $this->_apiToken,
            'password' => $newPassword
        ));

        return is_array($response);
    }

    /**
     * Executes a Mozello API request.
     * Returns a response array or False on error.
     *
     * @param  string $url  : API URL.
     * @param  array  $vars : Request variables.
     * @return array
     */
    private function _request($url, array $vars)
    {
        $curl = curl_init();
        $endpoint = 'http://resellers.mozello.com';

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $endpoint . $url,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => http_build_query($vars),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => true
        ));

        $this->_rawResponse = curl_exec($curl);
        $this->_rawResponseInfo = curl_getinfo($curl);
        curl_close($curl);

        if ($this->_rawResponseInfo['http_code'] == 200 && $this->_rawResponse !== false) {

            $this->_lastResponse = @json_decode($this->_rawResponse, true);

            if (!is_array($this->_lastResponse)) {
                $this->_lastErrorMessage = 'Unable to parse the response';
                return false;
            }
            elseif (isset($this->_lastResponse['error']) && $this->_lastResponse['error'] == true) {
                $this->_lastErrorMessage = $this->_lastResponse['errorMsg'];
                return true;
            }
            else {
                $this->_lastErrorMessage = '';
                return $this->_lastResponse;
            }
        }
        else {
            $this->_lastErrorMessage = 'API is unavailable';
            return false;
        }
    }

    /**
     * Returns the last API request error message or empty string on success.
     * @return string
     */
    public function getLastErrorMessage()
    {
        return $this->_lastErrorMessage;
    }
}