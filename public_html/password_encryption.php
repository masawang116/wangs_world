<?php
/*
 * encryption
 * ----------
 * php 3 - md5($password)
 * php 4.3 - sha1($password)
 * php 5.1.2 - hash('sha1', $password) <- fast
 * php 5.3 crypt($password, $salt) <-slow
 * 
 * salt
 * ----
 * rainbow tables -  precomputed table of passwords
 * salts add additional data before encryption to prevent rainbow tables
 * i.e "Put salt on the {$password}"
 * 
 * random salt
 * i.e "Put salt on the {$password} at ". time();
 * 
 * store salt in database
 * encrypt salt
 * 
 * $salt = md5(uniqid(mt_rand(), true));
 * $format_and_salt = $format_string . $salt;
 * $hashed_password = crypt($password, $format_andsalt);
 */

$password = 'password';
$hash_format = '$2y$10$';
$salt = 'Salt22CharactersOrMore';
echo 'Length: ' . strlen($salt);
$format_and_salt = $hash_format . $salt;

$hash = crypt($password, $format_and_salt);
echo '<br />';
echo $hash;

$hash2 = crypt('password', $hash);
echo '<br />';
echo $hash2;

//functions
function password_encrypt($password) {
    $hash_format = '$2y$10$'; //use blowfish with cost of 10
    $salt_length = 22; //blowfish salts should be 22 chars or more
    $salt = generate_salt($salt_length);
    $format_and_salt = $hash_format . $salt;
    $hash = crypt($password, $format_and_salt);
    return $hash;
}

function generate_salt($length) {
    //md5 returns 32 chars
    $unique_random_string = md5(uniqid(mtrand(), true));
    
    //valid chars for a salt are [a-zA-Z0-9./]
    $base64_string = base64_encode($unique_random_string);
    
    //removes + signs because not valid
    $modified_base64_string = str_replace('+', '.', $base64_string);
    
    $salt = substr($modified_base64_string, 0, $length);
    
    return $salt;
}

function password_check($password, $existing_hash) {
    $hash = crypt($password, $existing_hash);
    if ($hash === $existing_hash) {
        return true;
    } else {
        return false;
    }
}