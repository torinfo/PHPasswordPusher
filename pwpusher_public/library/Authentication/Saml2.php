<?php
/**
 * Licensed to The Apereo Foundation under one or more contributor license
 * agreements. See the NOTICE file distributed with this work for
 * additional information regarding copyright ownership.

 * The Apereo Foundation licenses this file to you under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.

 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


class Authentication_Saml2 extends Authentication_Abstract
{

    private $_record = array();

    private $_saml2config = array(
        'ssourl' => '',
        'slourl' => '',
    );

    public function getUsername() {
        return $this->_record->username;
    }
    public function getFirstname()
    {
        return $this->_record->firstname;
    }

    public function getSurname()
    {
        return $this->_record->lastname;
    }

    public function getEmail()
    {
        if (isset($this->_record->email)) {
            return $this->_record->email;
        }
        return null;
    }

    public function check()
    {
        return true;
    }

    public function login($username, $password)
    {
        return true;
    }

    /** Saml2 integration */
    public function needsLogin()
    {
        // Redirect to sso site with saml2login.php RelayState
        // sso site should do a saml sso, and the RelayState saml2login.php, should POST all required data to
        // <this website>/library/Xerte/Authentication/Saml2/saml2login.php
        //
        // The latter saml2login.php should set the SESSION as required and the _record
        //
        // This implementation is based on One_Logins Saml2 php implementation

        if (!isset($_SESSION['saml2session'])) {
            if ($this->_saml2config['ssourl'] == "")
                $this->_saml2config['ssourl'] = $this->site_url . "library/Authentication/Saml2/sso.php";

            $_SESSION['saml2reqid'] = bin2hex(openssl_random_pseudo_bytes(10));
            if (strpos($this->_saml2config['ssourl'], '?') === false)
            {
                $url = $this->_saml2config['ssourl'] . "?site=" . $this->site_url . "&returnurl=library/Authentication/Saml2/saml2login.php&request=" . $_SESSION['saml2reqid'];
            }
            else
            {
                $url = $this->_saml2config['ssourl'] . "&site=" . $this->site_url . "&returnurl=library/Authentication/Saml2/saml2login.php&request=" . $_SESSION['saml2reqid'];
            }
            header("Location: " . $url);
            exit;
        }
        else
        {
            return false;
        }
    }

    public function hasLogout() {
        return true;
    }

    public function logout()
    {
        if (isset($_SESSION['saml2session'])) {
            session_destroy();

            $_SESSION['saml2reqid'] = bin2hex(openssl_random_pseudo_bytes(10));
            if ($this->_saml2config['slourl'] == "")
                $this->_saml2config['slourl'] = $this->site_url . "library/Xerte/Authentication/Saml2/slo.php";

            $url = $this->_saml2config['slourl'] . "?site=" . $this->site_url . "&returnurl=library/Authentication/Saml2/saml2login.php&request=" . $_SESSION['saml2reqid'];
            header("Location: " . $url);
            exit;
        }
        else
        {
            return true;
        }
    }

}
