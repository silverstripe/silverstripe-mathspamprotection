<?php

/**
 * @package mathspamprotection
 */

class MathSpamProtector implements SpamProtector {
	
	/**
	 * Returns the {@link MathSpamProtectorField} associated with this protector
	 *
	 * @return MathSpamProtectorField
	 */
	public function getFormField($name = null, $title = null, $value = null, $form = null, $rightTitle = null) {
		return new MathSpamProtectorField($name, $title, $value, $form, $rightTitle);
	}
	
	/**
	 * Function required to handle dynamic feedback of the system.
	 * if unneeded just return true
	 *
	 * @return true
	 */
	public function sendFeedback($object = null, $feedback = "") {
		return true;
	}
}