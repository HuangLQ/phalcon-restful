<?php

namespace PhalconApi\Acl;

interface MountingEnabledAdapterInterface extends \Phalcon\Acl\AdapterInterface
{
    /**
     * Mounts the mountable object onto the ACL
     *
     * @param MountableInterface $mountable
     *
     * @return static
     */
    public function mount(MountableInterface $mountable);

    /**
     * Mounts an array of mountable objects onto the ACL
     *
     * @param MountableInterface[] $mountables
     *
     * @return static
     */
    public function mountMany(array $mountables);
}
