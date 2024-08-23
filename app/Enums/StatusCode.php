<?php

namespace App\Enums;

enum StatusCode: int
{
    case OK = 200;
    case BadRequest = 400;
    case ForbiddenAccess = 403;
    case ServiceMaintenance = 503;
    
    case InvalidOperatorID = 900401;
    case InvalidPlayer = 900402;
    case InvalidSignature = 900403;
    case InvalidGameId = 900404;
    case InvalidTranID = 900408;
    
    case InternalServerError = 900500;
}
