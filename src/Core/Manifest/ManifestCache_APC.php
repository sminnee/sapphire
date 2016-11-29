<?php

namespace SilverStripe\Core\Manifest;

/**
 * Stores manifest data in APC.
 * Note: benchmarks seem to indicate this is not particularly faster than _File
 */
class ManifestCache_APC implements ManifestCache
{
    protected $pre;

    public function __construct($name)
    {
        $this->pre = $name;
    }

    public function load($key)
    {
        return apc_fetch($this->pre . $key);
    }

    public function save($data, $key)
    {
        apc_store($this->pre . $key, $data);
    }

    public function clear()
    {
    }
}
