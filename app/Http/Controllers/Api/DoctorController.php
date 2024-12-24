<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = User::where('role', 'doctor')->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'role' => 'required|string',
            'clinic_id' => 'required',
            'specialist_id' => 'required',]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $doctor = User::create($data);

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $file_path = $image->storeAs('doctor', $image_name, 'public');
            $doctor->image = '/storage/' . $file_path;
            $doctor->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $doctor,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string',
            'clinic_id' => 'required',
            'specialist_id' => 'required',
        ]);

        $doctor = User::find($id);
        $doctor->update($request->all());

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $file_path = $image->storeAs('doctor', $image_name, 'public');
            $doctor->image = '/storage/' . $file_path;
            $doctor->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $doctor,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $doctor = User::find($id);
        // delete image
        if ($doctor->image) {
            $image_path = str_replace('/storage', 'public', $doctor->image);
            Storage::delete($image_path);
        }
        $doctor->delete();


        return response()->json([
            'status' => 'success',
            'message' => 'Doctor deleted successfully',
        ]);
    }

    public function getDoctorActive()
    {
        $doctors = User::where('role', 'doctor')->where('status', 'active')->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors,
        ]);
    }

    // get search doctor by name and category specialist
    public function searchDoctor(Request $request)
    {
        $doctors = User::where('role', 'doctor')
            ->where('name', 'like', '%' . $request->name . '%')
            ->where('specialist_id', $request->specialist_id)
            ->with('clinic', 'specialist')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors,
        ]);
    }

    // get doctor by id
    public function getDoctorById(string $id)
    {
        $doctor = User::find($id);

        return response()->json([
            'status' => 'success',
            'data' => $doctor,
        ]);
    }

    // get doctor by clinic id
    public function getDoctorByClinicId($clinic_id)
    {
        $doctors = User::where('clinic_id', $clinic_id)->where('role', 'doctor')->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors,
        ]);
    }

    // get doctor by specialist id
    public function getDoctorBySpecialistId($specialist_id)
    {
        $doctors = User::where('specialist_id', $specialist_id)->where('role', 'doctor')->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors,
        ]);
    }
}
