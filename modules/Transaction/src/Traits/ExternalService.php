<?php

namespace Desafio\Transaction\Traits;

use Closure;

trait ExternalService
{
    /**
     * @param Closure $next
     * 
     * @return mixed
     */
    public function authorize(Closure $next)
    {        
        if (! method_exists($this->authorizeService, 'authorized')) {
            return $this->unauthorized();
        }

        return ! $this->authorizeService->authorized()
            ? $this->unauthorized()
                : $next($this);
    }

    /**
     * @param Closure $next
     * 
     * @return mixed
     */
    public function notify(Closure $next)
    {
        if (! method_exists($this->notifyService, 'canNotify')) {
            return $this;
        }

        return $this->notifyService->canNotify()
            ? $next($this)
                : $this;
    }
}