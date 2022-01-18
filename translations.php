<?php
if (empty($language)) {
  $language = 'en';
}
$translations = [
  "fr" => [
    "Date of Birth" => "Date de naissance",
    "Education" => "Formations",
    "Miscellaneous" => "Divers",
    "Skills" => "Compétences",
    "Professional Background" => "Expériences",
    "Interests" => "Intérêts"
  ]
];
function __(string $key) {
  global $language;
  if (!empty($translations[$language]) && !empty($translations[$language][$key])) {
    return $translations[$language][$key];
  } else {
    return $key;
  }
  
}