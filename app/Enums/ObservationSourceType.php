<?php

namespace App\Enums;

enum ObservationSourceType: string
{
    case PRICE_TAG_OCR = 'PRICE_TAG_OCR';
    case MANUAL        = 'MANUAL';
    case SCRAPE        = 'SCRAPE';
    case API           = 'API';
}
