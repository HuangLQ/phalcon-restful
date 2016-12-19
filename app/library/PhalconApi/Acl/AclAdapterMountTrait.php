<?php
namespace PhalconApi\Acl;

trait AclAdapterMountTrait
{
    public function mountMany(array $mountables)
    {
        foreach ($mountables as $mountable) {
            $this->mount($mountable);
        }

        return $this;
    }

    public function mount(\PhalconApi\Acl\MountableInterface $mountable)
    {
        if ($this instanceof \Phalcon\Acl\AdapterInterface) {
            $resources = $mountable->getAclResources();
            $rules = $mountable->getAclRules($this->getRoles());

            // Mount resources
            foreach ($resources as $resourceConfig) {
                if (count($resourceConfig) == 0) {
                    continue;
                }

                $this->addResource($resourceConfig[0], count($resourceConfig) > 1 ? $resourceConfig[1] : null);
            }

            // Mount rules
            $allowedRules = array_key_exists(\Phalcon\Acl::ALLOW, $rules) ? $rules[\Phalcon\Acl::ALLOW] : [];
            $deniedRules = array_key_exists(\Phalcon\Acl::DENY, $rules) ? $rules[\Phalcon\Acl::DENY] : [];

            foreach ($allowedRules as $ruleConfig) {
                if (count($ruleConfig) < 2) {
                    continue;
                }

                $this->allow($ruleConfig[0], $ruleConfig[1], count($ruleConfig) > 2 ? $ruleConfig[2] : null);
            }

            foreach ($deniedRules as $ruleConfig) {
                if (count($ruleConfig) < 2) {
                    continue;
                }

                $this->deny($ruleConfig[0], $ruleConfig[1], count($ruleConfig) > 2 ? $ruleConfig[2] : null);
            }
        }
    }
}
