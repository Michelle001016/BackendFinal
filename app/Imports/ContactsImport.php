<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;

class ContactsImport implements ToModel
{
    public function model(array $row)
    {
        return new Customer([
        'name'   => $row[1],
        'email'  => $row[2],
        'password'=>\Hash::make($row[4]), 
        ]);
    }
}
