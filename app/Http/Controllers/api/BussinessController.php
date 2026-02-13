<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Bussiness;
use App\Http\Controllers\Controller;


class BussinessController extends Controller
{
     public function index()
    {
        return Bussiness::latest()->get();
    }

    public function store(Request $request)
    {
        $save = $request->validate([
            'bussiness_type' => 'required|unique:bussinesses,bussiness_type',

        ]);

        return Bussiness::create($save);
    }

    public function update(Request $request, $id)
    {
        $type = Bussiness::findOrFail($id);
        $data = $request->validate([
           'bussiness_type' => 'required|unique:bussinesses,bussiness_type,' . $type->id,

        ]);
        $type->update($data);
        return $type;
    }

    public function destroy($id)
    {
        Bussiness::destroy($id);
        return response()->json(['success' => true]);
    }
    public function show($id)
    {
        return Bussiness::findOrFail($id);
    }

}


