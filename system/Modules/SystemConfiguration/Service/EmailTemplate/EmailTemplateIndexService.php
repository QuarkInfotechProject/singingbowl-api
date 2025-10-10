<?php

namespace Modules\SystemConfiguration\Service\EmailTemplate;

use Modules\SystemConfiguration\App\Models\EmailTemplate;

class EmailTemplateIndexService
{
    function index()
    {
        return EmailTemplate::select('name', 'title')->get();
    }
}
