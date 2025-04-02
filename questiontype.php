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
 * Question type class for the regexmatchcloze question type.
 *
 * @package    qtype
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /*https://docs.moodle.org/dev/Question_types#Question_type_and_question_definition_classes*/


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/regexmatchcloze/question.php');


/**
 * The regexmatchcloze question type.
 *
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexmatchcloze extends question_type {

    /**
     * Response cannot be analysed, because the method get_possible_responses cannot be implemented.
     * @return false
     */
    public function can_analyse_responses() {
        return false;
    }

    public function save_question_options($question) {
        parent::save_question_options($question);
        $this->save_question_answers($question);
        $this->save_hints($question);
    }

    public function extra_question_fields() {
        return null;
    }

    public function extra_answer_fields() {
        return null;
    }

    protected function make_answer($answer): qtype_regexmatch_answer {
        return new qtype_regexmatch_answer(
            $answer->id,
            $answer->answer,
            $answer->fraction,
            $answer->feedback,
            $answer->feedbackformat
        );
    }

    public function get_random_guess_score($questiondata) {
        return 0;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        global $CFG;
        require_once($CFG->dirroot.'/question/type/regexmatch/question.php');

        if (!isset($data['@']['type']) || $data['@']['type'] != 'question_regexmatch') {
            return false;
        }

        $qo = $format->import_headers($data);
        $qo->qtype = $data['@']['type'];

        // Run through the answers.
        $answers = $data['#']['answer'];
        $acount = 0;

        $qo->answer = [];
        $qo->answerformat = [];
        $qo->fraction = [];
        $qo->feedback = [];
        $qo->feedbackformat = [];

        foreach ($answers as $answer) {
            $ans = $format->import_answer($answer, false, $format->get_format($qo->questiontextformat));
            $qo->answer[$acount] = $ans->answer['text'];
            $qo->fraction[$acount] = $ans->fraction;
            $qo->feedback[$acount] = $ans->feedback;
            ++$acount;
        }

        $format->import_hints($qo, $data);
        return $qo;
    }

    /**
     * @param qtype_regexmatch_question $question
     * @param qformat_xml $format
     * @param $extra
     * @return string
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        $expout = parent::export_to_xml($question, $format, $extra);

        if(!$expout)
            $expout = '';

        $extraanswersfields = $this->extra_answer_fields();
        if (is_array($extraanswersfields))
            array_shift($extraanswersfields);

        foreach ($question->options->answers as $answer) {
            $extra = '';
            if (is_array($extraanswersfields)) {
                foreach ($extraanswersfields as $field) {
                    if(!isset($answer->$field) || $answer->$field == 0)
                        continue;
                    $exportedvalue = $format->xml_escape($answer->$field);
                    $extra .= "      <{$field}>{$exportedvalue}</{$field}>\n";
                }
            }

            $expout .= $format->write_answer($answer, $extra);
        }

        $expout .= $format->write_hints($question);

        return $expout;
    }
}
