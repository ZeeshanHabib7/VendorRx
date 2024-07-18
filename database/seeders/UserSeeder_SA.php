<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder_SA extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productCreate = Permission::create(['name' => 'product.create']);
        $productUpdate = Permission::create(['name' => 'product.update']);
        $productDelete = Permission::create(['name' => 'product.delete']);
        $productView = Permission::create(['name' => 'product.view']);


        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        $adminRole->givePermissionTo(Permission::all());
        $userRole->givePermissionTo([
            $productView,
        ]);

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin1234',
        ]);
        $admin->assignRole($adminRole);
        $admin->givePermissionTo(Permission::all());

        $faker = Faker::create();

        for ($i = 1; $i < 10; $i++) {

            $user = new User;
            $user->name = $faker->name;
            $user->email = $faker->unique()->safeEmail;
            $user->email_verified_at = now();
            $user->password = bcrypt($faker->password);
            $user->created_at = $faker->date();
            $user->assignRole($userRole);
            $user->givePermissionTo([$productView]);
            $user->save();
        }



    }
}
