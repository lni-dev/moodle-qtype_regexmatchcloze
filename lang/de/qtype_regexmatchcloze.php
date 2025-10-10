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
 * Strings for component 'qtype_regexmatchcloze', language 'de', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype_regexmatchcloze
 * @subpackage regexmatchcloze
 * @copyright  2025 Linus Andera (linus@linusdev.de)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Regexmatch Lückentext';
$string['pluginname_help'] = 'Erstelle einen Regexmatch Lückentext Fragetypen. Bei dem jede Lücke durch reguläre Ausdrücke definiert werden kann';
$string['pluginname_link'] = 'question/type/regexmatchcloze';
$string['pluginnameadding'] = 'Hinzufügen einer Regexmatch Lückentext Frage';
$string['pluginnameediting'] = 'Bearbeiten einer Regexmatch Lückentext Frage';
$string['pluginnamesummary'] = 'Der Regexmatch Lückentext ermöglicht es, jede Lücke durch reguläre Ausdrücke zu definieren';
$string['gap-number'] = 'Lücke {$a}';
$string['error-no-gaps'] = 'Der Fragetext muss mindestens eine Lücke enthalten ("[[1]]").';
$string['error-duplicated-gap'] = 'Der Fragetext enthählt die Lücke [[{$a}]] doppelt.';
$string['error-no-such-gap'] = 'Die dazugehörige Lücke ist nicht im Fragetext enthalten.';
$string['error-gap-not-defined'] = 'Die Lücke {$a} ist unten nicht definiert.';
$string['gap-num'] = 'Lücke {$a}:';
$string['empty-answer'] = 'leer';
$string['empty-feedback'] = 'Kein Feedback.';
$string['gap-number_help'] = /* @lang Markdown */
    'Es muss die folgende Syntax eingehalten werden:
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
Das folgende Beispiel findet `ls -la` (5 Punkte) und `ls` (1 Punkt). Keine extra Optionen sind aktiviert (nur die Default-Optionen sind aktiviert):
```
[[ls -la]]//
%20 [[ls]]//
points=5
```
Eine genauere Beschreibung (mit weiteren Beispielen) findet sich [hier](https://github.com/lni-dev/moodle-qtype_regexmatchcloze/blob/master/usage-examples.md).

Die Schlüssel `separator`, `points`, `size`, `feedback` und `comment` sind Optional. `separator=` wird in dem Hilfefeld zu den Optionen beschrieben.

`points` beschreibt die maximal erreichbaren Punkte für diese Lücke (default: 1).
`size` beschreibt die größe des Eingabefeldes (default: 5). `feedback` is das Feedback für diese Lücke, dies wird dem Lernenden angezeigt.
`comment` ist ein Textfeld, welches nur hier sichtbar ist.

`/OPTIONS/` werden in dem Hilfefeld zu den Optionen beschrieben. Falls keine Optionen an oder ausgeschaltet werden müssen
leere Optionen (`//`) angegeben werden.

`regex` ist ein regulärer Ausdruck im [PCRE syntax](https://www.php.net/manual/en/reference.pcre.pattern.syntax.php).
Der reguläre Ausdruck muss sich zwischen doppelten eckigen Klammern (\[\[\]\]) befinden.
Hier ist eine kurze Beschreibung der wichtigsten regex Funktionen:

|        |                Strukturen                |
|:------:|:----------------------------------------:|
|  abc   |               Findet "abc"               |
| [abc]  |    Findet ein Zeichen aus der Klammer    |
| [^abc] | Findet ein Zeichen nicht aus der Klammer |
| ab\|cd |          Findet "ab" oder "cd"           |
| (abc)  |       Findet das Untermuster "abc"       |
|   \    |   Escape Zeichen für .^$*+-?()[]{}\\\|   |

|          |    Wiederholungen    |
|:--------:|:--------------------:|
|    a*    |  Null oder mehr "a"  |
|    a+    |  Ein oder mehr "a"   |
|    a?    |  Null oder Ein "a"   |
|   a{n}   |     Genau n "a"      |
|  a{n,}   |   n oder mehr "a"    |
|  a{,m}   |  m oder weniger "a"  |
|  a{n,m}  | Zwischen n und m "a" |

|    |           Zeichen & Grenzen           |
|:--:|:-------------------------------------:|
| \w |  Irgendein Wort-Zeichen (a-z 0-9 _)   |
| \W |     Irgendein nicht Wort-Zeichen      |
| \s |  Leerzeichen (space, tab, leerzeile)  |
| \S |  Irgendein Zeichen außer Leerzeichen  |
| \d |             Ziffern (0-9)             |
| \D |    Irgendein Zeichen außer Ziffern    |
| .  | Irgendein Zeichen außer Zeilenumbruch |
| \b |              Wortgrenze               |
| \B |           Keine Wortgrenze            |

Die Regex Anker "$" und "^" können nicht verwendet werden. Falls diese als Literal gesucht werden
sollen, können sie escaped werden: "\$", "\^".
';
$string['options'] = "Options";
$string['default_options'] = "Default Options";
$string['options_help'] = /* @lang Markdown */
    'Einige Optionen können aktiviert/deaktiviert werden.
Diese müssen mit einem Schrägstrich (`/`) anfangen und enden. Zum Beispiel: `/PI/`. Jede Option wird durch einen
einzigen großen Buchstaben aktiviert und durch einen kleinen Buchstaben deaktiviert.

**I: Ignoriere Groß-/Kleinschreibung**<br>
Der reguläre Ausdruck wird Groß- und Kleinschreibung ignorieren.

**D: Punkt findet alles**<br>
Alle Punkte (`.`) in dem regulären Ausdruck werden auch Zeilenumbrüche finden.

**P: Semikolons und Pipes**<br>
Shell spezifisch: Alle Semikolons `;` und maskierte Pipes `\|` werden jeweils
durch `([ \t]*[;\n][ \t]*)` und `([ \t]*\|[ \t]*)` ersetzt.
Dadurch finden diese beliebige viele Leerzeichen vor und nach dem Semikolon oder der Pipe.
Zusätzlich kann das Semikolon auch eine Leerzeile finden. Note: Alle Leerzeichen vor und nach
der Pipe / dem Semikolon im Regulären Ausdruck müssen auch innerhalb der Antwort vorkommen.

**R: Umleitungen**<br>
Shell spezifisch: Alle Umleitungen (`<`, `>`, `<<`, `>>`) werden durch z.B. `([ \t]*<[ \t]*)` ersetzt.
Wenn aktiviert können diese Zeichen nicht mehr in anderen Regex-Funktionen verwendet werden (Z.B.: Lookbehind:
`(?<=...)`). Note: Alle Leerzeichen vor und nach der Umleitung im Regulären Ausdruck müssen auch innerhalb
der Antwort vorkommen.

**O: Beliebige Reihenfolge**<br>
Der reguläre Ausdruck sollte aus mindestens zwei regulären Ausdrucken bestehen (`[[regex1]] [[regex2]]`), da bei nur einem regulären Ausdruck die Option nicht verändert.
Die Antworten (Von dem Wert des Schlüssels `separator=` getrennt. Standardmäßig ein Zeilenumbruch.) müssen von einem der regulären Ausdrücke gefunden werden, die
Reihenfolge ist allerdings egal. Jeder regulärer Ausdruck kann nur einmal gefunden werden. Es werden auch Teilmengen gefunden, die Teilpunkte geben. Genaue Berechnung der
Punktzahl findet sich [hier](https://github.com/lni-dev/moodle-qtype_regexmatchcloze/blob/master/usage-examples.md#evaluation).
';
$string['default_options_help'] = /* @lang Markdown */
    'Die folgenden Optionen sind standardmäßig aktiviert und können durch Angabe des jeweiligen (kleinen) Buchstaben deaktiviert werden.

**s: Beliebig Viele Leerzeichen**<br>
Alle Leerzeichen innerhalb des Ausdrucks werden mit `([ \t]+)` ersetzt. Dadurch finden diese ein oder mehr Whitespace Charakter.

**t: Leerzeichen Trimmen**<br>
Leerzeilen zu Beginn und am Ende der Antwort, sowie Leerzeichen zu Beginn und am Ende jeder Zeile
der Antwort, werden ignoriert. Leerzeilen am Ende der Antwort werden immer ignoriert, egal ob diese
Option aktiviert ist oder nicht.';
$string['dollarroofmustbeescaped'] = 'Die Regex Anker "$" und "^" können nicht verwendet werden. Falls diese als Literal gesucht werden sollen, können sie escaped werden: "\\$", "\\^"';
$string['valerror_illegalsyntax'] = 'Ungültige Syntax.';
$string['valerror_illegaloption'] = 'Ungültige Option "{$a}".';
$string['valerror_illegalkeyorder'] = 'Ungültige Schlüssel-Reihenfolge. Erforderliche Reihenfolge: {$a}.';
$string['valerror_unkownkey'] = 'Unbekannter Schlüssel "{$a}".';
$string['valerror_illegalpercentage'] = 'Prozentzahl muss zwischen 0 und 100 liegen.';
$string['valerror_pointsmustbenum'] = 'Die Variable \'points=\' muss auf eine positive Zahl, die nicht null ist, gesetzt werden.';
$string['valerror_illegalsyntaxspecific'] = 'Ungültige Syntax: "{$a}".';
$string['valerror_sizemustbenum'] = 'Die Variable \'size=\' muss auf eine positive natürliche Zahl, die nicht null ist, gesetzt werden.';
$string['valerror_illegalchar'] = 'Ungültige Syntax: "{$a->context}": Ungültiges Zeichen "{$a->actual}". Erwartet wird "{$a->expected}".';
$string['privacy:metadata'] = 'Regexmatch Fragetyp Plugin hat keine User Einstellungen.';
