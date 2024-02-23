<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    /**
     * Process payment for a user.
     */
    public function payUser(Request $request): JsonResponse
    {

        $this->authorize('create', Payment::class);

        try {
            $userId = $request->input('user_id');

            if ($userId) {
                $user = User::findOrFail($userId);

                $request->validate([
                    'value' => 'required|numeric',
                    'payment_date' => 'date',
                ]);

                $paymentDate = $request->input('payment_date', now()->toDateString());

                $payment = Payment::create([
                    'user_id' => $user->id,
                    'value' => $request->input('value'),
                    'payment_date' => $paymentDate,
                ]);

                return $this->successResponse('Payment successful', ['payment' => $payment]);
            } else {
                return $this->errorResponse('User not found', 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Error processing payment', 500);
        }
    }

    /**
     * Get payments for a specific user.
     */
    public function getPaymentsForUser(Request $request, $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $perPage = $request->input('per_page', 10);

            $payments = Payment::where('user_id', $user->id)->paginate($perPage);

            return $this->successResponse('Payments retrieved successfully', ['payments' => $payments]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('User not found', 404);
        }
    }
}
