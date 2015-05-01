<?php
/**
 * the configuration file
 *
 * @see https://michelf.ca/projects/php-markdown/configuration/
 */

return array(
	'empty_element_suffix' => ' />',
	'tab_width'            => 4,
	'no_markup'            => false,
	'no_entities'          => false,
	'predef_urls'          => array(
		'base_url' => \Phile\Core\Utility::getBaseUrl() // base_url is a good reference to have
	),
	'predef_titles'        => array(),
	'fn_id_prefix'         => "",
	'fn_link_title'        => "",
	'fn_backlink_title'    => "",
	'fn_link_class'        => "footnote-ref",
	'fn_backlink_class'    => "footnote-backref",
	'code_class_prefix'    => "",
	'code_attr_on_pre'     => false,
	'predef_abbr'          => array()
);
