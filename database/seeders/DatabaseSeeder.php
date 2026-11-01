<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['admin', 'مدير النظام'],
            ['beneficiary', 'مستفيد'],
            ['donor', 'متبرع'],
            ['case_officer', 'باحث اجتماعي'],
            ['auditor', 'مدقق'],
        ] as [$name, $display]) {
            Role::updateOrCreate(['name' => $name], ['display_name' => $display]);
        }
    }
}
