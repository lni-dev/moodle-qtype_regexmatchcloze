# Regexmatch and Regexmatch Cloze usage examples

This file contains some example regular expressions which can be used within Regexmatch

## Some basic examples
Here are a few examples for simple regular expressions with only the default options enabled.

| regular expression  | matches (not a complete list)           | description                                                      |
|---------------------|-----------------------------------------|------------------------------------------------------------------|
| `[[test]]//`        | `test`                                  | text                                                             |
| `[[abc\|def]]//`    | `abc`, `def`                            | The or-operator (`\|`) can match either the left or the right    |
| `[[a*]]//`          | empty answer, `a`, `aa`, `aaaaaa`       | The `*` matches zero or more times                               |
| `[[a+]]//`          | `a`, `aa`, `aaaaaa`                     | The `+` matches one or more times                                |
| `[[(abc\|def)*]]//` | empty answer, `abc`, `def`, `abcabcdef` | The brackets `()` form a group                                   |
| `[[[abcdef]]]//`    | `a`, `b`, `e`                           | The square brackets `[]` match any of the characters inside them |
| `[[[^abc]]]//`      | `d`, `$`, `e`, `f`                      | `[^]` matches any characters except the ones inside the brackets |
| `[[\*]]`            | `*`                                     | `\` is the escape character for .^$*+-?()[]{}\\\|                |
| `[[a{3, 6}]]//`     | `aaa`, `aaaa`, `aaaaa`, `aaaaaa`        | `{n,m}` matches Between n and m times                            |


## Regexmatch Syntax
Regular expressions in Regexmatch consist of the regex, options and keys: 
```
[[regex]]/OPTIONS/
separator=,
comment=text
```
The `regex` uses the default syntax of regular expressions in PHP (without the requirement of a delimiter or the ability
to specify modifiers). The internet provides vast amounts of information on how to write regular expression (not specific to Regexmatch):
- A very good (but technical) explanation for different regex syntax can be found [here](https://stackoverflow.com/questions/22937618/reference-what-does-this-regex-mean/22944075#22944075).
- Test regexes directly in the browser [here](https://regex101.com/) (Select `PCRE2` flavor).

The `OPTIONS` described in [Options](#options).<br>
The following is a valid regular expressions with no options changed that match `abc`:
```
[[abc]]//
```
The following is a valid regular expressions that matches `abc` and has some options set:
```
[[abc]]/I/
```
Additionally, all new lines and spaces before the options will be ignored. This means the following regular expressions are the same
as the previous one:
```
[[abc]]
/I/
```
```
[[abc]]     /I/
```
```
[[abc]]

/I/
```
## Regexmatch Cloze Syntax
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
TODO

### Examples
TODO

## Keys
Regexmatch supports the keys `comment=` and `separator=`. 
- `comment=` is a text field only visible inside the question edit form and has no other use.
- `separator=` is a field for the separator the student has to enter between his answers if the Match Any Order option (`O`).
  is enabled.

Regexmatch Cloze supports the keys `separator=`, `points=`, `size=`, `feedback=` and `comment=`.
- `separator=` is a field for the separator the student has to enter between his answers if the Match Any Order option (`O`).
- `points=` defines the maximum points for this gap.
- `size=` defines the size of the input field for this gap.
- `feedack` defines the feedback for this gap.
- `comment=` is a text field only visible inside the question edit form and has no other use.


## Options
All Options can be activated by a single capital letter and disabled by its small letter counterpart.
Some options are enabled by default. These are called default options.

| Letter | Name                 | Default |
|:------:|----------------------|:-------:|
|   I    | Ignore Case          |         |
|   D    | Dot All              |         |
|   P    | Pipes and Semicolons |         |
|   R    | Redirects            |         |
|   O    | Match Any Order      |         |
|   s    | Infinite Space       |    x    |
|   t    | Trim Spaces          |    x    |


### I: Ignore Case
This option makes the regular expression ignore case.
For example the regular expression `abc/I/` will match `abc`, `Abc`, `ABC`, `aBc`, etc. This option acts exactly as
the PCRE option `IGNORE_CASE`.

### D: Dot All
All Dots (`.`) in the regular expression will also match new lines.
This option acts exactly as the PCRE option `DOT_ALL`.

### P: Pipes and Semicolons
This is a shell specific option. All semicolons `;` and escaped pipes `\|` will be replaced with `([ \t]*[;\n][ \t]*)`
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
This is a shell specific option. All redirections (`<`,`>`,`<<`,`>>`) will be replaced for example with `([ \t]*<[ \t]*)`.
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
This option is experimental and subject to change. If this option is enabled the regex must consist of multiple 
regexes. The answers (separated by the `separator=`) must match any of the regexes,
but order is not important. Each regex can only be matched by a single answer. Wrong, too many or too few answers
results in a point deduction evaluation.

#### Evaluation
The evaluation of the answer is calculated based on an internal point system. The maximum amount of points (`maxPoints`) 
is the same as the amount of regexes. The answer will be rated with points using the following rules:
- The `rating` starts with the value of `maxPoints`.
- If too little answers are given, a point is deducted for each missing answer .
- If too many answers  are given, a point is deducted for each answer  which is too much.
- If an answer  is wrong (that means it does not match any regex), a point is deducted.
- A regex  can only be matched once and an answer can only match a single regex
- An answer that is too much is not counted as a wrong answer.

The points are then converted to a percent based evaluation using `rating/maxPoints` (`1.0 == 100%`). 
This fraction is then used to calculate the actual points for the given answer (`actualPoints*(rating/maxPoints)`).

##### Examples:
The regular expression
```
[[cat]] [[dog]] [[alpaca]] /O/
separator=,
```
or (new lines are allowed)
```
[[cat]]
[[dog]]
[[alpaca]]
/O/
separator=,
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

### I: Infinite Space
This option is enabled by default and can be disabled by specifying the letter `I` in the options.
If this option is enabled all spaces will be replaced with `([ \t]+)`. Thereby they match one or more whitespace characters.

#### Examples
The regular expression `some test sentence` will match any of the following example answers:
- `some test sentence`
- `some     test     sentence`

But it will not match:
- `sometestsentence`
- `some testsentence`

### T: Trim Spaces
This option is enabled by default and can be disabled by specifying the letter `T` in the options.
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

## Common Mistakes and special cases
- If the regular expression contains spaces at the end of a line these must also be contained in the answer.
- If you encounter a problem with a complex regular expression, try to disable the Infinite Space (`i`) option.
