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
 * Strings for component 'qtype_regexmatchcloze', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'regexmatchcloze';
$string['pluginname_help'] = 'Create a Regexmatch Cloze question type where every gap is defined through regular expressions.';
$string['pluginname_link'] = 'question/type/regexmatchcloze';
$string['pluginnameadding'] = 'Adding a Regexmatch Cloze question';
$string['pluginnameediting'] = 'Editing a Regexmatch Cloze question';
$string['pluginnamesummary'] = 'A Regexmatch Cloze question allows defining every gaps through regular expressions.';
$string['gap-number'] = 'Gap {$a}';
$string['error-no-gaps'] = 'The question text must contain at least one gap ("[[1]]").';
$string['error-duplicated-gap'] = 'The question text contains the gap [[{$a}]] twice.';
$string['error-no-such-gap'] = 'The corresponding gap does not exist in the question text.';
$string['error-gap-not-defined'] = 'Gap {$a} is not defined below.';
$string['gap-num'] = 'Gap {$a}:';
$string['empty-answer'] = 'none';
$string['empty-feedback'] = 'No feedback.';
$string['gap-number_help'] = /** @lang Markdown */
    'The following syntax must be respected:
```
[[regex]] /OPTIONS/
%50 [[regex with 50% points]] /OPTIONS/
%10 [[regex with 10% points]] /OPTIONS/
separator=,
points=5
size=10
feedback=text
comment=text
```
The following example matches `ls -la` (5 points) and `ls` (1 point). No extra options are enabled (only the default options are enabled):
```
[[ls -la]]//
%20 [[ls]]//
points=5
```
A more concrete description (with examples) can be found [here](https://github.com/lni-dev/moodle-qtype_regexmatch/blob/regexmatchcloze_dev/usage-examples.md).

The keys `separator`, `points`, `size`, `feedback` and `comment` are optional. `separator` is described in help-field of the options.
`points` describes the maximum points for this gap (default: 1).
`size` describes the size of the input field (default: 5). `feedback` is the feedback for this field shown to the user. 
`comment` is a text field only visible in the question edit form. 

`/OPTIONS/` are described in the help-field of the options below. If no options are enabled or disabled an empty `//` must be present.

`regex` is a regular expression in the [PCRE syntax](https://www.php.net/manual/en/reference.pcre.pattern.syntax.php).
The regex must be between double square brackets (\[\[\]\]). A short description of the most important regex features:

|        |                    Structures                     |
|:------:|:-------------------------------------------------:|
|  abc   |                   Matches "abc"                   |
| [abc]  | Matches any of the characters inside the brackets |
| [^abc] |   Matches any character NOT inside the brackets   |
| ab\|cd |                Match "ab" or "cd"                 |
| (abc)  |           Matches the subpattern "abc"            |
|   \    |      Escape character for .^$*+-?()[]{}\\\|       |

|        |        Quantifiers        |
|:------:|:-------------------------:|
|   a*   |    Zero or more of "a"    |
|   a+   |    One or more of "a"     |
|   a?   |    Zero or one of "a"     |
|  a{n}  |    Exactly n times "a"    |
| a{n,}  |     n or more of "a"      |
| a{,m}  |     m or less of "a"      |
| a{n,m} | Between n and m times "a" |

|    |      Characters & Boundaries      |
|:--:|:---------------------------------:|
| \w |  Any word character (a-z 0-9 _)   |
| \W |      Any non word character       |
| \s | Whitespace (space, tab, new line) |
| \S |   Any non whitespace character    |
| \d |           Digits (0-9)            |
| \D |      Any non digit character      |
| .  |   Any character except newline    |
| \b |           Word boundary           |
| \B |        Not a word boundary        |

The regex anchors "$" and "^" cannot be used. If they should be matched as literals, they can be escaped: "\\$", "\\^".
';