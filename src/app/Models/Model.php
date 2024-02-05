<?php
declare(strict_types=1);

namespace App\Models;

use App\App;
use App\Database\Database;

abstract class Model
{
    protected Database $db;

    public function __construct()
    {
        $this->db = App::db();
    }
}