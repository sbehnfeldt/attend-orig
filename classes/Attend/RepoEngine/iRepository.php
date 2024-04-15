<?php

namespace Attend\RepoEngine;


interface iRepository
{
    static public function getTableName();

    static public function getColumnNames();
}
