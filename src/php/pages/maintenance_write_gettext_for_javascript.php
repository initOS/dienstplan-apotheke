<?php

/*
 * Copyright (C) 2018 Dr. rer. nat. M. Mandelkow <netbeans-pdr@martin-mandelkow.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once '../../../default.php';
$Javascript_files = glob(PDR_FILE_SYSTEM_APPLICATION_PATH . 'src/js/*.js');
$Strings_to_translate = array();
$match = '';
foreach ($Javascript_files as $javascript_file) {
    $fh = fopen($javascript_file, 'r');
    while (FALSE !== ($line = fgets($fh))) {
        preg_match('/gettext\s*\((.*)\)\s*;/u', $line, $match);
        if (!empty($match)) {
            $Strings_to_translate[] = trim(trim(trim($match[1]), '"'), "'");
        }
    }
    fclose($fh);
}
$Localization_folders = glob(PDR_FILE_SYSTEM_APPLICATION_PATH . 'locale/*');
foreach ($Localization_folders as $localization_folder) {
    $localization = basename($localization_folder);
    initialize_gettext($localization);
    foreach ($Strings_to_translate as $string_to_translate) {
        $Translations[$localization][$string_to_translate] = pdr_gettext($string_to_translate);
    }
}

$json_string = 'var pdr_translations = ' . json_encode($Translations);
$result = file_put_contents(PDR_FILE_SYSTEM_APPLICATION_PATH . 'src/js/translations.json', $json_string);
