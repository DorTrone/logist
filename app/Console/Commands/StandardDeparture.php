<?php

namespace App\Console\Commands;

use App\Models\Action;
use App\Models\Error;
use App\Models\Package;
use App\Models\PushNotification;
use App\Models\Transport;
use Exception;
use Illuminate\Console\Command;

class StandardDeparture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:standard-departure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Standard Departure for app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $transport = Transport::where('type', 0)
                ->where('status', 0)
                ->orderBy('id', 'desc')
                ->firstOrFail();

            $packages = Package::where('transport_id', $transport->id)
                ->where('transport_type', 0)
                ->where('status', 0)
                ->where('type', 0)
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
            $action->updates = ['transportStatus' => 0];
            $action->note = 'Standard package statuses have been updated';
            $action->save();

            $newTransport = Transport::create(['code' => str()->random(5), 'type' => 0]);
            $newTransport->code = 'T' . $newTransport->id;
            $newTransport->ext_keyword = str('T' . $newTransport->id)->squish()->lower()->slug(' ');
            $newTransport->update();

            $action = new Action();
            $action->transport_id = $newTransport->id;
            $action->updated_by = 'System';
            $action->updates = ['transportStatus' => 0];
            $action->note = 'A new transport has been added';
            $action->save();

        } catch (Exception $e) {
            Error::create([
                'title' => 'StandardDeparture handle Exception',
                'body' => $e->getMessage(),
            ]);
        }

        return Command::SUCCESS;
    }
}
