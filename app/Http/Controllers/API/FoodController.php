<?php

namespace App\Http\Controllers\API;

use App\Models\Food;
use Illuminate\Http\Request;
use App\Http\Requests\FoodRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;

class FoodController extends BaseController
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $types = $request->input('types');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        $rate_from = $request->input('rate_from');
        $rate_to = $request->input('rate_to');

        if($id)
        {
            $food = Food::find($id);

            if($food)
                return $this->success(
                    $food,
                    'Data produk berhasil diambil'
                );
            else
                return  $this->error(
                    null,
                    'Data produk tidak ada',
                    404
                );
        }

        $food = Food::query();

        if($name)
            $food->where('name', 'like', '%' . $name . '%');

        if($types)
            $food->where('types', 'like', '%' . $types . '%');

        if($price_from)
            $food->where('price', '>=', $price_from);

        if($price_to)
            $food->where('price', '<=', $price_to);

        if($rate_from)
            $food->where('rate', '>=', $rate_from);

        if($rate_to)
            $food->where('rate', '<=', $rate_to);

        return  $this->success(
            $food->paginate($limit),
            'Data list produk berhasil diambil'
        );
    }
    public function store(FoodRequest $request)
    {
        // $data = $request->all();
        // $data = Food::create([
        //     'name' => $request->name,
        //     'description' => $request->description,
        //     'ingredients' => $request->ingredients,
        //     'price' => $request->price,
        //     'rate' => $request->rate,
        //     'type' => $request->type,
        //     'picturePath' => ''
        // ]);
        $data = $request->all();

        $data['picturePath'] = $request->file('picturePath')->store('assets/food', 'public');

        Food::create($data);

        // return redirect()->route('food.index');
        return $this->success($data, 'Data Food Berhasil Dibuat');
    }
    public function show($id)
    {
        $food = Food::find($id);

        if (is_null($food)) {
            return $this->error('Data list makanan ini tidak berhasil diambil');
        }
        return  $this->success($food,'Data list makanan berhasil diambil');
    }
    public function update(Request $request, Food $food)
    {
        $data = $request->all();

        if($request->file('picturePath'))
        {
            $data['picturePath'] = $request->file('picturePath')->store('assets/food', 'public');
        }
        // dd($data);

        $food->name = $request->name;
        $food->description = $request->description;
        $food->ingredients = $request->ingredients;
        $food->price = $request->price;
        $food->rate = $request->rate;
        $food->types = $request->types;
        $food->picturePath = $request->picturePath;
        $food->save();
        // $food->update($data);

        // return redirect()->route('food.index');
        return $this->success($food, 'Update Data Food Berhasil');
    }
    public function destroy(Food $food)
    {
        $food->delete();

        return redirect()->route('food.index');
    }
}
