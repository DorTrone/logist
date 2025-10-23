<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class BannerController extends Controller
{
    public function index()
    {
        $objs = Banner::orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.banner.index')
            ->with([
                'objs' => $objs,
            ]);
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_tm' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_ru' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_cn' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'image_2_tm' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'image_2_ru' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'image_2_cn' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'url' => ['nullable', 'string', 'max:255'],
            'datetime_start' => ['required', 'date'],
            'datetime_end' => ['required', 'date'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        $obj = new Banner();
        $obj->url = $request->url ?: null;
        $obj->datetime_start = Carbon::parse($request->datetime_start);
        $obj->datetime_end = Carbon::parse($request->datetime_end);
        $obj->sort_order = $request->sort_order;
        if ($request->hasfile('image')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image = $imageUrl;
        }
        if ($request->hasfile('image_tm')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_tm'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_tm = $imageUrl;
        }
        if ($request->hasfile('image_ru')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_ru'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_ru = $imageUrl;
        }
        if ($request->hasfile('image_cn')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_cn'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_cn = $imageUrl;
        }
        if ($request->hasfile('image_2')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2 = $imageUrl;
        }
        if ($request->hasfile('image_2_tm')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2_tm'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2_tm = $imageUrl;
        }
        if ($request->hasfile('image_2_ru')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2_ru'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2_ru = $imageUrl;
        }
        if ($request->hasfile('image_2_cn')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2_cn'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2_cn = $imageUrl;
        }
        $obj->save();

        return to_route('admin.banners.index')
            ->with([
                'success' => trans('app.banner') . ' ' . trans('app.added'),
            ]);
    }

    public function edit($id)
    {
        $obj = Banner::findOrFail($id);

        return view('admin.banner.edit')
            ->with([
                'obj' => $obj,
            ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_tm' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_ru' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_cn' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=400,height=400'],
            'image_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'image_2_tm' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'image_2_ru' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'image_2_cn' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', 'dimensions:width=800'],
            'url' => ['nullable', 'string', 'max:255'],
            'datetime_start' => ['required', 'date'],
            'datetime_end' => ['required', 'date'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        $obj = Banner::findOrFail($id);
        $obj->url = $request->url ?: null;
        $obj->datetime_start = Carbon::parse($request->datetime_start);
        $obj->datetime_end = Carbon::parse($request->datetime_end);
        $obj->sort_order = $request->sort_order;
        if ($request->hasfile('image')) {
            if ($obj->image) {
                Storage::disk('public')->delete($obj->image);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image = $imageUrl;
        }
        if ($request->hasfile('image_tm')) {
            if ($obj->image) {
                Storage::disk('public')->delete($obj->image);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_tm'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_tm = $imageUrl;
        }
        if ($request->hasfile('image_ru')) {
            if ($obj->image_ru) {
                Storage::disk('public')->delete($obj->image_ru);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_ru'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_ru = $imageUrl;
        }
        if ($request->hasfile('image_cn')) {
            if ($obj->image_cn) {
                Storage::disk('public')->delete($obj->image_cn);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_cn'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_cn = $imageUrl;
        }
        if ($request->hasfile('image_2')) {
            if ($obj->image_2) {
                Storage::disk('public')->delete($obj->image_2);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2 = $imageUrl;
        }
        if ($request->hasfile('image_2_tm')) {
            if ($obj->image_2_tm) {
                Storage::disk('public')->delete($obj->image_2_tm);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2_tm'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2_tm = $imageUrl;
        }
        if ($request->hasfile('image_2_ru')) {
            if ($obj->image_2_ru) {
                Storage::disk('public')->delete($obj->image_2_ru);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2_ru'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2_ru = $imageUrl;
        }
        if ($request->hasfile('image_2_cn')) {
            if ($obj->image_2_cn) {
                Storage::disk('public')->delete($obj->image_2_cn);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image_2_cn'))->toWebp(85)->toString();
            $imageUrl = 'b/' . str()->random(10) . '.' . 'webp';
            Storage::disk('public')->put($imageUrl, $image);
            $obj->image_2_cn = $imageUrl;
        }
        $obj->update();

        return to_route('admin.banners.index')
            ->with([
                'success' => trans('app.banner') . ' ' . trans('app.updated'),
            ]);
    }

    public function destroy($id)
    {
        $obj = Banner::findOrFail($id);
        if ($obj->image) {
            Storage::disk('public')->delete($obj->image);
        }
        if ($obj->image_tm) {
            Storage::disk('public')->delete($obj->image_tm);
        }
        if ($obj->image_ru) {
            Storage::disk('public')->delete($obj->image_ru);
        }
        if ($obj->image_cn) {
            Storage::disk('public')->delete($obj->image_cn);
        }
        if ($obj->image_2) {
            Storage::disk('public')->delete($obj->image_2);
        }
        if ($obj->image_2_tm) {
            Storage::disk('public')->delete($obj->image_2_tm);
        }
        if ($obj->image_2_ru) {
            Storage::disk('public')->delete($obj->image_2_ru);
        }
        if ($obj->image_2_cn) {
            Storage::disk('public')->delete($obj->image_2_cn);
        }
        $obj->delete();

        return to_route('admin.banners.index')
            ->with([
                'success' => trans('app.banner') . ' ' . trans('app.deleted'),
            ]);
    }

    public function up(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|min:1',
        ]);

        $obj = Banner::findOrFail($request->id);
        if ($obj->sort_order < 999) {
            $obj->sort_order += 1;
        }
        $obj->update();

        return response()->json([
            'status' => 1,
            'sort_order' => $obj->sort_order,
        ], Response::HTTP_OK);
    }

    public function down(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|min:1',
        ]);

        $obj = Banner::findOrFail($request->id);
        if ($obj->sort_order > 1) {
            $obj->sort_order -= 1;
        }
        $obj->update();

        return response()->json([
            'status' => 1,
            'sort_order' => $obj->sort_order,
        ], Response::HTTP_OK);
    }
}
