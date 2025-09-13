# Regexmatch Cloze Usage Examples

This file contains some example regular expressions which can be used within Regexmatch Cloze

## Some Basic Examples
Here are a few examples for simple regular expressions with only the default options enabled. For the sake of readability
the keys `points=` and `size=`, which are optional but mostly used, are omitted.

| regular expression  | matches (not a complete list)           | description                                                                             |
|---------------------|-----------------------------------------|-----------------------------------------------------------------------------------------|
| `[[test]]//`        | `test`                                  | text                                                                                    |
| `[[abc\|def]]//`    | `abc`, `def`                            | The or-operator (`\|`) can match either the left or the right expression                |
| `[[a*]]//`          | empty answer, `a`, `aa`, `aaaaaa`       | The `*` matches zero or more times                                                      |
| `[[a+]]//`          | `a`, `aa`, `aaaaaa`                     | The `+` matches one or more times                                                       |
| `[[(abc\|def)*]]//` | empty answer, `abc`, `def`, `abcabcdef` | The brackets `()` form a group                                                          |
| `[[[abcdef]]]//`    | `a`, `b`, `e`                           | The square brackets `[]` match any of the characters inside them                        |
| `[[[^abc]]]//`      | `d`, `$`, `e`, `f`                      | `[^]` matches any characters except the ones inside the brackets                        |
| `[[\*]]`            | `*`                                     | `\` is the escape character for .^$*+-?()[]{}\\\| if the characters are meant literally |
| `[[a{3, 6}]]//`     | `aaa`, `aaaa`, `aaaaa`, `aaaaaa`        | `{n,m}` matches Between n and m times                                                   |

## Regexmatch Cloze Question Text Syntax
The question text can contain multiple gaps. The gaps are introduced using double square brackets with the gap number
inside: `[[1]]`. The order of the gaps does not matter. Duplicated gaps are not allowed. Each gap must be defined.

**Example question text**
```
This is an example question text with multiple [[1]]. The amount of gaps in this text is [[2]].
```

## Regexmatch Cloze Gap Syntax
```
[[regex]] /OPTIONS/
%50 [[another regex with half the points]] /OPTIONS/
%10 [[another regex with 10% points]] /OPTIONS/
separator=,
points=5
size=10
feedback=text
comment=text
```
The `regex` uses the default syntax of regular expressions in PHP (without the requirement of a delimiter or the ability
to specify modifiers). The internet provides vast amounts of information on how to write regular expression (not specific to Regexmatch):
- A very good (but technical) explanation for different regex syntax can be found [here](https://stackoverflow.com/questions/22937618/reference-what-does-this-regex-mean/22944075#22944075).
- You can test regexes directly in the browser [here](https://regex101.com/) (Select `PCRE2` flavor).

Expressions like `%50 [[another regex with half the points]] /OPTIONS/` describe an alternative solution, which give a percentage of points. The percentage must be
between `0` and `100`.<br>

The `OPTIONS` are described in [Options](#options) and the keys (e.g.`points=5`) are described in [Keys](#keys).<br>

The following is a valid regular expressions with no options changed that match `abc`:
```
[[abc]]//
points=1
size=4
```
The following is a valid regular expressions that matches `abc` and has some options set:
```
[[abc]]/I/
points=1
size=4
```
Additionally, all new lines and spaces before the options will be ignored. This means the following regular expressions are the same
as the previous one:
```
[[abc]]
/I/
points=1
size=4
```
```
[[abc]]     /I/
points=1
size=4
```
```
[[abc]]

/I/
points=1
size=4
```

### Example
**Question text**
```
The command [[1]] prints the content of the current directory in a readable table.
Additionally, the output can be redirected using a [[2]].
```
**Definition Gap 1**
```
[[ls -la]]//
%50 [[ls]]//
points=5
size=20
feedback=The correct answer is "ls -la" or "ls" (50%)
comment=
```
**Definition Gap 2**
```
[[pipe]]/I/
%100 [[\|]]//
points=5
size=10
feedback=The correct answer is "pipe" or "|"
comment=
```

**View for the student**
```
The command ____________________ prints the content of the current directory in a readable table.
Additionally, the output can be redirected using a __________.
```

## Keys
Regexmatch Cloze supports the keys `separator=`, `points=`, `size=`, `feedback=` and `comment=`.
- `separator=` is a field for the separator the student has to enter between his answers if the Match Any Order option (`O`).
- `points=` defines the maximum points for this gap. The default value is `1.0`.
- `size=` defines the size of the input field for this gap. The default value is `5`.
- `feedack` defines the feedback for this gap which is communicated to the students.
- `comment=` is a text field only visible inside the question edit form and has no other use.
  This enables the author of the question to save some internal information.

The keys must appear in the order listed above.

## Options
All Options can be activated by a single capital letter and disabled by its small letter counterpart.
Some options are enabled by default. These are called default options.

| Letter | Name                 | Default |
|:------:|----------------------|:-------:|
|   I    | Ignore Case          |   no    |
|   D    | Dot All              |   no    |
|   P    | Pipes and Semicolons |   no    |
|   R    | Redirects            |   no    |
|   O    | Match Any Order      |   no    |
|   s    | Infinite Space       |   YES   |
|   t    | Trim Spaces          |   YES   |

In the following sections the keys `points=` and `size=`, which are optional but mostly used, may be omitted for the sake of readability.

### I: Ignore Case
This option makes the regular expression ignore case.
For example the regular expression `[[abc]]/I/` will match `abc`, `Abc`, `ABC`, `aBc`, etc. This option acts exactly as
the PCRE option `IGNORE_CASE`.

### D: Dot All
All Dots (`.`) in the regular expression will also match new lines.
This option acts exactly as the PCRE option `DOT_ALL`.

### P: Pipes and Semicolons
This is a specific option to ease the definition of questions for Linux shell commands. All semicolons `;` and escaped pipes `\|` will be replaced with `([ \t]*[;\n][ \t]*)`
and `([ \t]*\|[ \t]*)` respectively. Thereby infinite spaces are allowed around these and the semicolon
will also match a new line.

#### Examples:
The regular expression `[[cat test.txt\|tee]] /P/` will match any of the following example answers
- `cat test.txt|tee`
- `cat test.txt | tee`
- `cat test.txt      |     tee`

To note is here, that any spaces in front and after the pipe in the regex must also be contained in the answer:
The regular expression `[[cat test.txt \| tee]] /P/` will match
- `cat test.txt | tee`
- `cat test.txt      |     tee`

but it will not match 
- `cat test.txt|tee`.

The semicolon has the same properties, if this option is enabled. Additionally, the semicolon will also be matched
by a new line´. Thus, the regular expression `[[cat test.txt;tee]] /P/` will match any of the following example answers:

- `cat test.txt;tee`
- `cat test.txt   ;   tee`

and it will also match the following example answer:
```
cat test.txt
tee
```

### R: Redirects
This is a specific option to ease the definition of questions for Linux shell commands. All redirections (`<`,`>`,`<<`,`>>`) will be replaced for example with `([ \t]*<[ \t]*)`.
If enabled redirections cannot be used in other regex-functions (eg.: lookbehind `(?<=...)`). Note: Any spaces in front
and after the redirect inside the regex, must also be contained in the answer.

#### Examples:
The regular expression `[[cat test.txt>2]] /R/` will match any of the following example answers
- `cat test.txt>2`
- `cat test.txt > 2`
- `cat test.txt      >     2`

Similar to the pipe option it is important to remember that any spaces in front and after the redirect in the regex must
also be contained in the answer:
The regular expression `[[cat test.txt > tee]] /R/` will match
- `cat test.txt > tee`
- `cat test.txt      >     tee`

but it will not match
- `cat test.txt>tee`.

### O: Match Any Order
If this option is enabled the regex must consist of multiple sub-regexes.
The answers (separated by the `separator=`) must match any of the sub-regexes,
but order is not important. Each sub-regex can only be matched by a single answer. Wrong answers, too many or too few answers
result in a point substraction in the evaluation.

#### Evaluation
The evaluation of the answer is calculated based on an internal point system. The maximum amount of points (`maxPoints`) 
is the same as the amount of sub-regexes. The answer will be rated with points using the following rules:
- The `rating` starts with the value of `maxPoints`.
- If too few answers are given, a point is subtracted for each missing answer .
- If too many answers are given, a point is subtracted for each answer  which is too much.
- If an answer is wrong (that means it does not match any sub-regex), a point is subtracted.
- A sub-regex can only be matched once and an answer can only match a single sub-regex.
- An answer that is too much is not counted as a wrong answer.

The points are then converted to a percent based evaluation using `rating/maxPoints` (`1.0 == 100%`). 
This fraction is then used to calculate the actual points for the given answer (`actualPoints*(rating/maxPoints)`).

##### Examples:
The regular expression
```
[[cat]] [[dog]] [[alpaca]] /O/
separator=,
points=5
size=10
```
or (new lines are allowed)
```
[[cat]]
[[dog]]
[[alpaca]]
/O/
separator=,
points=5
size=10
```
will match any of the following example answers with 100% correctness.

```
cat,dog,alpaca
```
```
alpaca,cat,dog
```

If one line is not correct, too much or missing it will result in a point reduction. In this case the following answers will result
in 66% correctness:
```
alpaca,cat
```
```
alpaca,cat,elephant
```
```
alpaca,cat,dog,elephant
```

### S: Infinite Space
This option is enabled by default and can be disabled by specifying the small letter `s` in the options.
If this option is enabled all spaces will be replaced with `([ \t]+)`. Thereby they match one or more whitespace characters.

#### Examples
The regular expression `[[some test sentence]]//` will match any of the following example answers:
- `some test sentence`
- `some     test     sentence`

But it will not match:
- `sometestsentence`
- `some testsentence`

### T: Trim Spaces
This option is enabled by default and can be disabled by specifying the small letter `t` in the options.
If this option is enabled all trailing and leading empty lines in the answer, as well as all trailing and leading
spaces of every line in the answer, will be ignored. Trailing empty lines will always be
ignored, even if this option is disabled.

#### Examples
The regular expression `[[test]]//` or `[[test]]/T/` will match any of the following example answers:
- `test`
- `    test      `

and also the following example answer
```


  test  


```

## Common Mistakes and Special Cases
- If the regular expression contains spaces at the end of a line these must also be contained in the answer.
- Using Option P (Pipess and Semincolons) and accidentally inserting a blank before and/or after the pipe-character or the semicolon.
- Using Option R (Redirects) and accidentally inserting a blank before and/or after the redirect-characters.
- Specifying multiple answers with one gap and defining the percentage points for an answer: the correct syntax is e.g. '%50' and not '50%' or '%50%'.
- If you encounter a problem with a complex regular expression, try to disable the Infinite Space (`s`) option.
