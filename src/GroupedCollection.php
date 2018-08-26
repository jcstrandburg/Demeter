<?php
namespace Jcstrandburg\Demeter;

interface GroupedCollection extends Collection, \ArrayAccess
{
    public function getGroupKeys(): array;
}
