<?php

namespace App\Enums;

enum CaptureStatus: string
{
    case PENDING = 'PENDING';
    case PARSED  = 'PARSED';
    case FAILED  = 'FAILED';
    case LINKED  = 'LINKED';
}
