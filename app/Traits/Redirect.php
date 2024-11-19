<?php

namespace App\Traits;

trait Redirect
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
