<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface CRUD_Operations_Interface_SA{

    public function store(Request $request);
    public function show($permissionId);
    public function update(Request $request, $permissionId);
    public function destroy($permissionId);

}