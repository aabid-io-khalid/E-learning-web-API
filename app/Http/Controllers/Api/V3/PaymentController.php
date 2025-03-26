<?php

namespace App\Http\Controllers\Api\V3;

use App\Models\Course;
use App\Models\Payment;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::find($request->course_id);
        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $course->name,
                    ],
                    'unit_amount' => $course->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => config('app.frontend_url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.frontend_url') . '/payment/cancel',
            'metadata' => [
                'user_id' => $request->user()->id,
                'course_id' => $course->id
            ]
        ]);

        Payment::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'stripe_session_id' => $session->id,
            'amount' => $course->price,
            'currency' => 'eur'
        ]);

        return response()->json([
            'checkout_url' => $session->url
        ]);
    }

    public function status($id)
    {
        $payment = Payment::where('stripe_session_id', $id)->firstOrFail();
        
        if ($payment->status === 'pending') {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($id);
            
            if ($session->payment_status === 'paid') {
                $payment->update(['status' => 'completed']);
            }
        }

        return response()->json([
            'status' => $payment->status,
            'payment' => $payment
        ]);
    }

    public function history(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
                         ->with('course')
                         ->latest()
                         ->get();

        return response()->json($payments);
    }

}
