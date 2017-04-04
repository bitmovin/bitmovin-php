<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class H264SubMe extends AbstractEnum
{

    const FULLPEL = 'FULLPEL';
    const SAD = 'SAD';
    const SATD = 'SATD';
    const QPEL3 = 'QPEL3';
    const QPEL4 = 'QPEL4';
    const QPEL5 = 'QPEL5';
    const RD_IP = 'RD_IP';
    const RD_ALL = 'RD_ALL';
    const RD_REF_IP = 'RD_REF_IP';
    const RD_REF_ALL = 'RD_REF_ALL';
    const QPRD = 'QPRD';
    const FULL_RD = 'FULL_RD';

}