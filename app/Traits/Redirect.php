<?php

namespace App;

trait Redirect
{
    protected function getRedirectRoute(): string
    {
        return $this->getRedirectRoute()::getUrl('index');
    }
}
