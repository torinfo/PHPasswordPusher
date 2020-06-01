<?php
/**
 * Encryption functions
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

/**
 * Check the key size.
 *
 * @return boolean $correct
 */
function correctKeySize()
{
    include 'config.php';
    switch (strlen($key)) {
        case 16:
        case 24:
        case 32:
            return true;
            break;
        default:
            return false;
            break;
    }
}

/**
 * Encrypt the credential.
 *
 * @param string $plaintext the credential to be encrypted
 *
 * @return string $encrypted the openssl and base64 encrypted string
 */
function encryptCred($plaintext) 
{
    include 'config.php';
    
    if (in_array($cipher, openssl_get_cipher_methods()))
    {
        $iv = bin2hex(random_bytes(openssl_cipher_iv_length($cipher)));
        $tag = bin2hex(random_bytes($tagLength));
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
    }
    
    return base64_encode($iv . $tag . $ciphertext);
}

/**
 * Decrypt the credential from the database.
 *
 * @param string $encrypted the encrypted string
 *
 * @return string $decrypted the decrypted string
 */
function decryptCred($encoded) 
{
    include 'config.php';
    
    $ivLength = strlen(bin2hex(random_bytes(openssl_cipher_iv_length($cipher))));

    $decodedCred = base64_decode($ciphertext);
    $iv = substr($decodedCred, 0, $ivLength);
    $tag = substr($decodedCred, $ivLength, $tagLength);
    $decodedCT = substr($decodedCred, $ivLength + $tagLength);
    
    return openssl_decrypt($decodedCT, $cipher, $key, $options=0, $iv, $tag);
}


/**
 * Generates a UUID v4 
 * From Andrew Moore's example: http://www.php.net/manual/en/function.uniqid.php#94959
 *
 * @return string $uniqueId
 */
function getUniqueId() 
{
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

/**
 * Hashes the id via CRYPT_SHA512
 *
 * @param string $id , $salt
 *
 * @param $salt
 * @return string $hashedId
 */
function hashId($id, $salt) 
{
    $hashedId = crypt($id, '$6$rounds=5000$' . $salt . '$');
    return $hashedId;
}

/**
 * Generates a 128-bit salt
 *
 * Unused? 
 *
 * @return string $salt
 */
function getSalt()
{
    $salt = substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22);
    return $salt;
}
