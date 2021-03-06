<?php

namespace App\Http\Controllers;

use App\Debit;
use App\Purpose;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data=Debit::where('date', '>', Carbon::now()->subDays(30))->get();
        return view('debit.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $purposes=Purpose::where('type', '=', 'Dr')->pluck('name', 'id');
        return view('debit.create', compact('purposes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input=$request->all();
        $input['user_id']=Auth::user()->id;
        try{
            $data=Debit::create($input);
            $bug=0;
        }
        catch (\Exception $e){
            $bug=$e->errorInfo[1];
        }

        if($bug==0){
            return redirect('/debits')->with('success', 'Credit Item has been added');
        }
        else{
            return redirect()->back()->with('error', 'Something Went Wrong!')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Credit  $credit
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return abort('404');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Credit  $credit
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purposes=Purpose::where('type', '=', 'Dr')->pluck('name', 'id');
        $data=Debit::findOrFail($id);
        return view('debit.edit', compact('purposes','data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Credit  $credit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data=Debit::findOrFail($id);
        $input=$request->all();
        $input['user_id']=Auth::user()->id;

        try{
            $data->update($input);
            $bug=0;
        }
        catch (Exception $e){
            $bug=1;
        }
        if($bug==0){
            return redirect('/debits')->with('success', 'Item has been updated');
        }
        else{
            return redirect()->back()->with('error', 'Something Went Wrong!')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Credit  $credit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data=Debit::findOrFail($id);
        $data->delete();
        return redirect()->back()->with('info', 'Item has been Deleted!');
    }
}
