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
 * @package    qtype_regexmatchcloze
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
    /**
     * Generate the display of the formulation part of this regexmatchcloze question.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function formulation_and_controls(
        question_attempt $qa,
        question_display_options $options
    ): string {
        /* @var $question qtype_regexmatchcloze_question */
        $question = $qa->get_question();

        // Text to be displayed for this question (set when creating)
        $questiontext = $question->format_questiontext($qa);

        $inputattributes = [
            'type' => 'text',
            'class' => 'form-control d-inline',
        ];
        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
        }

        foreach ($question->answers as $answer) {
            $key = $answer->feedback; // index is stored in feedback
            $inputname = $qa->get_qt_field_name($question->get_answer_field_name($answer));
            $currentanswer = $qa->get_last_qt_var($question->get_answer_field_name($answer)); // The last answer, that the student entered (if any)
            $inputattributes['name'] = $inputname;
            $inputattributes['value'] = $currentanswer;
            $inputattributes['id'] = $inputname;
            $inputattributes['size'] = $answer->size;

            $feedbackimage = '';
            if ($options->correctness) {
                $submittedanswer = $qa->get_last_qt_var($question->get_answer_field_name($answer));
                $qs = $question->get_question_state_for_answer($answer, $submittedanswer);
                $feedbackclass = $qs->get_feedback_class();
                $inputattributes['class'] .= ' ' . $feedbackclass;
                $feedbackimage = $this->output->pix_icon('i/grade_' . $feedbackclass, get_string($feedbackclass, 'question'));;
            }

            $input = html_writer::empty_tag('input', $inputattributes) . $feedbackimage;
            $questiontext = str_replace("[[$key]]", $input, $questiontext);
        }

        $result = "";

        // Add question text
        $result .= html_writer::tag('div', $questiontext);

        return $result;
    }

    /**
     * Generate the specific feedback. This is feedback for every gap combined.
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    public function specific_feedback(question_attempt $qa): string {
        /* @var qtype_regexmatchcloze_question $question */
        $question = $qa->get_question();

        $feedback = '';

        foreach ($question->answers as $answer) {
            $submittedanswer = $qa->get_last_qt_var($question->get_answer_field_name($answer));
            $res = $question->get_regex_for_answer($answer, $submittedanswer);

            if ($res == null) {
                $res = ['0'];
            }

            $key = $answer->feedback; // index is stored in feedback
            $feedback .= get_string('gap-num', 'qtype_regexmatchcloze', $key . " ($res[0]/$answer->points)") . ' ' . $answer->feedbackvalue . "<br>";
        }

        return $feedback;
    }

    /**
     * Cannot generate a correct response.
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string empty string.
     */
    public function correct_response(question_attempt $qa): string {
        // Cannot generate correct response from regex.
        return '';
    }
}
