<?php

namespace Modules\Shared\ImageUpload\Exception;

class ThumbnailImageException extends \Exception
{

    public function __construct($message = 'Unable to create thumbnail image.' , $statusCode = 400)
    {
        parent::__construct($message, $statusCode);
    }
}
