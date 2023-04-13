<?php

// The array of languages will be selected by the user, and elements will appear depending on the user type:
$langs = [
	'ar' => 'العربية',
	// '!ar' => 'كل النسخ عدا العربية',
	'id' => 'الإندونيسية',
	'en' => 'الإنجليزية',
	'fr' => 'الفرنسية',
	'ur' => 'الأوردية'
];
$complete_langs = $langs;
$tokens = [
	'ar' => '26abc81f3a71f2c17ceec76c5d45b465',
	// '!ar' => '',
	'id' => '555ef088682e26135019f52c2c5ff148',
	'en' => '5d67dc5eec6b25617c0e55c00c8a9fd6',
	'fr' => '555ef088682e26135019f52c2c5ff148',
	'ur' => '00889dad04ab6ff568b5946bb81ec0ef'
];

if (isset($_SESSION['login_languages']) && !empty($_SESSION['login_languages'])) {
	// If the sponsor is a partial sponsor, unset irrelated languages:
	$sponsor_langs = explode(',', $_SESSION['login_languages']);

	// Create a new langs array from sponsor_langs:
	$new_langs = [];
	foreach ($sponsor_langs as $lang) {
		$new_langs[$lang] = $langs[$lang];
	}

	$langs = $new_langs;
}
