<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function index()
    {
        return view('admin.contact.index');
    }

    public function destroy($id)
    {
        $obj = Contact::findOrFail($id);
        $obj->delete();

        return to_route('admin.contacts.index')
            ->with([
                'success' => trans('app.contact') . ' ' . trans('app.deleted'),
            ]);
    }

    public function archive(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $obj = Contact::findOrFail($request->id);
        $obj->archive = $obj->archive ? 0 : 1;
        $obj->update();

        return response()->json([
            'status' => 1,
            'checked' => $obj->archive,
        ], Response::HTTP_OK);
    }

    public function api(Request $request)
    {
        $columns = [
            'id',
            'name',
            'surname',
            'phone',
            'email',
            'message',
            'archive',
            'created_at',
        ];
        $total = Contact::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = Contact::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Contact::count();
        } else {
            $search = $request->input('search.value');
            $rs = Contact::where('id', 'ilike', "%{$search}%")
                ->orWhere('name', 'ilike', "%{$search}%")
                ->orWhere('surname', 'ilike', "%{$search}%")
                ->orWhere('phone', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Contact::where('id', 'ilike', "%{$search}%")
                ->orWhere('name', 'ilike', "%{$search}%")
                ->orWhere('surname', 'ilike', "%{$search}%")
                ->orWhere('phone', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['name'] = $r->name;
                $nestedData['surname'] = $r->surname;
                $nestedData['phone'] = $r->phone ? '<i class="bi-telephone-fill text-secondary"></i> ' . $r->phone : '';
                $nestedData['email'] = $r->email ? '<i class="bi-envelope-fill text-secondary"></i> ' . $r->email : '';
                $nestedData['message'] = $r->message;
                $nestedData['archive'] = '<div class="form-check form-switch">'
                    . '<input class="form-check-input check-archive" type="checkbox" role="switch" value="' . $r->id . '" id="check' . $r->id . '" ' . ($r->archive ? 'checked' : '') . '>'
                    . '<label class="form-check-label" for="check' . $r->id . '">' . trans('app.archive') . '</label>'
                    . '</div>';
                $nestedData['created_at'] = $r->created_at->format('Y-m-d H:i:s');
                $nestedData['action'] = '<form action="' . route('admin.contacts.destroy', $r->id) . '" method="post">' . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-dark btn-sm mb-1"><i class="bi-trash"></i></button></form>';
                $data[] = $nestedData;
            }
        }

        return json_encode([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($total),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }
}
