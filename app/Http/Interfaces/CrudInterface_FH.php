<?php

namespace App\Http\Interfaces;

interface CrudInterface_FH
{
    //to fetch records
    public function index();

    //to add a record
    public function store(array $payload);

    //to update a record
    public function update(array $payload, $id);
    
    //to delete a record
    public function destroy($id);
}