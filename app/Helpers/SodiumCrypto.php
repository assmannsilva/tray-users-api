<?php

namespace App\Helpers;

class SodiumCrypto
{

    public static function getCryptKey($config_key_encrypt): string
    {
        $crypt_key = config($config_key_encrypt);
        if(!$crypt_key) {
            throw new \RuntimeException("Chave de criptografia não configurada para {$config_key_encrypt}");
        }
        return sodium_hex2bin($crypt_key);
    }

    public static function encrypt(string $value, string $key): string
    {
        $nonce = random_bytes(24);
        $cipher_text = sodium_crypto_secretbox($value, $nonce, $key);
        return bin2hex($nonce . $cipher_text);
    }
    

    public static function decrypt(string $cipher_text, string $key): string
    {
        $decoded = hex2bin($cipher_text);
        $nonce = mb_substr($decoded, 0, 24, '8bit');
        $cipher = mb_substr($decoded, 24, null, '8bit');
        return sodium_crypto_secretbox_open($cipher, $nonce, $key);
    }

    public static function getIndex(string $value, string $index_key): string
    {
        return hash_hmac('sha256', strtolower($value), $index_key);
    }

}

?>