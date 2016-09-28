<?php

namespace SilverStripe\Forms;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Object;
use SilverStripe\Dev\Debug;

/**
 * This class represents "transformations" of a form - such as making it printable or making it readonly.
 * The idea is that sometimes you will want to make your own such transformations, and you shouldn't have
 * to edit the underlying code to support this.
 *
 * The first step in creating a transformation is subclassing FormTransformation.  After that, you have two
 * ways of defining specific functionality:
 *   - Define performMyTransformation() methods on each applicable FormField() object.
 *   - Define transformFieldType($field) methods on your subclass of FormTransformation.
 *
 * To actually perform the transformation, call $form->transform(new MyTransformation());
 */
class FormTransformation {
	public function transform(FormField $field) {
		// Look for a performXXTransformation() method on the field itself.
		// performReadonlyTransformation() is a pretty commonly applied method.
		// Otherwise, look for a transformXXXField() method on this object.
		// This is more commonly done in custom transformations

		// We iterate through each array simultaneously, looking at [0] of both, then [1] of both.
		// This provides a more natural failover scheme.

		$transNames = array_reverse(array_map(
			function($name) { return ClassInfo::shortName($name); },
			array_values(ClassInfo::ancestry(static::class))
		));
		$fieldClasses = array_reverse(array_map(
			function($name) { return ClassInfo::shortName($name); },
			array_values(ClassInfo::ancestry(get_class($field)))
		));

		$len = max(sizeof($transNames), sizeof($fieldClasses));
		for($i=0; $i<$len; $i++) {
			// This is lets fieldClasses be longer than transNames
			if(!empty($transNames[$i])) {
				$funcName = 'perform' . $transNames[$i];
				if($field->hasMethod($funcName)) {
					return $field->$funcName($this);
				}
			}

			// And this one does the reverse.
			if(!empty($fieldClasses[$i])) {
				$funcName = 'transform' . $fieldClasses[$i];
				if($this->hasMethod($funcName)) {
					return $this->$funcName($field);
				}
			}
		}

		throw new \BadMethodCallException("FormTransformation:: Can't perform '" . static::class . "' on '" .
			get_class($field) . "'");
	}
}

