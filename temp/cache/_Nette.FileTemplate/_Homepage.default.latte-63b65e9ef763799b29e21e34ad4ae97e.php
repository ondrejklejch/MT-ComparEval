<?php //netteCache[01]000400a:2:{s:4:"time";s:21:"0.48086500 1340350686";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:78:"/home/neithan/workspace/php/MT-ComparEval/app/templates/Homepage/default.latte";i:2;i:1337110722;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"eb558ae released on 2012-04-04";}}}?><?php

// source file: /home/neithan/workspace/php/MT-ComparEval/app/templates/Homepage/default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, '24h2s2fg8i')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbb20848d0a4_content')) { function _lbb20848d0a4_content($_l, $_args) { extract($_args)
?><div id="banner">
	<h1>Congratulations!</h1>
</div>

<div id="content">
	<h2>You have successfully created your Nette Framework project.</h2>

	<div class="box">
		<h2><img src="http://files.nette.org/sandbox/sourcecode.png" alt="" />Explore source codes</h2>
		<p>See source code of <a href="#template">this page template</a>, <a href="#layout">layout template</a> and
		<a href="#presenter">corresponding presenter</a>. And feel free to modify them!</p>
	</div>

	<div class="box">
		<h2><a href="<?php echo htmlSpecialChars($basePath) ?>/adminer/"><img src="http://files.nette.org/sandbox/database.png" alt="" />Manage database tables</a></h2>
		<p>Manage your database using pre-installed tool <a href="<?php echo htmlSpecialChars($basePath) ?>/adminer/">Adminer</a> with an easy to use GUI.<p>
	</div>

	<div class="box">
		<h2><a href="http://doc.nette.org/quickstart"><img src="http://files.nette.org/sandbox/book.png" alt="" />Read<br />Quick-start tutorial</a></h2>
		<p>If you are exploring Nette Framework for the first time, you should read the
		<a href="http://doc.nette.org/quickstart">Quick Start</a>, <a href="http://doc.nette.org">documentation</a>,
		<a href="http://pla.nette.org">tutorials</a> and <a href="http://forum.nette.org">forum</a>.</p>
	</div>

	<h2>We hope you enjoy this framework!</h2>

	<section id="template">
		<h2>This page template located at <span><?php echo Nette\Templating\Helpers::escapeHtml(strstr($presenter->template->getFile(), 'app'), ENT_NOQUOTES) ?></span></h2>

		<pre><code class="jush"><?php echo Nette\Templating\Helpers::escapeHtml(file_get_contents($presenter->template->getFile()), ENT_NOQUOTES) ?></code></pre>
	</section>

	<section id="layout">
		<h2>Layout template located at <span><?php echo Nette\Templating\Helpers::escapeHtml(strstr($template->getFile(), 'app'), ENT_NOQUOTES) ?></span></h2>

		<pre><code class="jush"><?php echo Nette\Templating\Helpers::escapeHtml(file_get_contents($template->getFile()), ENT_NOQUOTES) ?></code></pre>
	</section>

	<section id="presenter">
		<h2>Current presenter located at <span><?php echo Nette\Templating\Helpers::escapeHtml(strstr($presenter->reflection->fileName, 'app'), ENT_NOQUOTES) ?></span></h2>

		<pre><code class="jush-php"><?php echo Nette\Templating\Helpers::escapeHtml(file_get_contents($presenter->reflection->fileName), ENT_NOQUOTES) ?></code></pre>
	</section>

	<footer>PHP <?php echo Nette\Templating\Helpers::escapeHtml(PHP_VERSION, ENT_NOQUOTES) ?> |
		<?php if (isset($_SERVER['SERVER_SOFTWARE'])): ?>Server <?php echo Nette\Templating\Helpers::escapeHtml($_SERVER['SERVER_SOFTWARE'], ENT_NOQUOTES) ;endif ?> |
		Nette Framework <?php echo Nette\Templating\Helpers::escapeHtml(Nette\Framework::VERSION, ENT_NOQUOTES) ?></footer>
</div>

<script src="http://jush.sourceforge.net/jush.js"></script>
<script>
	jush.create_links = false;
	jush.highlight_tag('code');
	$('code.jush').each(function(){ $(this).html($(this).html().replace(/\x7B[/$\w].*?\}/g, '<span class="jush-latte">$&</span>')) });

	$('a[href^=#]').click(function(){
		$('html,body').animate({ scrollTop: $($(this).attr('href')).show().offset().top - 5 }, 'fast');
		return false;
	});
</script>
<?php
}}

//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb6c218b9417_head')) { function _lb6c218b9417_head($_l, $_args) { extract($_args)
?><style>
html { overflow-y: scroll; }
body { font: 14px/1.65 Verdana, "Geneva CE", lucida, sans-serif; background: #3484d2; color: #333; margin: 38px auto; max-width: 940px; min-width: 770px; }

h1, h2 { font: normal 150%/1.3 Georgia, "New York CE", utopia, serif; color: #1e5eb6; -webkit-text-stroke: 1px rgba(0,0,0,0); }

img { border: none; }

a { color: #006aeb; padding: 3px 1px; }

a:hover, a:active, a:focus { background-color: #006aeb; text-decoration: none; color: white; }

#banner { border-radius: 12px 12px 0 0; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAB5CAMAAADPursXAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAGBQTFRFD1CRDkqFDTlmDkF1D06NDT1tDTNZDk2KEFWaDTZgDkiCDTtpDT5wDkZ/DTBVEFacEFOWD1KUDTRcDTFWDkV9DkR7DkN4DkByDTVeDC9TDThjDTxrDkeADkuIDTRbDC9SbsUaggAAAEdJREFUeNqkwYURgAAQA7DH3d3335LSKyxAYpf9vWCpnYbf01qcOdFVXc14w4BznNTjkQfsscAdU3b4wIh9fDVYc4zV8xZgAAYaCMI6vPgLAAAAAElFTkSuQmCC); }
#banner h1 { color: white; font-size: 50px; line-height: 121px; margin: 0; padding-left: 40px; background: url(http://files.nette.org/sandbox/logo.png) no-repeat 95%; text-shadow: 1px 1px 0 rgba(0, 0, 0, .9); }

#content { background: white; border: 1px solid #eff4f7; border-radius: 0 0 12px 12px; padding: 10px 40px; }
#content > h2 { font-size: 130%; color: #666; clear: both; padding: 1.2em 0; margin: 0; }

h2 span { color: #87A7D5; }
h2 a { text-decoration: none; background: transparent; }

.box { width: 24%; float: left; background: #f0f0f0; margin-right: 4%; min-height: 230px; padding: 3%; border: 1px solid #e6e6e6; border-radius: 5px; }
.box h2 { text-align: right; margin: 0; }
.box img { float: left; }
.box p { clear: both; }
.box:nth-child(4n - 2) h2 { color: #00a6e5; }
.box:nth-child(4n - 2) img { margin: -24px 0 0 -24px; }
.box:nth-child(4n - 1) h2 a { color: #db8e34; background: transparent; }
.box:nth-child(4n) { margin: 0; }
.box:nth-child(4n) h2 a { color: #578404; background: transparent; }

body.js section { display: none; }

pre { font-size: 12px; line-height: 1.4; padding: 10px; margin: 1.3em 0; overflow: auto; max-height: 500px; background: #F1F5FB; border-radius: 5px; box-shadow: 0 1px 1px rgba(0, 0, 0, .1); }

footer { font-size: 70%; padding: 1em 0; color: gray; }

.jush-com, .jush-php_doc { color: #929292; }
.jush-tag, .jush-tag_js { color: #6A8527; font-weight: bold; }
.jush-att { color: #8CA315 }
.jush-att_quo { color: #448CCB; font-weight: bold; }
.jush-php_var { color: #d59401; font-weight: bold; }
.jush-php_apo { color: green; }
.jush-php_new { font-weight: bold; }
.jush-php_fun { color: #254DB3; }
.jush-js, .jush-css { color: #333333; }
.jush-css_val { color: #448CCB; }
.jush-clr { color: #007800; }
.jush a { color: inherit; background: transparent; }
.jush-latte { color: #D59401; font-weight: bold }
</style>
<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars())  ?>


<?php call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars()) ; 