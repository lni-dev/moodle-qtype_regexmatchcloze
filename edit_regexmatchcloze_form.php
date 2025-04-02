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
        $this->add_per_answer_fields(
            $mform,
            get_string('regex-number', 'qtype_regexmatch', '{no}'),
            question_bank::fraction_options()
        );

        // Add Help Button to the first to 5th answer text field
        // Add (?) / help button
        for ($i = 0; $i < 10; $i++) {
            $mform->addHelpButton("answer[$i]", 'regex', 'qtype_regexmatch', '', true);
            $mform->addHelpButton("options[$i]", 'options', 'qtype_regexmatch', '', true);
            $mform->addHelpButton("default-options[$i]", 'default_options', 'qtype_regexmatch', '', true);
        }

        $this->add_interactive_settings();
    }

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
            array('size' => 1000)
        );

        $repeated[] = $mform->createElement('static', 'options', get_string('options', 'qtype_regexmatch'), 'I, D, P, R, O');
        $repeated[] = $mform->createElement('static', 'default-options', get_string('default_options', 'qtype_regexmatch'), 'S, T');


        $repeated[] = $mform->createElement('select',
            'fraction',
            get_string('gradenoun'),
            $gradeoptions
        );

        $repeated[] = $mform->createElement('editor',
            'feedback',
            get_string('feedback', 'question'),
            array('rows' => 5),
            $this->editoroptions
        );

        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        return $repeated;
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function validation($fromform, $files): array {
        $errors = parent::validation($fromform, $files);

        $answers = $fromform['answer'];

        $answerCount = 0;
        $maxGrade = false;

        foreach ($answers as $key => $answer) {
            if ($answer !== '') {
                $answerCount++;
                if ($fromform['fraction'][$key] == 1) $maxGrade = true;


                // check syntax
                if(preg_match('/(?<!\\\\)(\\\\\\\\)*[$^]/', $fromform['answer'][$key]) == 1) {
                    $errors["answer[$key]"] = get_string('dollarroofmustbeescaped', 'qtype_regexmatch');
                }

                //check syntax
                if(preg_match('%^(\[\[.*\]\]\\n? *)+/[a-zA-Z]*/.*$%s', $fromform['answer'][$key]) != 1) {
                    $errors["answer[$key]"] = get_string('valerror_illegalsyntax', 'qtype_regexmatch');
                } else {
                    if(preg_match("%]][ \\n]*/[a-zA-Z]*/%", $fromform['answer'][$key], $matches, PREG_OFFSET_CAPTURE)) {
                        $index = intval($matches[0][1]);

                        // Options E.g.: "OPTIONS"
                        $options = substr($matches[0][0], 2); // first remove the "]]" at the beginning
                        $options = trim($options); // Now trim all spaces at the beginning and end
                        $options = substr($options, 1, strlen($options) - 2); // remove first and last "/"

                        foreach (str_split($options) as $option) {
                            $found = false;
                            foreach (ALLOWED_OPTIONS as $allowed) {
                                if ($option == $allowed) {
                                    $found = true;
                                }
                            }

                            if(!$found) {
                                $errors["answer[$key]"] = get_string('valerror_illegaloption', 'qtype_regexmatch', $option);
                            }
                        }

                        // Key Value pairs
                        $keyValuePairs = substr($fromform['answer'][$key], $index + strlen($matches[0][0]));
                        $nextKey = 0;
                        foreach (preg_split("/\\n/", $keyValuePairs) as $keyValuePair) {
                            if(preg_match("/[a-z]+=/", $keyValuePair, $matches)) {
                                $match = $matches[0];
                                $found = false;
                                for (; $nextKey < count(REGEXMATCH_ALLOWED_KEYS); $nextKey++) {
                                    if($match == REGEXMATCH_ALLOWED_KEYS[$nextKey]) {
                                        $found = true;
                                        break;
                                    }
                                }

                                if(!$found) {
                                    $isAllowed = false;
                                    foreach (REGEXMATCH_ALLOWED_KEYS as $allowed) {
                                        if ($allowed == $match) {
                                            $isAllowed = true;
                                            break;
                                        }
                                    }
                                    if($isAllowed) {
                                        $errors["answer[$key]"] = get_string('valerror_illegalkeyorder', 'qtype_regexmatch', implode(', ', REGEXMATCH_ALLOWED_KEYS));
                                    } else  {
                                        $errors["answer[$key]"] = get_string('valerror_unkownkey', 'qtype_regexmatch', $match);
                                    }

                                }

                            }
                        }
                    }
                }





            } else if ($fromform['fraction'][$key] != 0 || !html_is_blank($fromform['feedback'][$key]['text'])) {
                $errors["answer[$key]"] = get_string('fborgradewithoutregex', 'qtype_regexmatch');
                $answerCount++;
            }


        }

        if ($answerCount==0)
            $errors['answer[0]'] = get_string('notenoughregexes', 'qtype_regexmatch');

        if (!$maxGrade)
            $errors['answer[0]'] = get_string('fractionsnomax', 'question');

        return $errors;
    }

    public function qtype() {
        return 'regexmatchcloze';
    }
}
