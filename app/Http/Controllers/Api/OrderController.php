<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('patient', 'doctor', 'clinic')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'service' => 'required',
            'price' => 'required',
            'duration' => 'required',
            'clinic_id' => 'required',
            'schedule' => 'required',
        ]);

        $order = Order::create($request->all());

        Configuration::setXenditKey(env("XENDIT_SERVER_KEY"));

        $apiInstance = new InvoiceApi();
        $create_invoice_request = new CreateInvoiceRequest([
        'external_id' => 'INV-'.$order->id,
        'description' => 'Payment for ' . $order->service,
        'amount' => $order->price,
        'invoice_duration' => 172800,
        'currency' => 'IDR',
        'reminder_time' => 1,
        'success_redirect_url' => 'flutter/success',
        'failure_redirect_url' => 'flutter/failure',
        ]);

        try {
            $result = $apiInstance->createInvoice($create_invoice_request);
            $payment_url = $result->getInvoiceUrl();
            $order->payment_url = $payment_url;
            $order->save();

            return response()->json([
                'status' => 'success',
                'data' => $order,
            ], 201);
        } catch (\Xendit\XenditSdkException $e) {
            echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }
    }

    public function handleCallback(Request $request)
    {
        //check header 'x-callback-token'
        $xenditCallbackToken = env('XENDIT_CALLBACK_TOKEN', '');
        $callbackToken = $request->header('x-callback-token');
        if ($callbackToken !== $xenditCallbackToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $request->all();
        $external_id = $data['external_id'];
        $order = Order::where('id', explode('-', $external_id)[1])->first();
        $order->status = $data['status'];
        $order->save();

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
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
        //
    }

    // get order by patient desc
    public function getOrderByPatient(string $id)
    {
        $orders = Order::where('patient_id', $id)->with('patient', 'doctor', 'clinic')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    // get order by doctor desc
    public function getOrderByDoctor(string $id)
    {
        $orders = Order::where('doctor_id', $id)->with('patient', 'doctor', 'clinic')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    // get order by clinic desc
    public function getOrderByClinic(string $id)
    {
        $orders = Order::where('clinic_id', $id)->with('patient', 'doctor', 'clinic')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    // admin clinic summary
    public function getSummary(string $id)
    {
        $orders = Order::where('clinic_id', $id)->with('patient', 'doctor', 'clinic')->get();

        $order_count = $orders->count();
        $total_income = $orders->where('status', 'paid')->sum('price');
        $doctor_count = $orders->groupBy('doctor_id')->count();
        $patient_count = $orders->groupBy('patient_id')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_count' => $order_count,
                'total_income' => $total_income,
                'doctor_count' => $doctor_count,
                'patient_count' => $patient_count,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
