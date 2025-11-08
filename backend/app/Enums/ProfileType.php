<?php

namespace App\Enums;

enum ProfileType: string
{
    case PERSONAL = 'personal';
    case BUSINESS = 'business';
    case ACADEMIC = 'academic';
    case CREATOR = 'creator';
}
