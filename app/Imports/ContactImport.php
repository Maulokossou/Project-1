<?php
namespace App\Imports;

use App\Models\Contact;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContactImportService
{public function import($file)
    {
    $importedContacts = Excel::toCollection(new ContactsImport, $file);
    $successContacts = [];
    $errorContacts = [];foreach
    ($importedContacts[0] as $row) {$validator = Validator::make($row, ['first_name' =>
        'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:contacts,email',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'address' => 'nullable|string',
    ]);

        if ($validator->fails()) {
            $errorContacts[] = [
                'data' => $row,
                'errors' => $validator->errors()->toArray(),
            ];
            continue;
        }

        $contact = Contact::create([
             ...$validator->validated(),
            'source' => 'import',
        ]);

        $successContacts[] = $contact;}

    return [
        'success' => $successContacts,
        'errors' => $errorContacts,
    ];
}}