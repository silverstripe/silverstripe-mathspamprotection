<?php

/**
 * {@link FormField} for adding an optional maths protection question to a form.
 *
 * @package mathspamprotection
 */

class MathSpamProtectorField extends SpamProtectorField {

	/**
	 * @var bool If MathSpamProtection is enabled
	 */
	private static $enabled = true;

	/**
	 * Outputs the field HTML to the the web browser
	 *
	 * @return HTML
	 */
	function Field($properties = array()) {
		if(self::is_enabled()) {
			$attributes = array(
				'type' => 'text',
				'class' => 'text ' . ($this->extraClass() ? $this->extraClass() : ''),
				'id' => $this->id(),
				'name' => $this->getName(),
	 			'value' => $this->Value(),
				'title' => $this->Title(),
				'tabindex' => $this->getAttribute('tabindex'),
				'maxlength' => ($this->maxLength) ? $this->maxLength : null,
				'size' => ($this->maxLength) ? min( $this->maxLength, 30 ) : null
			);
			return $this->createTag('input', $attributes);
		}
	}

	/**
	 * Returns the spam question
	 *
	 * @return string
	 */
	function Title() {
		return sprintf(_t('MathSpamProtectionField.SPAMQUESTION', "Spam protection question: %s"), self::get_math_question());
	}

	/**
	 * Validates the value submitted by the user with the one saved
	 * into the {@link Session} and then notify callback object
	 * with the spam checking result.
	 *
	 * @return bool
	 */
	function validate($validator) {
		if(!self::is_enabled()) return true;

		if(!self::correct_answer($this->Value())){
			$validator->validationError(
				$this->name,
				_t(
					'MathSpamProtectionField.INCORRECTSOLUTION',
					"Incorrect solution to the spam protection question, please try again."
				),
				"error"
			);
			return false;
		}

		return true;
	}


	/**
	 * Creates the question from random variables, which are also saved to the session.
	 *
	 * @return String
	 */
	public static function get_math_question(){
		if(!Session::get("mathQuestionV1")&&!Session::get("mathQuestionV2")){
			$v1 = rand(1,9);
			$v2 = rand(1,9);
			Session::set("mathQuestionV1",$v1);
			Session::set("mathQuestionV2",$v2);
		}
		else{
			$v1 = Session::get("mathQuestionV1");
			$v2 = Session::get("mathQuestionV2");
		}

		return sprintf(
			_t('MathSpamProtection.WHATIS',"What is %s plus %s?"),
			MathSpamProtectorField::digit_to_word($v1),
			MathSpamProtectorField::digit_to_word($v2)
		);
	}

	/**
	 * Checks the given answer if it matches the addition of the saved session variables.
	 * Users can answer using words or digits.
	 *
	 * @return bool
	 */
	public static function correct_answer($answer){
		$v1 = Session::get("mathQuestionV1");
		$v2 = Session::get("mathQuestionV2");

		Session::clear('mathQuestionV1');
		Session::clear('mathQuestionV2');

		return (MathSpamProtectorField::digit_to_word($v1 + $v2) == $answer || ($v1 + $v2) == $answer);
	}

	/**
	 * Helper method for converting digits to their equivalent english words
	 *
	 * @return string
	 */
	static function digit_to_word($num){
		$numbers = array(_t('MathSpamProtection.ZERO', 'zero'),
			_t('MathSpamProtection.ONE', 'one'),
			_t('MathSpamProtection.TWO', 'two'),
			_t('MathSpamProtection.THREE', 'three'),
			_t('MathSpamProtection.FOUR', 'four'),
			_t('MathSpamProtection.FIVE', 'five'),
			_t('MathSpamProtection.SIX', 'six'),
			_t('MathSpamProtection.SEVEN', 'seven'),
			_t('MathSpamProtection.EIGHT', 'eight'),
			_t('MathSpamProtection.NINE', 'nine'),
			_t('MathSpamProtection.TEN', 'ten'),
			_t('MathSpamProtection.ELEVEN', 'eleven'),
			_t('MathSpamProtection.TWELVE', 'twelve'),
			_t('MathSpamProtection.THIRTEEN', 'thirteen'),
			_t('MathSpamProtection.FOURTEEN', 'fourteen'),
			_t('MathSpamProtection.FIFTEEN', 'fifteen'),
			_t('MathSpamProtection.SIXTEEN', 'sixteen'),
			_t('MathSpamProtection.SEVENTEEN', 'seventeen'),
			_t('MathSpamProtection.EIGHTEEN', 'eighteen'));

			if($num < 0) return "minus ".($numbers[-1*$num]);

		return $numbers[$num];
	}

	/**
	 * Returns true when math spam protection is enabled
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return (bool) self::$enabled;
	}

	/**
	 * Set whether math spam protection is enabled
	 *
	 * @param bool
	 */
	public static function set_enabled($enabled = true) {
		self::$enabled = $enabled;
	}
}