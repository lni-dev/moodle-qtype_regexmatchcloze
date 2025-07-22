# Regexmatchcloze Syntax
This file contains information used for both the regexmatchcloze question type.

## Regexmatch Cloze Syntax
Legend:
- `<options>`: Options specified by a single letter.
- `<regex>`: The regular expression.
- `<separator-char>`: separator the student must use, if the match any order option ist enabled.
- `<comment-text>`: comment. Only visible to the editor. Must not contain `/` as last character. Single line.
- `<points>`: max possible points for this gap. Decimal number greater than zero.
- `<size-int>`: Size of the input field as integer. Must be greater than zero.
- `<feedback-text>`: feedback text. Must not contain `/` as last character. Single line.
- spaces: All spaces in the syntax are optional.

**Syntax for a single regular expression**:
```
[[<regex>]] /<options>/
%<percent-1> [[<regex-1>]] /<options-1>/
%<percent-2> [[<regex-2>]] /<options-2>/
points=<points>
size=<size-int>
feedback=<feedback-text>
comment=<comment-text>
```
A line like `%<percent-x> [[<regex-x>]] /<options-x>/` describes
an alternative regex, which gives `<percent-x>` percent of
the maximal possible points.

**Syntax for multiple regular expression (Option `O` enabled)**:
```
[[<regex-01>]] [[<regex-02>]] [[<regex-03>]] /O<options>/
%<percent-1> [[<regex-11>]] [[<regex-12>]] [[<regex-13>]] /O<options-1>/
%<percent-2> [[<regex-2>]] /<options-2>/
separator=<seperator-char>
points=<points>
size=<size-int>
feedback=<feedback-text>
comment=<comment-text>
```
```
[[<regex-01>]]
[[<regex-02>]]
[[<regex-03>]]
/O<options>/
%<percent-1> [[<regex-11>]]
[[<regex-12>]]
[[<regex-13>]]
/O<options-1>/
%<percent-2> [[<regex-2>]] /<options-2>/
separator=<seperator-char>
points=<points>
size=<size-int>
feedback=<feedback-text>
comment=<comment-text>
```

**Additional rules**:
- The order of `points`, `size`, `feedback` and `comment` must always
be the same.

### Question text
Inside the question text the gaps are marked using [[1]], [[2]], [[3]], [[4]].
The gap description are written in a separate input field for each gap below.

### Examples
**Example question text**
```
Name a color [[1]] and list the numbers from one to three [[2]].
```

**Example Gap 1**
```
[[red]] /I/
%50 [[green]] /I/
%20 [[blue]] //
points=5 
size=5
feedback=The correct answer is "red", "green" (50%) and "blue" (20%)
comment=text
```
This is a gap with 5 points.
- The correct answer is `red`, `Red`, `RED` and so on.
- The answer `green`, `Green`, `GREEN` gives 2.5 points.
- The answer `blue` gives 1 point.
- The answer `BLUE` is not correct, because the ignore case option is not enabled.

**Example Gap 2**
```
[[one]] [[two]] [[three]] /OI/
%50 [[red]] [[green]] [[blue]] /OI/
separator=,
points=5 
size=20
feedback=The correct answer is "red", "green" (50%) and "blue" (20%)
comment=text
```
This is a gap with 5 points.
- The correct answer is for example `red,green,blue` or `RED,GREEN,BLUE`.
- The answer `one,two,three` gives 2.5 points.

## Options (`<options>`)
Each option is enabled or disabled by a single letter. A capital letter enables
an option and a lower case letter disables an option. Default options are
enabled by default and can be disabled by specifying the lower case letter.
The following options exist:

| Letter | Name                 | Default |
|:------:|----------------------|:-------:|
|   I    | Ignore Case          |         |
|   D    | Dot All              |         |
|   P    | Pipes and Semicolons |         |
|   R    | Redirects            |         |
|   O    | Match Any Order      |         |
|   s    | Infinite Space       |    x    |
|   t    | Trim Spaces          |    x    |

## Regex (`<regex>`, `<regex-x>`)
Regular expression in the PHP-PCRE syntax.
It is important, that all `[` and `]` must be escaped if used as literal, even
if PCRE would not force it to be escaped. For example the
regular expression `[[]` would be valid in PCRE , but in Regexmatch it must
be `[\[]`.
<br><br>
Examples can be found in [usage-examples.md](usage-examples.md).