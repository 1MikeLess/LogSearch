<?php
// ДИРЕКТРОИЯ ЛОГИРОВАНИЯ
function getLogDirectory()
{
  $logdir_file_content = file_get_contents("../logdir.json");
  return json_decode($logdir_file_content, true)["directory"];
}

$_logfiles_dir = getLogDirectory();

/*
  ПОЛУЧЕНИЕ СПИСКА ЛОГ-ФАЙЛОВ
*/
function getLogfilesList($logfiles_dir)
{
  $logfiles = [];
  if ($handle = opendir($logfiles_dir)) {
    while (($file = readdir($handle)) !== false) {
      $is_valid_filename = $file != "." && $file != ".." && strpos($file, '.log');
      if ($is_valid_filename) {
        array_push($logfiles, $file);
      }
      unset($is_valid_filename);
    }
    return $logfiles;
  }
}

/*
  ИЗВЛЕЧЕНИЕ СТРОК ИЗ ЛОГ-ФАЙЛА
*/
function getLogContentFromFile($logfiles_dir, $filename)
{
  $file_handle = fopen($logfiles_dir.$filename, "r");
  $line_index = 0;
  $lines = [];
  while (!feof($file_handle)) {
    if (!empty($line = fgets($file_handle))) {
      array_push($lines, $line);
    }
  }
  fclose($file_handle);
  unset($file_handle, $line_index, $logfiles_dir, $filename);
  return $lines;
}

function isFullMatch($line, $query_str_part)
{
  foreach (explode("&&", $query_str_part) as $query_str_words) {
    $is_line_consist = $query_str_words && stripos($line, trim($query_str_words)) == false;
    if ($is_line_consist) {
      unset($line, $query_str_part, $query_str_words, $is_line_consist);
      return false;
    }
    else continue;
    unset($is_line_consist);
  }
  unset($line, $query_str_part);
  return true;
}

/*
  ПРОВЕРКА СТРОКИ НА СООТВЕТСТВИЕ ЗАПРОСУ ПРОИСКА
*/
function lineQueryCheck($line, $query_string)
{
  $query_string = trim($query_string);
  // Разделение по ||
  if (stripos($query_string, "||") !== false) {
    foreach (explode("||", $query_string) as $query_str_part) {
      if (stripos(trim($query_str_part), "&&") == false) {
        if ($query_str_part && stripos($line, trim($query_str_part)) !== false) {
          unset($line, $query_string, $query_str_part);
          return true;
        }
        else continue;
      } else {
        if (isFullMatch($line, $query_str_part)) {
          unset($line, $query_string, $query_str_part);
          return true;
        }
        else continue;
      }
    }
    return false;
  } elseif (stripos($query_string, "&&") !== false) {
    // ЕСЛИ ЕСТЬ ИСКЛЮЧИТЕЛЬНО &&
    foreach (explode("&&", $query_string) as $query_str_words) {
      if ($query_str_words && stripos($line, trim($query_str_words)) == false) {
        unset($line, $query_string, $query_str_words);
        return false;
      }
    }
    unset($line, $query_string);
    return true;
  } else {
    // ЕСЛИ НЕТ && и ||
    return stripos($line, $query_string) !== false;
  }
}

/*
  ИЗВЛЕЧЕНИЕ СОДЕРЖИМОГО ЛОГ-ФАЙЛОВ С ПАРАМЕТРАМИ ПОИСКА
 */
function getLogContent($logfiles_dir, $filenames = [], $query_string)
{
  $logfile_content_list = [];
  //  ЕСЛИ НЕ ВЫБРАН НИ ОДИН ЛОГ - ВЫБОРКА ВСЕХ ЛОГОВ
  $checked_logfiles = $filenames ? $filenames : getLogfilesList($logfiles_dir);
  $query_string = trim($query_string);

  foreach ($checked_logfiles as $filename) {
    // ФИЛЬТРАЦИЯ СТРОК ПО ЗАПРОСУ
    $lines = !strlen($query_string)
      ? getLogContentFromFile($logfiles_dir, $filename)
      : array_filter(
        getLogContentFromFile($logfiles_dir, $filename),
          function ($line) use ($query_string) {
            return lineQueryCheck($line, $query_string);
          }
        );
    $file = [
      'filename' => $filename,
      'lines' => $lines
    ];
    array_push($logfile_content_list, $file);
  }
  unset($checked_logfiles, $query_string, $filenames);
  return $logfile_content_list;
}

/*
  ОБРАБОТКА ВХОДЯЩИХ POST-ЗАПРОСОВ
*/
$action = $_POST["action"];

if (isset($action)) {
  switch ($action) {
    case 'getLogfilesList':
      echo json_encode(getLogfilesList($_logfiles_dir));
      break;
    case 'getLogContent':
      echo json_encode(getLogContent(
        $_logfiles_dir,
        json_decode($_POST["logfiles"]),
        $_POST["query_string"])
      );
      break;
    default:
      echo "<p>Empty request</p>";
      break;
  }
}
