<?php
/*
Plugin Name: Compare Translations
Version: 0.3
Plugin URI: http://uplift.ru/projects/
Description: Allows to compare translations in different .mo files.
Author: Sergey Biryukov
Author URI: http://sergeybiryukov.ru/
*/

$mofiles = array(
	'WP' => 'S:\home\wordpress\ru_RU\trunk\messages\ru_RU.mo',
	'MU' => 'S:\home\wordpress\l10n-ru\trunk\wordpress-mu\ru_RU.mo',
	'.C' => 'S:\home\wordpress\l10n-ru\trunk\wpcom\ru.mo'
);

$exceptions = array(
	'', 'Archive', 'Enabled', 'Latest', 'Site Admin'
);

function ct_options_page() {
global $l10n, $mofiles, $exceptions;
?>
<div class="wrap">
<h2><?php _e('Translations', 'compare-translations'); ?></h2>
<pre>
<?php
_e('Loaded files:', 'compare-translations');
foreach ( $mofiles as $branch => $mofile ) {
	load_textdomain($branch, $mofile);

	if ( is_array($l10n[$branch]->entries) )
		$strings[] = $l10n[$branch]->entries;
	else
		$strings[] = array();

	echo sprintf("\n$mofile (%d)", count($l10n[$branch]->entries));
}
echo "<hr />";

$differences = 0;
$mofiles_number = count($mofiles);
for ( $i = 0; $i < $mofiles_number - 1; $i++ )
	for ( $j = $i + 1; $j < $mofiles_number; $j++ )
		foreach ( $strings[$i] as $original => $translation_entry )
			if ( array_key_exists($original, $strings[$j]) && !in_array($original, $exceptions) ) {
				$first_translation = implode('|', $translation_entry->translations);
				$second_translation = implode('|', $strings[$j][$original]->translations);

				if ( $first_translation == $second_translation )
					continue;

				if ( isset($translation_entry->plural) )
					$original .= "|{$translation_entry->plural}";

				$branches = array_keys($mofiles);
				echo htmlspecialchars("$original\n");
				echo htmlspecialchars("{$branches[$i]}: $first_translation\n");
				echo htmlspecialchars("{$branches[$j]}: $second_translation\n");
				echo "<hr />";
				$differences++;
			}
echo sprintf(__('Total differences: %d', 'compare-translations'), $differences);
?>
</pre>
</div>
<?
}

function ct_add_menu() {
	load_plugin_textdomain('compare-translations', false, dirname(plugin_basename(__FILE__)));
	add_management_page(__('Translations', 'compare-translations'), __('Translations', 'compare-translations'), 'administrator', __FILE__, 'ct_options_page');
}
add_action('admin_menu', 'ct_add_menu');
?>