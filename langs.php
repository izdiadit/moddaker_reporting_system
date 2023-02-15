<?php

// The array of languages will be selected by the user, and elements will appear depending on the user type:
$langs = [
	'ar' => 'العربية',
	// '!ar' => 'كل النسخ عدا العربية',
	'id' => 'الإندونيسية',
	'en' => 'الإنجليزية',
	'fr' => 'الفرنسية'
];
$tokens = [
	'ar' => '26abc81f3a71f2c17ceec76c5d45b465',
	// '!ar' => '',
	'id' => 'e550100be5197a3e25596068c83ab9d2',
	'en' => '5d67dc5eec6b25617c0e55c00c8a9fd6',
	'fr' => 'f5a13ccf5b087df6ed67b12afce7dc3a'
];

if (isset($_SESSION['login_langs']) && !empty($_SESSION['login_langs'])) {
	// If the sponsor is a partial sponsor, unset irrelated languages:
	$sponsor_langs = explode(',', $_SESSION['login_langs']);

	// Create a new langs array from sponsor_langs:
	$new_langs = [];
	foreach ($sponsor_langs as $lang) {
		$new_langs[$lang] = $langs[$lang];
	}

	$langs = $new_langs;
}
