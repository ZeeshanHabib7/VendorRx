<?php

namespace App\Http\Interfaces;
use Illuminate\Http\Request;

interface CrudInterface_FH
{
    //to fetch records
    public function index();

    //to add a record
    public function store(Request $request);

    //to update a record
    public function update(Request $request, $id);

    //to delete a record
    public function destroy($id);
}