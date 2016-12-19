<?php

namespace PhalconApi\Acl\Adapter;

use PhalconApi\Acl\MountingEnabledAdapterInterface;
use PhalconApi\Acl\AclAdapterMountTrait;

class Memory extends \Phalcon\Acl\Adapter\Memory implements MountingEnabledAdapterInterface
{
    use AclAdapterMountTrait;
}
