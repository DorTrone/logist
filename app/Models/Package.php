<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Package extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected function casts()
    {
        return [
            'images' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)
            ->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentReports()
    {
        return $this->hasMany(PaymentReport::class)
            ->orderBy('id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class)
            ->orderBy('id');
    }

    public function location()
    {
        return trans('const.' . config('const.locations')[$this->location]['name']);
    }

    public function transportType()
    {
        return trans('const.' . config('const.transportTypes')[$this->transport_type]['name']);
    }

    public function payment()
    {
        return trans('const.' . config('const.packagePayments')[$this->payment]['name']);
    }

    public function paymentExt()
    {
        return trans('const.' . config('const.packagePayments')[$this->payment]['ext']);
    }

    public function type()
    {
        return trans('const.' . config('const.packageTypes')[$this->type]['name']);
    }

    public function status()
    {
        return trans('const.' . config('const.packageStatuses')[$this->status]['name']);
    }

    public function statusColor()
    {
        return config('const.packageStatuses')[$this->status]['color'];
    }

    public function nextStatuses()
    {
        return $this->location
            ? [[0, 2, 3, 4, 5], [1, 2, 3, 4, 5], [2, 3, 4, 5], [3, 4, 5], [4, 5], [5]][$this->status]
            : [[0, 2, 5], [1, 2, 5], [2, 5], [], [], [5]][$this->status];
    }

    public function next4Statuses()
    {
        return $this->location
            ? [[0, 1, 2, 3], [1, 2, 3, 4], [2, 3, 4, 5], [2, 3, 4, 5], [2, 3, 4, 5], [2, 3, 4, 5]][$this->status]
            : [[0, 1, 2, 5], [0, 1, 2, 5], [0, 1, 2, 5], [], [], [0, 1, 2, 5]][$this->status];
    }

    public function paymentStatus()
    {
        return trans('const.' . config('const.paymentStatuses')[$this->payment_status]['name']);
    }

    public function paymentStatusColor()
    {
        return config('const.paymentStatuses')[$this->payment_status]['color'];
    }

    public function nextPaymentStatuses()
    {
        return [[1, 2], [1, 2], [], [0, 1, 2]][$this->payment_status];
    }

    public function getName()
    {
        return $this->code;
    }

    public function getImages($url = false)
    {
        $url = $url ? url('') : '';

        if (count($this->images ?: []) > 0) {
            return collect($this->images)
                ->transform(function ($i) use ($url) {
                    return $url . Storage::url($i);
                })
                ->toArray();
        } else {
            return [];
        }
    }

    public function scopeFilterQuery($query, $guard, $f_transports, $f_customers, $f_locations, $f_transportTypes, $f_packagePayments, $f_packageTypes, $f_packageStatuses, $f_paymentStatuses)
    {
        return $query
            ->when(isset($f_transports) and count($f_transports) > 0, function ($query) use ($f_transports) {
                return $query->whereIn('transport_id', $f_transports);
            })
            ->when(isset($f_customers) and count($f_customers) > 0, function ($query) use ($f_customers) {
                return $query->whereIn('customer_id', $f_customers);
            })
            ->when(isset($f_locations) and count($f_locations) > 0, function ($query) use ($f_locations) {
                return $query->whereIn('location', $f_locations);
            })
            ->when(isset($f_transportTypes) and count($f_transportTypes) > 0, function ($query) use ($f_transportTypes) {
                return $query->whereIn('transport_type', $f_transportTypes);
            })
            ->when(isset($f_packagePayments) and count($f_packagePayments) > 0, function ($query) use ($f_packagePayments) {
                return $query->whereIn('payment', $f_packagePayments);
            })
            ->when(isset($f_packageTypes) and count($f_packageTypes) > 0, function ($query) use ($f_packageTypes) {
                return $query->whereIn('type', $f_packageTypes);
            })
            ->when(isset($f_packageStatuses) and count($f_packageStatuses) > 0, function ($query) use ($f_packageStatuses) {
                return $query->whereIn('status', $f_packageStatuses);
            })
            ->when(isset($f_paymentStatuses) and count($f_paymentStatuses) > 0, function ($query) use ($f_paymentStatuses) {
                return $query->whereIn('payment_status', $f_paymentStatuses);
            })
            ->when(in_array($guard, ['web', 'api']) and count(auth($guard)->user()->queries['locations']) > 0, function ($query) use ($guard) {
                return $query->whereIn('location', auth($guard)->user()->queries['locations']);
            })
            ->when(in_array($guard, ['web', 'api']) and count(auth($guard)->user()->queries['transportTypes']) > 0, function ($query) use ($guard) {
                return $query->whereIn('transport_type', auth($guard)->user()->queries['transportTypes']);
            })
            ->when(in_array($guard, ['web', 'api']) and count(auth($guard)->user()->queries['packagePayments']) > 0, function ($query) use ($guard) {
                return $query->whereIn('payment', auth($guard)->user()->queries['packagePayments']);
            })
            ->when(in_array($guard, ['web', 'api']) and count(auth($guard)->user()->queries['packageTypes']) > 0, function ($query) use ($guard) {
                return $query->whereIn('type', auth($guard)->user()->queries['packageTypes']);
            })
            ->when(in_array($guard, ['web', 'api']) and count(auth($guard)->user()->queries['packageStatuses']) > 0, function ($query) use ($guard) {
                return $query->whereIn('status', auth($guard)->user()->queries['packageStatuses']);
            })
            ->when(in_array($guard, ['web', 'api']) and count(auth($guard)->user()->queries['paymentStatuses']) > 0, function ($query) use ($guard) {
                return $query->whereIn('payment_status', auth($guard)->user()->queries['paymentStatuses']);
            });
    }
}
