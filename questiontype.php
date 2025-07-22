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

    /**
     * Saves (creates or updates) a question.
     *
     * Uses the unused feedback field of every answer to store the gap index.
     *
     * @param object $question the question object which should be updated. For a
     *      new question will be mostly empty.
     * @param object $form the object containing the information to save, as if
     *      from the question editing form.
     * @param object $course not really used any more.
     * @return object On success, return the new question object. On failure,
     *       return an object as follows. If the error object has an errors field,
     *       display that as an error message. Otherwise, the editing form will be
     *       redisplayed with validation errors, from validation_errors field, which
     *       is itself an object, shown next to the form fields. (I don't think this
     *       is accurate any more.)
     */
    public function save_question($question, $form) {
        // Since we are missing some fields in the ui we must set these to default values before saving.
        foreach ($form->answer as $key => $answerdata) {
            $form->fraction[$key] = 0;
            $form->feedback[$key]['text'] = "";

            if(!$this->is_answer_empty($form, $key)) {
                $form->feedback[$key]['format'] = FORMAT_PLAIN;
                $index = $key + 1;
                $form->feedback[$key]['text'] = "$index"; // feedback text stores the answer index
            }
        }

        return parent::save_question($question, $form);
    }

    /**
     * Saves question-type specific options
     *
     * @param object $question This holds the information from the editing form,
     *      it is not a standard question object.
     * @return bool|stdClass $result->error or $result->notice
     */
    public function save_question_options($question) {
        parent::save_question_options($question);
        $this->save_question_answers($question);
        $this->save_hints($question);
    }

    /**
     * Create a qtype_regexmatch_common_answer
     * @param object $answer the DB row from the question_answers table plus extra answer fields.
     * @return qtype_regexmatch_common_answer
     */
    protected function make_answer($answer): qtype_regexmatch_common_answer {
        return new qtype_regexmatch_common_answer(
            $answer->id,
            $answer->answer,
            $answer->fraction,
            $answer->feedback,
            $answer->feedbackformat
        );
    }

    /**
     * Calculate the score a monkey would get on a question by clicking randomly.
     *
     * @param stdClass $questiondata data defining a question, as returned by
     *      question_bank::load_question_data().
     * @return number 0
     */
    public function get_random_guess_score($questiondata) {
        return 0;
    }

    /**
     * Move all the files belonging to this question, answers or hints from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    /**
     * Delete all the files belonging to this question, answers or hints.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    /**
     * Initialise the common question_definition fields and answers. Also calculates $question->defaultmark
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);

        /**
         * @var qtype_regexmatchcloze_question $q
         */
        $q = $question;
        $maxpoints = 0.0;
        foreach ($q->answers as $answer) {
            $maxpoints += $answer->points;
        }
        $question->defaultmark = $maxpoints;
    }

    /**
     * @param $data mixed import data
     * @param $question mixed unused
     * @param qformat_xml $format import format
     * @param $extra mixed unused
     * @return false|object
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        $questiontype = $data['@']['type'];
        if ($questiontype != $this->name()) {
            return false;
        }

        $qo = $format->import_headers($data);
        $qo->qtype = $questiontype;

        // Run through the answers.
        $answers = $data['#']['answer'];
        $acount = 0;
        foreach ($answers as $answer) {
            $ans = $format->import_answer($answer);
            if (!$this->has_html_answers()) {
                $qo->answer[$acount] = $ans->answer['text'];
            } else {
                $qo->answer[$acount] = $ans->answer;
            }
            $qo->fraction[$acount] = $ans->fraction;
            $qo->feedback[$acount] = $ans->feedback;
            ++$acount;
        }
        return $qo;
    }

    /**
     * @param $question qtype_regexmatchcloze_question question to export
     * @param qformat_xml $format format to export to
     * @param $extra unused
     * @return string exported
     */
    public function export_to_xml($question, qformat_xml $format, $extra=null) {

        foreach ($question->options->answers as $answer) {
            $expout .= $format->write_answer($answer);
        }
        return $expout;
    }
}
