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
 * @package    qtype_regexmatchcloze
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

if (!class_exists('qtype_regexmatch_common_regex')) {
    require_once($CFG->dirroot . '/question/type/regexmatchcloze/common/common.php');
}

/**
 * @var array Allowed keys for regexmatch cloze
 */
const QTYPE_REGEXMATCH_CLOZE_ALLOWED_KEYS = [
    QTYPE_REGEXMATCH_COMMON_SEPARATOR_KEY,
    QTYPE_REGEXMATCH_COMMON_POINTS_KEY,
    QTYPE_REGEXMATCH_COMMON_SIZE_KEY,
    QTYPE_REGEXMATCH_COMMON_FEEDBACK_KEY,
    QTYPE_REGEXMATCH_COMMON_COMMENT_KEY,
];
/**
 * @var array Allowed options for regexmatch cloze
 */
const QTYPE_REGEXMATCH_CLOZE_ALLOWED_OPTIONS = ['I', 'D', 'P', 'R', 'O', 'S', 'T', 'i', 'd', 'p', 'r', 'o', 's', 't'];

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
    public $answers = [];

    /**
     * Whether the given response can be considered complete. Meaning all gaps are filled.
     *
     * @param array $response responses, as returned by
     *      question_attempt_step::get_qt_data().
     * @return bool whether this response is a complete answer to this question.
     */
    public function is_complete_response(array $response) {
        foreach ($this->answers as $answer) {
            if (!array_key_exists($this->get_answer_field_name($answer), $response)) {
                return false;
            }
            $submittedanswer = $response[$this->get_answer_field_name($answer)];

            if ($submittedanswer === null || $submittedanswer === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Whether the given response is gradable. Meaning at least one gap is filled.
     *
     * @param array $response responses, as returned by
     *      question_attempt_step::get_qt_data().
     * @return bool whether this response can be graded.
     */
    public function is_gradable_response(array $response) {
        foreach ($this->answers as $answer) {
            if (!array_key_exists($this->get_answer_field_name($answer), $response)) {
                continue;
            }
            $submittedanswer = $response[$this->get_answer_field_name($answer)];

            if ($submittedanswer !== null && $submittedanswer !== '') {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns an error if no gaps are filled.
     * @param array $response the response
     * @return string the message.
     */
    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_regexmatchcloze');
    }

    /**
     * Whether the given response has changed.
     *
     * @param array $prevresponse the responses previously recorded for this question,
     *      as returned by question_attempt_step::get_qt_data()
     * @param array $newresponse the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->answers as $answer) {
            if (!question_utils::arrays_same_at_key($prevresponse, $newresponse, $this->get_answer_field_name($answer))) {
                return false;
            }
        }

        return true;
    }

    /**
     * What data will be included in the form submission when a student submits
     * this question
     *
     * @return array|string variable name => PARAM_... constant, or, as a special case
     *      that should only be used in unavoidable, the constant question_attempt::USE_RAW_DATA
     *      meaning take all the raw submitted data belonging to this question.
     */
    public function get_expected_data() {
        $arr = [];
        foreach ($this->answers as $answer) {
            $arr[$this->get_answer_field_name($answer)] = PARAM_RAW;
        }

        return $arr;
    }

    /**
     * Produce a plain text summary of a response.
     * @param array $response a response, as might be passed to grade_response().
     * @return string a plain text summary of that response, that could be used in reports.
     */
    public function summarise_response(array $response) {
        $str = "";
        foreach ($this->answers as $answer) {
            $key = $answer->feedback;
            $submittedanswer = $response[$this->get_answer_field_name($answer)] ?? get_string('empty-answer', 'qtype_regexmatchcloze');
            $str .= get_string('gap-num', 'qtype_regexmatchcloze', $key) . " $submittedanswer\n";
        }

        return $str;
    }

    /**
     * Not possible.
     *
     * @param string $summary a string, which might have come from summarise_response
     * @return array empty array
     */
    public function un_summarise_response(string $summary) {
        return [];
    }

    /**
     * Grade a response to the question, returning a fraction between
     * get_min_fraction() and get_max_fraction(), and the corresponding question_state
     * right, partial or wrong.
     * @param array $response responses, as returned by
     *      question_attempt_step::get_qt_data().
     * @return array (float, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {

        $maxpoints = 0.0;
        $userpoints = 0.0;
        foreach ($this->answers as $answer) {
            $maxpoints += $answer->points;
            $submittedanswer = $response[$this->get_answer_field_name($answer)] ?? null;

            if ($submittedanswer == null) {
                continue;
            }

            $res = $this->get_regex_for_answer($answer, $submittedanswer);
            if ($res != null) {
                $userpoints += $res[0];
            }
        }

        $fraction = $userpoints / $maxpoints;

        return [$fraction, question_state::graded_state_for_fraction($fraction)];
    }

    /**
     * not possible
     *
     * @return null
     */
    public function get_correct_response() {
        return null;
    }

    /**
     * Given a response, rest the parts that are wrong. Does not clean anything.
     * @param array $response a response
     * @return array a cleaned up response with the wrong bits reset.
     */
    public function clear_wrong_from_response(array $response) {
        return $response;
    }

    /**
     * Checks whether the user is allow to be served a particular file.
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);
        } else {
            return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }

    /**
     * Returns the field name of the frontend input field of given answer
     * @param qtype_regexmatch_common_answer $answer the answer to get the field name for
     * @return string field name
     */
    public function get_answer_field_name(qtype_regexmatch_common_answer $answer) {
        return "gap" . $answer->feedback;
    }

    /**
     * Get the regex with the highest points of given answer for given submitted answer
     * @param qtype_regexmatch_common_answer $answer the answer
     * @param string $submittedanswer answer submitted from a student
     * @return array|null [0] => points, [1] => regex. regex of given $answer, which matches given answer or null if none matches
     */
    public function get_regex_for_answer(qtype_regexmatch_common_answer $answer, string $submittedanswer) {
        $ret = null;

        // Remove \r from the answer, which should not be matched.
        $submittedanswer = str_replace("\r", "", $submittedanswer);

        foreach ($answer->regexes as $regex) {
            $value = qtype_regexmatch_common_try_regex($answer, $regex, $submittedanswer);

            if ($value > 0.0) {
                $points = $answer->points * $value * ($regex->percent / 100.0);
                if ($ret == null || $points > $ret[0]) {
                    $ret = [$points, $regex];
                }
            }
        }

        return $ret;
    }

    /**
     * Get the question state for given answer for given submitted answer
     * @param qtype_regexmatch_common_answer $answer the answer to get the state for
     * @param string|null $submittedanswer answer submitted from a student
     * @return question_state question_state::$gradedwrong, question_state::$gradedright or question_state::$gradedpartial.
     */
    public function get_question_state_for_answer(qtype_regexmatch_common_answer $answer, $submittedanswer) {
        if($submittedanswer == null) {
            return question_state::$gradedwrong;
        }

        $ret = $this->get_regex_for_answer($answer, $submittedanswer);
        if ($ret == null || $ret[0] == 0) {
            return question_state::$gradedwrong;
        } else if ($ret[0] == $answer->points) {
            return question_state::$gradedright;
        }
        return question_state::$gradedpartial;
    }
}
