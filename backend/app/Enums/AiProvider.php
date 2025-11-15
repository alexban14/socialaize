<?php

namespace App\Enums;

enum AiProvider: string
{
    case OPENAI = 'openai';
    case GOOGLE = 'google';
    case GROQ = 'groq';
}
