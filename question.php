<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * regexmatchcloze question definition class.
 *
 * @package    qtype
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
*This holds the definition of a particular question of this type.
*If you load three questions from the question bank, then you will get three instances of
*that class. This class is not just the question definition, it can also track the current
*state of a question as a student attempts it through a question_attempt instance.
*/

if (!class_exists('qtype_regexmatch_common_regex')) {
    require_once($CFG->dirroot . '/question/type/regexmatchcloze/common/common.php');
}
const REGEXMATCH_CLOZE_ALLOWED_KEYS = array(QTYPE_REGEXMATCH_SEPARATOR_KEY, QTYPE_REGEXMATCH_POINTS_KEY, QTYPE_REGEXMATCH_SIZE_KEY, QTYPE_REGEXMATCH_FEEDBACK_KEY,  QTYPE_REGEXMATCH_COMMENT_KEY);
const REGEXMATCH_CLOZE_ALLOWED_OPTIONS = array('I', 'D', 'P', 'R', 'O', 'S', 'T', 'i', 'd', 'p', 'r', 'o', 's', 't');

/**
 * Represents a regexmatchcloze question.
 *
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexmatchcloze_question extends question_graded_automatically {

    /**
     * @var array<qtype_regexmatch_common_answer> array containing all the allowed regexes
     */
    public $answers = array();

    public function start_attempt(
        question_attempt_step $step,
                              $variant
    ) {
        // probably not needed
    }

    public function is_complete_response(array $response) {
        foreach ($this->answers as $answer) {
            if(!array_key_exists($this->get_answer_field_name($answer), $response)) {
               return false;
            }
            $submittedAnswer = $response[$this->get_answer_field_name($answer)];

            if($submittedAnswer === null || $submittedAnswer === '')
                return false;
        }
        return true;
    }

    public function is_gradable_response(array $response) {
        foreach ($this->answers as $answer) {
            if(!array_key_exists($this->get_answer_field_name($answer), $response)) {
                continue;
            }
            $submittedAnswer = $response[$this->get_answer_field_name($answer)];

            if($submittedAnswer !== null && $submittedAnswer !== '') {
                return true;
            }
        }
        return false;
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_regexmatch');
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->answers as $answer) {
            if(!question_utils::arrays_same_at_key($prevresponse, $newresponse, $this->get_answer_field_name($answer)))
                return false;
        }

        return true;
    }

    public function get_expected_data() {
        $arr = array();
        foreach ($this->answers as $answer) {
            $key = $answer->feedback;
            $arr[$this->get_answer_field_name($answer)] = PARAM_RAW;
        }

        return $arr;
    }

    public function summarise_response(array $response) {
        $str = "";
        foreach ($this->answers as $answer) {
            $key = $answer->feedback;
            $submittedAnswer = $response[$this->get_answer_field_name($answer)] ?? get_string('empty-answer', 'qtype_regexmatchcloze');
            $str .= get_string('gap-num', 'qtype_regexmatchcloze', $key) . " $submittedAnswer\n";
        }

        return $str;
    }

    public function un_summarise_response(string $summary) {
        return '';
    }

    public function grade_response(array $response) {

        $maxPoints = 0.0;
        $userPoints = 0.0;
        foreach ($this->answers as $answer) {
            $maxPoints += $answer->points;
            $submittedAnswer = $response[$this->get_answer_field_name($answer)] ?? null;

            if($submittedAnswer == null)
                continue;

            $res = $this->get_regex_for_answer($answer, $submittedAnswer);
            if($res != null)
                $userPoints += $res[0];
        }

        $fraction = $userPoints / $maxPoints;

        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function get_correct_response() {
        return null;
    }

    public function clear_wrong_from_response(array $response) {
        // We want to keep the previous answer as it is only a single answer field
        return $response;
    }

    public function check_file_access($qa, $options, $component, $filearea,
                                      $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);
        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                $args, $forcedownload);
        }
    }

    public function get_answer_field_name(qtype_regexmatch_common_answer $answer) {
        return "gap" . $answer->feedback;
    }

    /**
     * @param string $submittedAnswer answer submitted from a student
     * @return array|null [0] => points, [1] => regex. regex of given $answer, which matches given answer or null if none matches
     */
    public function get_regex_for_answer(qtype_regexmatch_common_answer $answer, string $submittedAnswer) {
        $ret = null;

        // remove \r from the answer, which should not be matched.
        $submittedAnswer = str_replace("\r", "", $submittedAnswer);

        foreach ($answer->regexes as $regex) {
            $value = qtype_regexmatch_common_try_regex($answer, $regex, $submittedAnswer);

            if($value > 0.0) {
                $points = $answer->points * $value * ($regex->percent / 100.0);
                if($ret == null || $points > $ret[0]) {
                    $ret = array($points, $regex);
                }
            }
        }

        return $ret;
    }

    /**
     * @param string $submittedAnswer answer submitted from a student
     * @return question_state  question_state::$gradedwrong, question_state::$gradedright or question_state::$gradedpartial.
     */
    public function get_question_state_for_answer(qtype_regexmatch_common_answer $answer, string $submittedAnswer) {
        $ret = $this->get_regex_for_answer($answer, $submittedAnswer);
        if($ret == null || $ret[0] == 0)
            return question_state::$gradedwrong;
        else if($ret[0] == $answer->points)
            return question_state::$gradedright;
        return question_state::$gradedpartial;
    }
}
