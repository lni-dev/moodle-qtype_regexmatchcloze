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
 * regexmatchcloze question type  capability definition
 *
 * @package    qtype_regexmatchcloze
 * @copyright  20XX Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$addons = [
    "qtype_regexmatchcloze" => [
        "handlers" => [ // Different places where the add-on will display content.
            'regexmatchcloze' => [ // Handler unique name (can be anything).
                'displaydata' => [
                    'title' => 'regexmatchcloze question',
                    'icon' => '/question/type/regexmatchcloze/pix/icon.png',
                    'class' => '',
                ],
                'delegate' => 'CoreQuestionDelegate', // Delegate (where to display the link to the add-on).
                'method' => 'mobile_get_regexmatchcloze',
                'offlinefunctions' => [
                    'mobile_get_regexmatchcloze' => [], // function in classes/output/mobile.php
                ], // Function needs caching for offline.
                'styles' => [

                ],
            ],
        ],
        'lang' => [
                    ['pluginname', 'qtype_regexmatchcloze'], // matching value in  lang/en/qtype_regexmatchcloze
        ],
    ],
];
