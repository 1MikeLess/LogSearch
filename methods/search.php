<?php

// ДИРЕКТРОИЯ ЛОГИРОВАНИЯ
function _getLogDirectory() {
  return json_decode(file_get_contents("../logdir.json"), true)["directory"];
}

$_logfiles_dir = _getLogDirectory();

/*
  ПОЛУЧЕНИЕ СПИСКА ЛОГ-ФАЙЛОВ
*/
function _getLogfilesList($logfiles_dir) {
  $logfiles = array();
  if($handle = opendir($logfiles_dir)){
    while(false !== ($file = readdir($handle))) {
      if($file != "." && $file != ".." && strpos($file, '.log')) {
        array_push($logfiles, $file);
      }
    }
    return $logfiles;
  }
}

/*
  ИЗВЛЕЧЕНИЕ СТРОК ИЗ ЛОГ-ФАЙЛА
*/
function _getLogContentFromFile($logfiles_dir, $filename) {
  $file_handle = fopen($logfiles_dir.$filename, "r");
  $line_index = 0;
  $lines = array();

  while (!feof($file_handle)) {
    if (!empty($line = fgets($file_handle))) {
      array_push($lines, $line);
    }
  }

  fclose($file_handle);
  return $lines;
}

function _is_full_match($line, $query_str_part) {
  foreach (explode("&&", $query_str_part) as $query_str_words) {
    if($query_str_words && stripos($line, trim($query_str_words)) == false)
      return false;
    else continue;
  }
  return true;
}

/*
  ПРОВЕРКА СТРОКИ НА СООТВЕТСТВИЕ ЗАПРОСУ ПРОИСКА
*/
function _line_query_check($line, $query_string) {
  $query_string = trim($query_string);

  // Разделение по ||
  if (stripos($query_string, "||") !== false) {
    foreach (explode("||", $query_string) as $query_str_part) {

      if (strpos(trim($query_str_part), "&&") == false) {
        if($query_str_part && stripos($line, trim($query_str_part)) !== false)
          return true;
        else continue;
      }
      else {
        if(_is_full_match($line, $query_str_part))
          return true;
        else continue;
      }
    }
    return false;
  }

  // ЕСЛИ ЕСТЬ ИСКЛЮЧИТЕЛЬНО &&
  else if (stripos($query_string, "&&") !== false) {
    foreach (explode("&&", $query_string) as $query_str_words) {
      if($query_str_words && stripos($line, trim($query_str_words)) == false)
        return false;
    }
    return true;
  }

  // ЕСЛИ НЕТ && и ||
  else {
    return stripos($line, $query_string) !== false;
  }
}

/*
  ИЗВЛЕЧЕНИЕ СОДЕРЖИМОГО ЛОГ-ФАЙЛОВ С ПАРАМЕТРАМИ ПОИСКА
*/
function _getLogContent($logfiles_dir, $filenames = [], $query_string) {
  $logfile_content_list = [];
  //  ЕСЛИ НЕ ВЫБРАН НИ ОДИН ЛОГ - ВЫБОРКА ВСЕХ ЛОГОВ
  $checked_logfiles = $filenames ? $filenames : _getLogfilesList($logfiles_dir);
  $query_string = trim($query_string);

  foreach ($checked_logfiles as $filename) {
    // ФИЛЬТРАЦИЯ СТРОК ПО ЗАПРОСУ
    $lines = !strlen($query_string)
      ? _getLogContentFromFile($logfiles_dir, $filename)
      : array_filter(_getLogContentFromFile($logfiles_dir, $filename), function($line) use ($query_string) {
        return _line_query_check($line, $query_string);
      });

    $file = [
      'filename' => $filename,
      'lines' => $lines
    ];
    array_push($logfile_content_list, $file);
  }
  return $logfile_content_list;
}

/*
  -------------------------------------------------
          ОБРАБОТКА ВХОДЯЩИХ POST-ЗАПРОСОВ
*/
$action = $_POST["action"];

if (isset($action)) {
  switch($action) {
    case 'getLogfilesList':
      echo json_encode(_getLogfilesList($_logfiles_dir));
      break;
    case 'getLogContent':
      echo json_encode(_getLogContent($_logfiles_dir, json_decode($_POST["logfiles"]), $_POST["query_string"]));
      break;
    default:
      echo "</p>Empty request</p>";
      break;
  }
}
