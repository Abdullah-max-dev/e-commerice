<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Controller;


class BussinessController extends Controller
{

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
            'bussiness_type' => 'required',

        ]);
        $type->update($data);
        return $type;
    }

    public function destroy($id)
    {
        Bussiness::destroy($id);
        return response()->json(['success' => true]);
    }
    public function show()
    {
        return Category::latest()->get();
    }

}


