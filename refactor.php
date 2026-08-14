<?php
$dir = __DIR__ . '/app/Http/Controllers';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        
        $originalContent = $content;

        // Replace use statements
        $content = str_replace('use App\Models\Customer;', 'use App\Models\Account;', $content);
        $content = str_replace('use App\Models\Vendor;', '', $content); // Account is already imported

        // Replace Customer::all() -> Account::customers()->get()
        $content = str_replace('Customer::all()', 'Account::customers()->get()', $content);
        // Replace Vendor::all() -> Account::vendors()->get()
        $content = str_replace('Vendor::all()', 'Account::vendors()->get()', $content);

        // Replace Customer::findOrFail -> Account::customers()->findOrFail
        $content = str_replace('Customer::findOrFail', 'Account::customers()->findOrFail', $content);
        $content = str_replace('Vendor::findOrFail', 'Account::vendors()->findOrFail', $content);

        // Replace Customer::find -> Account::customers()->find
        $content = str_replace('Customer::find', 'Account::customers()->find', $content);
        $content = str_replace('Vendor::find', 'Account::vendors()->find', $content);

        // Replace Customer::orderBy -> Account::customers()->orderBy
        $content = str_replace('Customer::orderBy', 'Account::customers()->orderBy', $content);
        $content = str_replace('Vendor::orderBy', 'Account::vendors()->orderBy', $content);

        // Replace Customer::with -> Account::customers()->with
        $content = str_replace('Customer::with', 'Account::customers()->with', $content);
        $content = str_replace('Vendor::with', 'Account::vendors()->with', $content);

        // Replace Customer::create -> Account::create (wait, type needs to be set, but where is create used? GroupAccountController doesn't create customers)

        if ($content !== $originalContent) {
            file_put_contents($file->getRealPath(), $content);
            echo "Updated " . $file->getFilename() . "\n";
        }
    }
}
