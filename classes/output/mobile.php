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
 * Mobile output class for qtype_regexmatchcloze
 *
 * @package    qtype_regexmatchcloze
 * @copyright  2018 YOUR NAME
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_regexmatchcloze\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Mobile output class for regexmatchcloze question type
 *
 * @package    qtype_regexmatchcloze
 * @copyright  20XX YOUR NAME
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns the regexmatchcloze question type for the quiz the mobile app.
     *
     * @return void
     */
    public static function mobile_get_regexmatchcloze() {
        global $CFG;
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => file_get_contents($CFG->dirroot .'/question/type/regexmatchcloze/mobile/qtype-regexmatchcloze.html'),
                    ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/question/type/regexmatchcloze/mobile/mobile.js'),
        ];
    }
}
