<?php

namespace SilverStripe\Core\Manifest;

/**
 * Stores manifest data in files in TEMP_DIR dir on filesystem
 */
class ManifestCache_File implements ManifestCache
{

    protected $folder = null;

    public function __construct($name)
    {
        $this->folder = TEMP_FOLDER . DIRECTORY_SEPARATOR . $name;
        if (!is_dir($this->folder)) {
            mkdir($this->folder);
        }
    }

    public function load($key)
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . 'cache_' . $key;
        return file_exists($file) ? unserialize(file_get_contents($file)) : null;
    }

    public function save($data, $key)
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . 'cache_' . $key;
        file_put_contents($file, serialize($data));
    }

    public function clear()
    {
        array_map('unlink', glob($this->folder . DIRECTORY_SEPARATOR . 'cache_*'));
    }
}
