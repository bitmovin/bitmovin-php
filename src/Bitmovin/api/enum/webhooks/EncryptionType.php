<?php

namespace Bitmovin\api\enum\webhooks;

use Bitmovin\api\enum\AbstractEnum;

class EncryptionType extends AbstractEnum
{
    const AES = 'AES';
    const DESede = 'DESede';
    const Blowfish = 'Blowfish';
    const RSA = 'RSA';
}