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
    public function getLogContent($filenames, $queryString, $allowMarks = false) {
        $logfileContentList = [];
        //  ЕСЛИ НЕ ВЫБРАН НИ ОДИН ЛОГ - ВЫБОРКА ВСЕХ ЛОГОВ
        $checkedLogfiles = $filenames ? $filenames : $this->getLogfilesList();
        $queryString = trim($queryString);

        foreach ($checkedLogfiles as $filename) {
            // ФИЛЬТРАЦИЯ СТРОК ПО ЗАПРОСУ
            $lines = !strlen($queryString)
                ? $this->getLogContentFromFile($this->logfilesDir, $filename)
                : array_filter(
                    $this->getLogContentFromFile($this->logfilesDir, $filename),
                        function ($line) use ($queryString) {
                            return $this->lineQueryCheck($line, $queryString);
                        }
                    );
            if ($allowMarks)
                $lines = $this->markMatches($lines, $queryString);
            $file = [
                'filename' => $filename,
                'lines' => $lines
            ];
            array_push($logfileContentList, $file);
        }
        unset($checkedLogfiles, $queryString, $filenames);
        return $logfileContentList;
    }

    /*
      ПОЛУЧЕНИЕ СПИСКА ЛОГ-ФАЙЛОВ
    */
    public function getLogfilesList() {
        $logfiles = [];
        if ($handle = opendir($this->logfilesDir)) {
            while (($file = readdir($handle)) !== false) {
                $isValidFilename = $file != "." && $file != ".." && strpos($file, '.log');
                if ($isValidFilename) {
                    array_push($logfiles, $file);
                }
                unset($isValidFilename);
            }
            return $logfiles;
        }
    }

    /*
      ИЗВЛЕЧЕНИЕ СТРОК ИЗ ЛОГ-ФАЙЛА
    */
    function getLogContentFromFile($logfilesDir, $filename) {
        $fileHandle = fopen($logfilesDir.$filename, "r");
        $lineIndex = 0;
        $lines = [];
        while (!feof($fileHandle)) {
            if (!empty($line = fgets($fileHandle))) {
                array_push($lines, $line);
            }
        }
        fclose($fileHandle);
        unset($fileHandle, $lineIndex, $logfilesDir, $filename);
        return $lines;
    }

    function isFullMatch($line, $queryStrPart) {
        foreach (explode("&&", $queryStrPart) as $queryStrWords) {
            $isLineConsist = $queryStrWords && stripos($line, trim($queryStrWords)) == false;
            if ($isLineConsist) {
                unset($line, $queryStrPart, $queryStrWords, $isLineConsist);
                return false;
            }
            else continue;
            unset($isLineConsist);
        }
        unset($line, $queryStrPart);
        return true;
    }

    /*
      ПРОВЕРКА СТРОКИ НА СООТВЕТСТВИЕ ЗАПРОСУ ПРОИСКА
    */
    function lineQueryCheck($line, $queryString) {
        $queryString = trim($queryString);
        // Разделение по ||
        if (stripos($queryString, "||") !== false) {
            foreach (explode("||", $queryString) as $queryStrPart) {
                if (stripos(trim($queryStrPart), "&&") == false) {
                    if ($queryStrPart && stripos($line, trim($queryStrPart)) !== false) {
                        unset($line, $queryString, $queryStrPart);
                        return true;
                    }
                    else continue;
                } else {
                    if ($this->isFullMatch($line, $queryStrPart)) {
                        unset($line, $queryString, $queryStrPart);
                        return true;
                    }
                    else continue;
                }
            }
            return false;
        } elseif (stripos($queryString, "&&") !== false) {
            // ЕСЛИ ЕСТЬ ИСКЛЮЧИТЕЛЬНО &&
            foreach (explode("&&", $queryString) as $queryStrWords) {
                if ($queryStrWords && stripos($line, trim($queryStrWords)) == false) {
                    unset($line, $queryString, $queryStrWords);
                    return false;
                }
            }
            unset($line, $queryString);
            return true;
        } else {
            // ЕСЛИ НЕТ && и ||
            return stripos($line, $queryString) !== false;
        }
    }

    private function markMatches($lines, $queryString) {
        $markedLines = [];
        $keywords = [];
        $keywordsByOr = explode("||", $queryString);
        foreach ($keywordsByOr as $keywordByOr) {
            $keywordsByAnd = explode("&&", trim($keywordByOr));
            foreach ($keywordsByAnd as $keywordByAnd) {
                array_push($keywords, trim($keywordByAnd));
            }
        }
        $markedKeywords = array_map(function($keyword) {
            return '<b>'.$keyword.'</b>';
        }, $keywords);
        return array_map(function($line) use ($keywords, $markedKeywords) {
            return str_replace($keywords, $markedKeywords, $line);
        }, $lines);
    }
}
