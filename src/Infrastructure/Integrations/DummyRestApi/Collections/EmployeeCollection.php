<?php

namespace App\Infrastructure\Integrations\DummyRestApi\Collections;

use App\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use Doctrine\Common\Collections\ArrayCollection;

class EmployeeCollection extends ArrayCollection
{
    public function add(mixed $element)
    {
        if (!$element instanceof Employee) {
            throw new \InvalidArgumentException('Invalid element');
        }
        parent::add($element);
    }
}
