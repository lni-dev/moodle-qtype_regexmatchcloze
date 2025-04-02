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
 * regexmatchcloze question renderer class.
 *
 * @package    qtype
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for regexmatchcloze questions.
 *
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexmatchcloze_renderer extends qtype_renderer {
    public function formulation_and_controls(
        question_attempt $qa,
        question_display_options $options
    ): string {

        // regexmatch question
        /**
         * @var $question qtype_regexmatchcloze_question
         */
        $question = $qa->get_question();


        // Text to be displayed for this question (set when creating)
        $questiontext = $question->format_questiontext($qa);

        // The last answer, that the student entered (if any)
        $currentanswer = $qa->get_last_qt_var('answer');

        $result = "";

        // If the regex was not able to be parsed, show an error.
        foreach ($question->answers as $correctAnswer) {
            if($correctAnswer->regexes[0] === "") {
                \core\notification::add(
                    "Invalid regex syntax. It may be an old regex, please edit.",
                    \core\notification::WARNING
                );
                break;
            }
        }

        // Add question text
        $result .= html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        // Add input field
        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array(
            'type' => 'text',
            'name' => $inputname,
            'value' => $currentanswer,
            'id' => $inputname,
            'size' => 80,
            'class' => 'form-control d-inline',
        );

        if ($options->readonly)
            $inputattributes['readonly'] = 'readonly';

        $result .= html_writer::tag('textarea', $currentanswer, $inputattributes);

        /* if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }*/
        return $result;
    }

    public function specific_feedback(question_attempt $qa): string {
        /* @var qtype_regexmatch_question $question */
        $question = $qa->get_question();

        // The last answer, that the student entered (if any)
        $currentanswer = $qa->get_last_qt_var('answer');

        $feedback = '';
        if($currentanswer != null) {
            $regex = $question->get_regex_for_answer($currentanswer);

            if($regex != null) {
                $feedback = $question->format_text(
                    $regex->feedback,
                    $regex->feedbackformat,
                    $qa,
                    'question', 'answerfeedback',
                    $regex->id
                );
            }
        }

        return $feedback;
    }

    public function correct_response(question_attempt $qa): string {
        // Cannot generate correct response from regex.
        return '';
    }
}
