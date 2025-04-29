<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dts_user;
use App\Models\Dtruser;
use App\Models\Technician;
use Illuminate\Support\Facades\Hash;

class TechnicianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $requestor = Dts_user::where('username', 201800276) //ma'am Bethel
                ->orWhere('username', 1756) //sir Theo
                ->get();

        foreach ($requestor as $req) {
            $requestor = Dtruser::where('userid', $req->username)->first();

            if ($requestor) {
                $requestor->password = Hash::make('123');
                $requestor->usertype = 0;
                $requestor->save();
                echo "Password reset for requestor: {$requestor->userid}\n";
            } else {
                echo "No Requestor found for username: {$req->username}\n";
            }
        }

        $users = Dts_user::where('section', 80)->get();

        foreach ($users as $user) {
            $technician = Dtruser::where('userid', $user->username)->first();

            if ($technician) {
                $technician->password = Hash::make('123');
                if($user->username == '1731') {
                    $technician->usertype = 1;
                }
                else {
                    $technician->usertype = 2;
                }
                $technician->save();

                Technician::updateOrCreate(
                    ['userid' => $user->username],
                    ['status' => 'active']
                );

                echo "Password reset for: {$technician->userid}\n";
            } else {
                echo "No technician found for username: {$user->username}\n";
            }
        }
    }
}
