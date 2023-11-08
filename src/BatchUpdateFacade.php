<?php

namespace Quangpv\BatchUpdate;

use Illuminate\Support\Facades\Facade;

class BatchUpdateFacade extends Facade
{
    /**
     * Get facade accessor to retrieve instance.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'BatchUpdate';
    }
}
