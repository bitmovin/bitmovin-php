<?php

namespace Bitmovin\api\model\inputs;

use Bitmovin\api\model\AbstractModel;
use Icecave\Parity\Parity;

abstract class Input extends AbstractModel
{

    public function equals(Input $input)
    {
        if (!$input instanceof self)
        {
            return false;
        }
        return Parity::isEqualTo($input, $this);
    }

}