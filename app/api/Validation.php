<?php

class Validation
{
    public function validate(string $name, string $address, string $phone)
    {
        if (strlen($name) < 3 || strlen($address) < 3 || strlen($phone) < 3) {
            return true;
        }
    }
}