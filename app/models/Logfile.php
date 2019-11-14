<?php

namespace App\Models;

use App\Core\Model;

class Logfile extends Model
{
    private $logfilesDir;

    public function __construct() {
        $this->logfilesDir = $_SERVER['DOCUMENT_ROOT'].'/logs//';
    }

    /*
      ИЗВЛЕЧЕНИЕ СОДЕРЖИМОГО ЛОГ-ФАЙЛОВ С ПАРАМЕТРАМИ ПОИСКА
     */
    public function getLogContent($filenames, $query_string, $allow_marks = false) {
        $logfile_content_list = [];
        //  ЕСЛИ НЕ ВЫБРАН НИ ОДИН ЛОГ - ВЫБОРКА ВСЕХ ЛОГОВ
        $checked_logfiles = $filenames ? $filenames : $this->getLogfilesList();
        $query_string = trim($query_string);

        foreach ($checked_logfiles as $filename) {
            // ФИЛЬТРАЦИЯ СТРОК ПО ЗАПРОСУ
            $lines = !strlen($query_string)
                ? $this->getLogContentFromFile($this->logfilesDir, $filename)
                : array_filter(
                    $this->getLogContentFromFile($this->logfilesDir, $filename),
                        function ($line) use ($query_string) {
                            return $this->lineQueryCheck($line, $query_string);
                        }
                    );
            if ($allow_marks)
                $lines = $this->markMatches($lines, $query_string);
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
      ПОЛУЧЕНИЕ СПИСКА ЛОГ-ФАЙЛОВ
    */
    public function getLogfilesList() {
        $logfiles = [];
        if ($handle = opendir($this->logfilesDir)) {
            while (($file = readdir($handle)) !== false) {
                $is_valid_filename = $file != "." && $file != ".." && strpos($file, '.log');
                if ($is_valid_filename)
                    array_push($logfiles, $file);
                unset($is_valid_filename);
            }
            return $logfiles;
        }
    }

    /*
      ИЗВЛЕЧЕНИЕ СТРОК ИЗ ЛОГ-ФАЙЛА
    */
    function getLogContentFromFile($logfiles_dir, $filename) {
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

    function isFullMatch($line, $query_str_part) {
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
    function lineQueryCheck($line, $query_string) {
        $query_string = rtrim(ltrim(trim($query_string), '('), ')');
        // Разделение по ||
        if (stripos($query_string, "||") !== false) {
            foreach (explode("||", $query_string) as $query_str_part) {
                if (stripos(trim($query_str_part), "&&") == false) {
                    if ($query_str_part && stripos($line, trim($query_str_part)) !== false) {
                        return true;
                    }
                    else continue;
                } else {
                    if ($this->isFullMatch($line, $query_str_part)) {
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

    private function markMatches($lines, $query_string) {
        $query_string = rtrim(ltrim(trim($query_string), '('), ')');
        $keywords = [];
        $keywords_by_or = explode("||", $query_string);
        foreach ($keywords_by_or as $keyword_by_or) {
            $keywords_by_and = explode("&&", trim($keyword_by_or));
            foreach ($keywords_by_and as $keyword_by_and) {
                array_push($keywords, trim($keyword_by_and));
            }
        }
        $marked_keywords = array_map(function($keyword) {
            return '<b>'.$keyword.'</b>';
        }, $keywords);
        return array_map(function($line) use ($keywords, $marked_keywords) {
            return str_replace($keywords, $marked_keywords, $line);
        }, $lines);
        // preg_match('/^([а-яА-ЯЁёa-zA-Z0-9_]+)$/u', $query_string, $keywords);
        // $keywords = array_unique($keywords);
        // $marked_keywords = array_map(function($keyword) {
        //     return '<b>'.$keyword.'</b>';
        // }, $keywords);
        // return array_map(function($line) use ($keywords, $marked_keywords) {
        //     return str_replace($keywords, $marked_keywords, $line);
        // }, $lines);
    }
}
