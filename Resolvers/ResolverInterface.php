<?php

namespace Ecg\DataMigration\Resolvers;

/**
 * Interface ResolverInterface
 * @package Ecg\DataMigration\Resolvers
 */
interface ResolverInterface
{
    /**
     * @return mixed
     */
    public function resolve();
}
