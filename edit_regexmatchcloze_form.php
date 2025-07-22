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
 * Defines the editing form for the regexmatchcloze question type.
 *
 * @package    qtype
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * regexmatchcloze question editing form definition.
 *
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexmatchcloze_edit_form extends question_edit_form {

    /**
     * @param MoodleQuickForm $mform
     */
    protected function definition_inner($mform) {

        $mform->removeElement('defaultmark', true);

        $this->add_per_answer_fields(
            $mform,
            get_string('gap-number', 'qtype_regexmatchcloze', '{no}'),
            question_bank::fraction_options()
        );

        // Add Help Button to the first to 5th answer text field
        // Add (?) / help button
        for ($i = 0; $i < 10; $i++) {
            $mform->addHelpButton("answer[$i]", 'gap-number', 'qtype_regexmatchcloze', '', true);
            $mform->addHelpButton("options[$i]", 'options', 'qtype_regexmatchcloze', '', true);
            $mform->addHelpButton("default-options[$i]", 'default_options', 'qtype_regexmatchcloze', '', true);
        }

        $this->add_interactive_settings();
    }

    /**
     * Get the list of form elements to repeat, one for each answer.
     * @param MoodleQuickForm $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $answersoption reference to return the name of $question->options
     *      field holding an array of answers
     * @return array of form fields.
     */
    protected function get_per_answer_fields(
        $mform,
        $label,
        $gradeoptions,
        &$repeatedoptions,
        &$answersoption
    ) {
        $repeated = array();

        // Help button added in definition_inner
        $repeated[] = $mform->createElement('textarea',
            'answer',
            $label,
            array('size' => 1000, 'rows' => 7)
        );

        $repeated[] = $mform->createElement('static', 'options', get_string('options', 'qtype_regexmatchcloze'), 'I, D, P, R, O');
        $repeated[] = $mform->createElement('static', 'default-options', get_string('default_options', 'qtype_regexmatchcloze'), 'S, T');

        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $answersoption = 'answers';
        return $repeated;
    }

    /**
     * Perform an preprocessing needed on the data passed to {@link set_data()}
     * before it is used to initialise the form.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    /**
     * validate regex syntax
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($fromform, $files): array {
        $errors = parent::validation($fromform, $files);

        $questiontext = $fromform['questiontext']['text'];

        preg_match_all("/\[\[(?P<number>[0-9]+)\]\]/", $questiontext, $matches);

        $gaps = array();
        $max = -1;
        foreach ($matches['number'] as $number) {
            $max = max($max, $number);
            if(array_key_exists($number, $gaps)) {
                $errors['questiontext'] = get_string('error-duplicated-gap', 'qtype_regexmatchcloze', $number);
            }
            $gaps[$number] = false;
        }

        if($max == -1) {
            $errors['questiontext'] = get_string('error-no-gaps', 'qtype_regexmatchcloze');
        }

        $answers = $fromform['answer'];

        foreach ($answers as $key => $answer) {
            $keyp1 = $key+1;
            $fromform['feedback'][$key]['text'] = "$$keyp1";
            if ($answer !== '') {
                if(!array_key_exists($key+1, $gaps)) {
                    $errors["answer[$key]"] = get_string('error-no-such-gap', 'qtype_regexmatchcloze');
                    continue;
                }
                $gaps[$key+1] = true;


                $remaining = preg_replace("/\\r/", "", $fromform['answer'][$key]);

                //check syntax
                if(preg_match('%^(\[\[.*\]\][ \\n]*)+/[a-zA-Z]*/([ \\n]*\\%[0-9]+[ \\n]*(\[\[.*\]\][ \\n]*)+/[a-zA-Z]*/[ \\n]*)*.*$%s', $remaining) != 1) {
                    $errors["answer[$key]"] = get_string('valerror_illegalsyntax', 'qtype_regexmatchcloze');
                } else {

                    // First look for the options "]] /OPTIONS/"
                    if(preg_match("%]][ \\n]*/[a-zA-Z]*/%", $remaining, $matches, PREG_OFFSET_CAPTURE)) {
                        $first = true;
                        do {
                            if ($first) {
                                $first = false;
                                $percent = 100;
                                $percentoffset = 0;
                            } else {

                                if (!preg_match("%]][ \\n]*/[a-zA-Z]*/%", $remaining, $matches, PREG_OFFSET_CAPTURE)) {
                                    //Invalid syntax.
                                    $errors["answer[$key]"] = get_string('valerror_illegalsyntax', 'qtype_regexmatchcloze');
                                    return $errors;
                                }

                                preg_match("/%[0-9]+/", $remaining, $percentmatch);
                                $percent = substr($percentmatch[0], 1);
                                $percentoffset = strlen($percentmatch[0]);
                            }

                            if($percent < 0 || $percent > 100) {
                                $errors["answer[$key]"] = get_string('valerror_illegalpercentage', 'qtype_regexmatchcloze');
                                return $errors;
                            }

                            $index = intval($matches[0][1]);

                            // Regexes without the last "]]". E.g.: [[regex1]] [[regex2
                            $regularexpressions = substr($remaining, $percentoffset, $index - $percentoffset);
                            $regularexpressions = trim($regularexpressions); // Now trim all spaces at the beginning and end
                            if(!qtype_regexmatch_common_str_starts_with($regularexpressions, '[[')) {
                                $a = array(
                                    'context' => substr($remaining, 0, $index - $percentoffset),
                                    'actual' => substr($regularexpressions, 0, 1),
                                    'expected' => '[['
                                );
                                $errors["answer[$key]"] = get_string('valerror_illegalchar', 'qtype_regexmatchcloze', $a);

                            }
                            $regularexpressions = substr($regularexpressions, 2); // remove the starting "[["

                            if(preg_match('/(?<!\\\\)(\\\\\\\\)*[$^]/', $regularexpressions) == 1) {
                                $errors["answer[$key]"] = get_string('dollarroofmustbeescaped', 'qtype_regexmatchcloze');
                            }

                            // Options E.g.: "OPTIONS"
                            $options = substr($matches[0][0], 2); // first remove the "]]" at the beginning
                            $options = trim($options); // Now trim all spaces at the beginning and end
                            $options = substr($options, 1, strlen($options) - 2); // remove first and last "/"

                            foreach (str_split($options) as $option) {
                                $found = false;
                                foreach (QTYPE_REGEXMATCH_CLOZE_ALLOWED_OPTIONS as $allowed) {
                                    if ($option == $allowed) {
                                        $found = true;
                                    }
                                }

                                if (!$found) {
                                    $errors["answer[$key]"] =
                                        get_string('valerror_illegaloption', 'qtype_regexmatchcloze', $option);
                                    return $errors;
                                }
                            }

                            // Key Value pairs or more regexes (cloze)
                            $remaining = substr($remaining, $index + strlen($matches[0][0]));
                            $remaining = trim($remaining);

                        } while (qtype_regexmatch_common_str_starts_with($remaining, "%"));

                        // Key Value pairs
                        $keyvaluepairs = $remaining;

                        if($keyvaluepairs != '') {
                            $nextkey = 0;
                            foreach (preg_split("/\\n/", $keyvaluepairs) as $keyvaluepair) {
                                if(trim($keyvaluepair) == '') {
                                    continue;
                                }
                                if(preg_match("/^[a-z]+=/", $keyvaluepair, $matches)) {
                                    $match = $matches[0];
                                    $value = trim(substr($keyvaluepair, strlen($match)));

                                    if($match === QTYPE_REGEXMATCH_COMMON_POINTS_KEY) {
                                        if(!preg_match("/(^0+\\.[1-9][0-9]*$)|(^0*[1-9][0-9]*(\\.[0-9]+)?$)/", $value)) {
                                            $errors["answer[$key]"] = get_string('valerror_pointsmustbenum', 'qtype_regexmatchcloze');
                                        }
                                    }

                                    if($match === QTYPE_REGEXMATCH_COMMON_SIZE_KEY) {
                                        if(!preg_match("/(^0*[1-9][0-9]*$)/", $value)) {
                                            $errors["answer[$key]"] = get_string('valerror_sizemustbenum', 'qtype_regexmatchcloze');
                                        }
                                    }

                                    $found = false;
                                    for (; $nextkey < count(QTYPE_REGEXMATCH_CLOZE_ALLOWED_KEYS); $nextkey++) {
                                        if($match == QTYPE_REGEXMATCH_CLOZE_ALLOWED_KEYS[$nextkey]) {
                                            $found = true;
                                            break;
                                        }
                                    }

                                    if(!$found) {
                                        $isallowed = false;
                                        foreach (QTYPE_REGEXMATCH_CLOZE_ALLOWED_KEYS as $allowed) {
                                            if ($allowed == $match) {
                                                $isallowed = true;
                                                break;
                                            }
                                        }
                                        if($isallowed) {
                                            $errors["answer[$key]"] = get_string('valerror_illegalkeyorder', 'qtype_regexmatchcloze', implode(', ', QTYPE_REGEXMATCH_CLOZE_ALLOWED_KEYS));
                                        } else  {
                                            $errors["answer[$key]"] = get_string('valerror_unkownkey', 'qtype_regexmatchcloze', $match);
                                        }

                                    }

                                } else {
                                    $errors["answer[$key]"] = get_string('valerror_illegalsyntaxspecific', 'qtype_regexmatchcloze', $keyvaluepair);
                                }
                            }
                        }
                    }
                }

            }
        }

        foreach ($gaps as $key => $value) {
            if(!$value) {
                $errors['questiontext'] = get_string('error-gap-not-defined', 'qtype_regexmatchcloze', $key);
            }
        }

        return $errors;
    }

    /**
     * Question type name.
     * @return the question type name, should be the same as the name() method
     *      in the question type class.
     */
    public function qtype() {
        return 'regexmatchcloze';
    }
}
