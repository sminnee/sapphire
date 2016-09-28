<?php

namespace SilverStripe\Core\Config;

/**
 * Provides extensions to this object to integrate it with standard config API methods.
 *
 * Note that all classes can have configuration applied to it, regardless of whether it
 * uses this trait.
 */
trait Configurable {

	/**
	 * Get a configuration accessor for this class. Short hand for Config::inst()->get(static::class, .....).
	 * @return Config_ForClass
	 */
	public static function config() {
		return Config::inst()->forClass(get_called_class());
	}

	/**
	 * Gets the first set value for the given config option
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function stat($name) {
		return Config::inst()->get(static::class, $name, Config::FIRST_SET);
	}

	/**
	 * Update the config value for a given property
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set_stat($name, $value) {
		Config::inst()->update(static::class, $name, $value);
	}

	/**
	 * Gets the uninherited value for the given config option
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function uninherited($name) {
		return Config::inst()->get(static::class, $name, Config::UNINHERITED);
	}
}
