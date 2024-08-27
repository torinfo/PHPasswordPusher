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
session_start();
if (isset($_GET['response']))
{
    $response = urldecode($_GET['response']);
    $decoded = json_decode($response);
    $_SESSION['saml2session'] = $decoded;
    error_log('saml2login: saml2reqid: response=' . $decoded->saml2reqid . ", session=" . $_SESSION['saml2reqid'] . "(site=" . $_GET['site'] . ")");
    if ($decoded->saml2reqid == $_SESSION['saml2reqid']) {
        header("Location: " . $_GET['site']);
    }
    else
    {
        die("Invalid login request");
    }
}
else
{
    error_log("saml2login: no response");
    die("Invalid login request");
}