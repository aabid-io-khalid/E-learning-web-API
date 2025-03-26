<?php

namespace App\Http\Controllers\Api\V3;

use App\Models\Course;
use App\Models\Payment;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


/**
 * @OA\Tag(
 *     name="Payments",
 *     description="Endpoints for payment processing"
 * )
 */
class PaymentController extends Controller
{

        /**
     * @OA\Post(
     *     path="/api/v3/payments/checkout",
     *     summary="Initiate a payment checkout session",
     *     tags={"Payments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_id"},
     *             @OA\Property(property="course_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Checkout session created",
     *         @OA\JsonContent(
     *             @OA\Property(property="checkout_url", type="string", example="https://checkout.stripe.com/pay/cs_test_abc123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="course_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected course id is invalid.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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


        /**
     * @OA\Get(
     *     path="/api/v3/payments/status/{session_id}",
     *     summary="Check payment status",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="session_id",
     *         in="path",
     *         required=true,
     *         description="Stripe session ID",
     *         @OA\Schema(type="string", example="cs_test_abc123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment status",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"pending", "completed"}, example="completed"),
     *             @OA\Property(
     *                 property="payment",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", format="float", example=99.99),
     *                 @OA\Property(property="currency", type="string", example="eur"),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found")
     *         )
     *     )
     * )
     */

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

        /**
     * @OA\Get(
     *     path="/api/v3/payments/history",
     *     summary="Get user payment history",
     *     tags={"Payments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user payments",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", format="float", example=99.99),
     *                 @OA\Property(property="currency", type="string", example="eur"),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="course",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Advanced Programming")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function history(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
                         ->with('course')
                         ->latest()
                         ->get();

        return response()->json($payments);
    }

}
