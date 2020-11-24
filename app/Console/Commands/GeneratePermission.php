<?php

namespace App\Console\Commands;
use App\User;
use App\Models\Helper;
use App\Models\PosPermission;
use App\Models\PosRolePermission;
use App\Models\RolePermission;
use App\Models\UserPosPermission;

use Illuminate\Support\Facades\Auth;
use Illuminate\Console\Command;

class GeneratePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily generate permission for user that does not have this permission';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();

        /*User POS Permission*/
        $getPosPermission = PosPermission::all();

        foreach($users as $user) {
            if ($user->id === 1) continue;
            $user_id = $user->id;
            $getUserPosPermission = UserPosPermission::where('user_id', $user_id)->pluck('pos_permission_id')->toArray();

            if(!empty($getUserPosPermission)) {
                foreach ($getPosPermission as $posPermission) {

                    $posPermissionId = $posPermission->pos_permission_id;

                    if(!empty($getUserPosPermission)) {
                        if(!in_array($posPermissionId, $getUserPosPermission)) {
                            $insertPermission = [
                                'up_pos_uuid' => Helper::getUuid(),
                                'user_id' => $user_id,
                                'pos_permission_id' => $posPermission->pos_permission_id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => 1,
                            ];
                            UserPosPermission::create($insertPermission);
                        }
                    }
                }
            } else {
                foreach ($getPosPermission as $value) {
                    $insertPermission = [
                        'up_pos_uuid' => Helper::getUuid(),
                        'user_id' => $user_id,
                        'pos_permission_id' => $value->pos_permission_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => 1,
                    ];
                    UserPosPermission::create($insertPermission);
                }
            }

        }

        Helper::log('Generate for user permission ends');

        return 0;
    }
}
