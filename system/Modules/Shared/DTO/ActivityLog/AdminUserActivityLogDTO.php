<?php

namespace Modules\Shared\DTO\ActivityLog;

use Modules\Shared\DTO\Constructor;

class AdminUserActivityLogDTO extends Constructor
{
    public string $description;
    public string $activityType;
    public string $ipAddress;
    public string|null $objectId;
    public string $modifierId;
    public string $modifierUsername;
}
