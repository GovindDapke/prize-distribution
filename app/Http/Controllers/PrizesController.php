<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use Illuminate\Http\Request;

class PrizesController extends Controller
{
    public function index()
    {
        $prizes = Prize::all();
        return view('prizes.index', ['prizes' => $prizes]);
    }

    // public function create()
    // {
    //     return view('prizes.create');
    // }
    public function create()
    {
        $prizes = Prize::all(); // Adjust this according to your model and data retrieval method
        return view('prizes.create', compact('prizes'));
    }
    
    // public function store(Request $request)
    // {
    //     $prize = new Prize;
    //     $prize->title = $request->input('title');
    //     $prize->probability = floatval($request->input('probability'));
    //     $prize->save();

    //     return redirect()->route('prizes.index');
    // }

    public function store(Request $request)
{
    // Validate individual probability
    $request->validate([
        'title' => 'required|string',
        'probability' => 'required|numeric|min:0|max:100',
    ]);

    // Calculate total probability
    $totalProbability = Prize::sum('probability') + $request->probability;

    if ($totalProbability > 100) {
        return redirect()->back()->withErrors(['probability' => 'Total probability cannot exceed 100%.']);
    }

    // Store the prize
    Prize::create($request->all());

    return redirect()->route('prizes.index')->with('success', 'Prize created successfully.');
}


    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    public function update(Request $request, $id)
    {
        $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return redirect()->route('prizes.index');
    }

    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return redirect()->route('prizes.index');
    }

    public function simulate(Request $request)
    {
        $numberOfPrizes = $request->input('number_of_prizes', 10);

        // Simulate prize distribution
        for ($i = 0; $i < $numberOfPrizes; $i++) {
            $prize = Prize::inRandomOrder()->first();
            if ($prize) {
                $prize->increment('awarded_count');
            }
        }

        $prizes = Prize::all();
        return response()->json(['prizes' => $prizes]);
    }

    public function reset()
    {
        Prize::query()->update(['awarded_count' => 0]);
        $prizes = Prize::all();
        return response()->json(['prizes' => $prizes]);
    }
}
