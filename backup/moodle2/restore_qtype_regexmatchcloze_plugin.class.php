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
 * Provides the information to restore regexmatchcloze questions
 *
 * @package    qtype_regexmatchcloze
 * @subpackage backup-moodle2
 * @copyright  2025 Linus Andera (linus@linusdev.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Provides the information to restore regexmatchcloze questions
 */
class restore_qtype_regexmatchcloze_plugin extends restore_qtype_extrafields_plugin {

    /**
     * Process the qtype/regexmatchcloze element
     * @param mixed $data restore data
     */
    public function process_regexmatchcloze($data) {
    }
}
