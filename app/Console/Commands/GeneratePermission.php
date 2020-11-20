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
        Helper::log('Generate for user permission starts');
        $users = User::all();
        
        /*User POS Permission*/
        $getPosPermission = PosPermission::all();
        
        Helper::log($getPosPermission);

        $getPosPermission = PosRolePermission::join('pos_permission', 'pos_permission.pos_permission_id', 'pos_role_permission.pos_rp_permission_id')
            ->where('pos_role_permission.pos_rp_role_id', $role_id)
            ->where('pos_role_permission.pos_rp_permission_status',1)
            ->select('pos_permission.pos_permission_id')
            ->get();
        
        if (!empty($getPosPermission)) {
            foreach ($getPosPermission as $value) {
                $insertPermission = [
                    'up_pos_uuid' => Helper::getUuid(),
                    'user_id' => $userId,
                    'pos_permission_id' => $value->pos_permission_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                UserPosPermission::create($insertPermission);
            }
        }

        Helper::log('End');

        return 0;
    }
}
