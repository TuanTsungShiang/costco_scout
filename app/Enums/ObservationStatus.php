<?php

namespace App\Enums;

enum ObservationStatus: string
{
    case VALID       = 'VALID';
    case INVALIDATED = 'INVALIDATED';
    case SUPERSEDED  = 'SUPERSEDED';
}
