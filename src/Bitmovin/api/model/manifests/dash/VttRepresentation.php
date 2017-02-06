<?php
/**
 * Created by PhpStorm.
 * User: dmoser
 * Date: 03.02.17
 * Time: 14:47
 */

namespace Bitmovin\api\model\manifests\dash;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class VttRepresentation extends AbstractModel
{
    /**
     * @JMS\type("string")
     * @var string
     */
    var $vttUrl;
}