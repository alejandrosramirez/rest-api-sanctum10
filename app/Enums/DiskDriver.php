<?php

namespace App\Enums;

/**
 * Generate basic disk driver names.
 */
enum DiskDriver: string
{
    case LOCAL = 'local';
    case S3 = 's3';
    case UPLOADS = 'uploads';
    case FILES = 'files';
}
