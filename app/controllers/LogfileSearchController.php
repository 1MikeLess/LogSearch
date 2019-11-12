<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Logfile;

class LogfileSearchController extends Controller
{
    // private $logfilesDir = "../../../userdata/logs/";
    private $logfilesDir;
    private $logfile;

    public function __construct() {
        $this->logfilesDir = $_SERVER['DOCUMENT_ROOT'].'/logs//';
        $this->logfile = new Logfile;
    }

    public function getLogContentAction() {
        echo json_encode(
            $this->logfile->getLogContent(
                json_decode($_POST["logfiles"]),
                $_POST["query_string"] ?? "",
                $_POST["allow_marks"] && $_POST["allow_marks"] == 'true' ? true : false
        ));
    }

    public function getLogfilesListAction() {
        echo json_encode($this->logfile->getLogfilesList());
    }
}
