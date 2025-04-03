<?php
namespace App\Models\Traits;

use App\Helpers\SodiumCrypto;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasEncrypt {

    protected function makeEncryptedAttributeCallables(string $column): Array
    {
        return [
            function ($value) use ($column) {
                if (!$value) return null;
                $crypt_key = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.$column");
                return SodiumCrypto::decrypt($value, $crypt_key);
            },
            function ($value) use ($column) {
                if (!$value) return null;
                $crypt_key = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.$column");
                return SodiumCrypto::encrypt($value, $crypt_key);
            }
        ];
    }

    protected function encryptCpfIndex(): void
    {
        if($this->cpf === \null) return;
        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.cpf_index");
        $this->attributes["cpf_index"] = SodiumCrypto::getIndex($this->cpf, $crypt_index);
    }

    protected function encryptFirstNameIndex(): void
    {
        if($this->name === \null) return;
        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.name_index");
        
        $names = explode(" ", $this->name);
        $first_name = $names[0];

        $this->attributes["first_name_index"] = SodiumCrypto::getIndex($first_name, $crypt_index);
    }

    protected function encryptSurnameTokens(): void
    {
        if($this->name === \null) return;
        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.name_index");

        $surnames = explode(" ", $this->name);
        \array_shift($surnames);
        $surnames_tokens = \array_map(fn($surname) => SodiumCrypto::getIndex($surname, $crypt_index), $surnames);
        
        $this->attributes["surname_tokens"] = \json_encode($surnames_tokens);
    }
}