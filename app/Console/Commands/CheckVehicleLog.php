<?php

namespace App\Console\Commands;

use App\Models\VehicleAssign;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\UserInfo;
use App\Models\Template;
use App\Models\EmailLogs;
use App\Mail\MessageReasonMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\VehicleLogHistory;
use App\Notifications\VehicleLogHistoryNotification;

class CheckVehicleLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:vehicle-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for new vehicle logs with PowerCut and is_send = 0';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $logs = VehicleLogHistory::where('message_reason', 'POWER_CUT')
            ->where('is_send', 0)
            ->get();

        if ($logs && count($logs) > 0) {
            
            foreach ($logs as $datas) {
                
                $serialNumber = $datas->identifier;

                if ($serialNumber) {
                    
                    $device = Device::where('serial_number', $serialNumber)->first();

                    if ($device) {
                    
                        $userId = $device->created_by;
                    
                        $vehicleId = $device->vehicle_id;

                        $vehicleAssign = VehicleAssign::where('vechile_id', $vehicleId)->first();

                        $driverId = $vehicleAssign->driver_id;

                        if ($userId) {

                            $user = User::find($userId);

                            $master = User::find($driverId);

                            $vehicle = Vehicle::find($vehicleId);

                            // Configuration values
                            $eld_url = config('app.eld_web'); // Use config helper consistently
        
                            $eld_mail = config('app.eld_mail');

                            $name = $master->first_name . ' ' . $master->last_name;
                            
                            $email = $user->email;
                        
                            $master_email = $master->email;

                            $userInfo = UserInfo::where('user_id', $master->id)->first();
        
                            $timeZone = $userInfo->home_terminal_timezone;
        
                            $timeNow = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();
        
                            $currentTime = Carbon::parse($timeNow);

                            // Create email log for user email
                            $mailUser = EmailLogs::create([
                                'user_id' => $userId,
                                'reciever_email' => $email,
                                'template_id' => 2,
                                'send_time' => $currentTime,
                                'type' => 0,
                                'is_send' => 1,
                            ]);

                            // Create email log for master email
                            $mailMaster = EmailLogs::create([
                                'user_id' => $master->id,
                                'reciever_email' => $master_email,
                                'template_id' => 2,
                                'send_time' => $currentTime,
                                'type' => 0,
                                'is_send' => 1,
                            ]);

                            // Data for the email
                            $data = [
                                'mail' => $mailUser,
                                'mail_master' => $mailMaster, // Use the user-specific email log here
                                'first_name' => $master->first_name,
                                'last_name' => $master->last_name,
                                'vName' => $vehicle->name,
                                'time' => $datas->event_date_time,
                                'url' => url('assets/media/logos/custom-1.png'),
                                'eld_url' => $eld_url,
                                'eld_mail' => $eld_mail,
                                'location' => $datas->location, // Ensure 'location' comes from $vehicle
                                'odometer' => $datas->odometer, // Ensure 'odometer' comes from $vehicle
                            ];

                            // Send email to both user and master
                            Mail::to([$email, $master_email])->send(
                                (new MessageReasonMail($data))->from(env('MAIL_FROM_ADDRESS'), $name)
                            );

                            $message = "Device serial number " . $serialNumber . " is not active, it has a power cut with the vehicle on " . $datas->created_at->format('d/m/Y') . ". Kindly, please have a look.";
                            
                            $url = route('transport.dashboard');
                            
                            $notification = new VehicleLogHistoryNotification($message, $url);
                            
                            $user->notify($notification);

                            // Update the log entry after processing
                            $vlog = VehicleLogHistory::find($datas->id);
                            
                            $vlog->update([
                                'is_send' => 1
                            ]);
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }

        $this->info('Checked vehicle logs and sent notifications if necessary.');
        
    }

}
