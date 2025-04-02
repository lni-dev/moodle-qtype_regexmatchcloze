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

const SEPARATOR_KEY = 'separator=';
const FEEDBACK_KEY = 'feedback=';
const SIZE_KEY = "size=";
const POINTS_KEY = 'points=';
const COMMENT_KEY = 'comment=';

const REGEXMATCH_ALLOWED_KEYS = array(SEPARATOR_KEY, COMMENT_KEY);

const ALLOWED_OPTIONS = array('I', 'D', 'P', 'R', 'O', 'S', 'T', 'i', 'd', 'p', 'r', 'o', 's', 't');

/** 
*This holds the definition of a particular question of this type. 
*If you load three questions from the question bank, then you will get three instances of 
*that class. This class is not just the question definition, it can also track the current
*state of a question as a student attempts it through a question_attempt instance. 
*/


/**
 * Represents a regexmatchcloze question.
 *
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexmatchcloze_question extends question_graded_automatically_with_countback {

    public function start_attempt(
        question_attempt_step $step,
                              $variant
    ) {
        // probably not needed
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
            ($response['answer'] || $response['answer'] === '0');
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_regexmatch');
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key(
            $prevresponse, $newresponse, 'answer');
    }

    public function get_expected_data() {
        return array('answer' => PARAM_RAW);
    }

    public function summarise_response(array $response) {
        return $response['answer'] ?? null;
    }

    public function un_summarise_response(string $summary): array {
        if (!empty($summary)) {
            return ['answer' => $summary];
        } else {
            return [];
        }
    }

    public function constructRegex(string $regex, qtype_regexmatch_answer $options): string {
        $constructedRegex = $regex;

        if($options->infspace)
            $constructedRegex = str_replace(" ", "(?:[ \t]+)", $constructedRegex);

        if($options->pipesemispace)
            $constructedRegex = str_replace(
                array(";", "\|"),
                array("(?:[ \t]*[;\\n][ \t]*)", "(?:[ \t]*\|[ \t]*)"),
                $constructedRegex
            );

        if($options->redictspace)
            $constructedRegex = str_replace(
                array("<", "<<", ">", ">>"),
                array("(?:[ \t]*<[ \t]*)", "(?:[ \t]*<<[ \t]*)", "(?:[ \t]*>[ \t]*)", "(?:[ \t]*>>[ \t]*)"),
                $constructedRegex
            );

        // preg_match requires a delimiter ( we use "/").
        // replace all actual occurrences of "/" in $regex->answer with an escaped version ("//").
        // Add "^(?:" at the start of the regex and ")$" at the end, to match from start to end.
        // and put the regex in a non-capturing-group, so the function of the regex does not change (eg. "^a|b$" vs "^(?:a|b)$")
        $toEscape = array("/");
        $escapeValue = array("\\/");
        $constructedRegex = "/^(?:" . str_replace($toEscape, $escapeValue, $constructedRegex) . ")$/";

        // Set Flags based on enabled options
        if($options->ignorecase)
            $constructedRegex .= "i";

        if($options->dotall)
            $constructedRegex .= "s";

        return $constructedRegex;
    }

    /**
     * @param string $answer answer submitted from a student
     * @return mixed|null regex of {@link self::$answers}, which matches given answer or null if none matches
     */
    public function get_regex_for_answer(string $answer) {
        $ret = null;

        foreach ($this->answers as $correctAnswer) {

            // remove \r from the answer, which should not be matched.
            $processedAnswer = str_replace("\r", "", $answer);

            // Trim answer if enabled.
            if($correctAnswer->trimspaces) {
                $processedAnswer = trim($processedAnswer);
            }

            if($correctAnswer->matchAnyOrder) {
                $answerLines = explode($correctAnswer->separator, $processedAnswer);
                $answerLineCount = count($answerLines);

                // Trim all answers if enabled.
                if($correctAnswer->trimspaces) {
                    for ($i = 0; $i < $answerLineCount; $i++) {
                        $answerLines[$i] = trim($answerLines[$i]);
                    }
                }

                foreach ($correctAnswer->regexes as $r) {
                    $r = $this->constructRegex($r, $correctAnswer);

                    $i = 0;
                    for (; $i < $answerLineCount; $i++) {
                        if($answerLines[$i] === null)
                            continue;
                        if(preg_match($r, $answerLines[$i]) == 1) {
                            break;
                        }
                    }

                    if($i !== $answerLineCount) {
                        $answerLines[$i] = null;
                    }
                }

                $wrongAnswerCount = 0;
                foreach ($answerLines as $answerLine) {
                    if($answerLine !== null) $wrongAnswerCount++;
                }

                $maxPoints = count($correctAnswer->regexes);
                $answerCountDif = $maxPoints - $answerLineCount;
                $points = max(0, $maxPoints - abs($answerCountDif) - ($wrongAnswerCount - max(0, -$answerCountDif)));

                $fraction = $correctAnswer->fraction * (floatval($points) / floatval($maxPoints));
                $ret = new qtype_regexmatch_answer($correctAnswer->id, $correctAnswer->answer, $fraction, $correctAnswer->feedback, $correctAnswer->feedbackformat);
            } else {
                // Construct regex based on enabled options
                $constructedRegex = $this->constructRegex($correctAnswer->regexes[0], $correctAnswer);

                // debugging("constructedRegex: $constructedRegex");
                // debugging("processedAnswer: $processedAnswer");
                if(preg_match($constructedRegex, $processedAnswer) == 1) {
                    if($ret == null || $correctAnswer->fraction > $ret->fraction) {
                        $ret = $correctAnswer;
                    }
                }
            }
        }

        return $ret;
    }

    public function grade_response(array $response): array {
        $submittedAnswer = $response['answer'] ?? null;
        $fraction = 0;

        if($submittedAnswer != null) {
            $regex = $this->get_regex_for_answer($submittedAnswer);
            if($regex != null) {
                $fraction = $regex->fraction;
            }
        }

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

}
