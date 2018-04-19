<?php

use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Session;
use SilverStripe\Forms\TextField;

/**
 * {@link FormField} for adding an optional maths protection question to a form.
 *
 * @package mathspamprotection
 */

class MathSpamProtectorField extends TextField
{
    /**
     * @config
     *
     * @var bool $enabled
     */
    private static $enabled = true;

    /**
     * @config
     *
     * @var string
     */
    private static $question_prefix;
    
    /**
     * @config
     *
     * @var bool $allow_numeric_answer
     */
    private static $allow_numeric_answer = true;
    
    public function Field($properties = array())
    {
        if (Config::inst()->get('MathSpamProtectorField', 'enabled')) {
            return parent::Field($properties);
        }

        return null;
    }

    public function FieldHolder($properties = array())
    {
        if (Config::inst()->get('MathSpamProtectorField', 'enabled')) {
            return parent::FieldHolder($properties);
        }

        return null;
    }

    /**
     * Returns the spam question
     *
     * @return string
     */
    public function Title()
    {
        $prefix = Config::inst()->get('MathSpamProtection', 'question_prefix');

        if (!$prefix) {
            $prefix = _t('MathSpamProtectionField.SPAMQUESTION', "Spam protection question: %s");
        }

        return sprintf(
            $prefix,
            self::get_math_question()
        );
    }

    /**
     * Validates the value submitted by the user with the one saved into the 
     * {@link Session} and then notify callback object with the spam checking 
     * result.
     *
     * @return bool
     */
    public function validate($validator)
    {
        if (!Config::inst()->get('MathSpamProtectorField', 'enabled')) {
            return true;
        }

        if (!self::correct_answer($this->Value())) {
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
     * @return string
     */
    public static function get_math_question()
    {
        if (!Session::get("mathQuestionV1") && !Session::get("mathQuestionV2")) {
            $v1 = rand(1, 9);
            $v2 = rand(1, 9);

            Session::set("mathQuestionV1", $v1);
            Session::set("mathQuestionV2", $v2);
        } else {
            $v1 = Session::get("mathQuestionV1");
            $v2 = Session::get("mathQuestionV2");
        }

        return sprintf(
            _t('MathSpamProtection.WHATIS', "What is %s plus %s?"),
            MathSpamProtectorField::digit_to_word($v1),
            MathSpamProtectorField::digit_to_word($v2)
        );
    }

    /**
     * Checks the given answer if it matches the addition of the saved session 
     * variables.
     *
     * Users can answer using words or digits.
     *
     * @return bool
     */
    public static function correct_answer($answer)
    {
        $v1 = Session::get("mathQuestionV1");
        $v2 = Session::get("mathQuestionV2");

        Session::clear('mathQuestionV1');
        Session::clear('mathQuestionV2');

        $word = MathSpamProtectorField::digit_to_word($v1 + $v2);

        return ($word == strtolower($answer) || (Config::inst()->get('MathSpamProtectorField', 'allow_numeric_answer') && (($v1 + $v2) == $answer)));
    }

    /**
     * Helper method for converting digits to their equivalent english words
     *
     * @return string
     */
    public static function digit_to_word($num)
    {
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

        if ($num < 0) {
            return "minus ".($numbers[-1*$num]);
        }

        return $numbers[$num];
    }

    public function Type()
    {
        return 'mathspamprotector text';
    }
}
