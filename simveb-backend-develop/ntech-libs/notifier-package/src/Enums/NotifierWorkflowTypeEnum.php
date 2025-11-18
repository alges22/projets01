<?php

namespace Ntech\NotifierPackage\Enums;

use Ntech\NotifierPackage\Traits\EnumToArray;

enum NotifierWorkflowTypeEnum
{
    use EnumToArray;

    case in_app;
    case email;
    case sms;
    case chat;
    case push;
}
