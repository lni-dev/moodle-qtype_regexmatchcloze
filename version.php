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
 * regexmatchcloze question type version information.
 *
 * @package    qtype_regexmatchcloze
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'qtype_regexmatchcloze';

/*
 * format: yyyymmddnn<br>
 * nn: number to increase if needed<br>
 * <br>
 * underscores are not allowed inside the number
 */
$plugin->version   = 2025072200;

$plugin->requires  = 2022112807; // Moodle 4.1.7

$plugin->maturity  = MATURITY_ALPHA;
