<?php

namespace Modules\common\Exceptions;

use Exception;
use Throwable;

class ModuleNotEnabledException extends Exception
{
    public function __construct(string $requirementName, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(__('Modules/common::messages.module_not_enabled', ['module_name' => $requirementName]), $code, $previous);
    }
}
