<?php

namespace App\Console\Commands;

use App\Models\Action;
use App\Models\Error;
use App\Models\Package;
use App\Models\PushNotification;
use App\Models\Transport;
use Exception;
use Illuminate\Console\Command;

class RemainingDeparture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remaining-departure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remaining Departure for app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $transport = Transport::where('type', 0)
                ->where('status', 0)
                ->orderBy('id')
                ->with('packages')
                ->firstOrFail();
            $transport->total_weight = $transport->packages->sum('weight');
            $transport->total_price = $transport->packages->sum('total_price');
            $transport->status = 1;
            $transport->update();

            $packages = Package::where('transport_id', $transport->id)
                ->where('transport_type', 0)
                ->where('status', 0)
                ->whereIn('type', [1, 2, 3, 4, 5, 6])
                ->with('customer')
                ->get();

            $customers = [];

            foreach ($packages as $package) {
                $package->status = 1;
                $package->update();

                $action = new Action();
                $action->package_id = $package->id;
                $action->updated_by = 'System';
                $action->updates = ['packageStatus' => 1];
                $action->save();

                $customers[$package->customer->id]['language'] = $package->customer->languageCode();
                $customers[$package->customer->id]['packages'][] = $package->getName();
            }

            foreach ($customers as $key => $value) {
                $pn = new PushNotification();
                $pn->push = 'app';
                $pn->to = 'shazada_app_' . $key;
                $pn->title = trans('const.' . config('const.packageStatuses')[1]['name'], [], $value['language']);
                $pn->body = str(implode(', ', $value['packages']))->limit(200);
                $pn->datetime = now()->addMinute()->startOfMinute();
                $pn->save();
            }

            $action = new Action();
            $action->transport_id = $transport->id;
            $action->updated_by = 'System';
            $action->updates = ['transportStatus' => 1];
            $action->note = 'Remaining package statuses have been updated';
            $action->save();

        } catch (Exception $e) {
            Error::create([
                'title' => 'RemainingDeparture handle Exception',
                'body' => $e->getMessage(),
            ]);
        }

        return Command::SUCCESS;
    }
}
