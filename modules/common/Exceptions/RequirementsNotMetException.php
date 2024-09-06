<?php

namespace Modules\common\Exceptions;

use Exception;
use Throwable;

class RequirementsNotMetException extends Exception
{
    public function __construct(string $requirementName, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(__('Modules/common::messages.requirements_not_met', ['requirement' => $requirementName]), $code, $previous);
    }
}
