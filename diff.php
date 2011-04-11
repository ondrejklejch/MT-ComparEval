<?php

/*
 * 
 *  Simple diff  using some PEAR packages.
 *  Compares given files and displays differences in the terminal.
 *
 *  Please install PEAR packages:
 *  Text_Diff
 *  Console_Color
 *
 *  The source code is modified example from
 *  http://pear.php.net/manual/en/package.text.text-diff.usage-examples.shell.php
 *
 */

require_once 'Console/Color.php';
require_once 'Text/Diff.php';
require_once 'Text/Diff/Renderer/inline.php';

$lines1 = file('./translations/reference.txt');
$lines2 = file('./translations/1.txt');

$diff = new Text_Diff('auto', array($lines1, $lines2));
$renderer = new Text_Diff_Renderer_inline(
    array(
        'ins_prefix' => '%g',
        'ins_suffix' => '%n',
        'del_prefix' => '%r',
        'del_suffix' => '%n',
    )
);

echo Console_Color::convert(
    htmlspecialchars_decode(
        $renderer->render($diff)
    )
); 

