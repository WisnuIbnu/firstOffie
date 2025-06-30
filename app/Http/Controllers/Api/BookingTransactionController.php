<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingTransaction;
use App\Models\OfficeSpace; // Assuming you have an OfficeSpace model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingTransactionController extends Controller
{

    public function store(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'office_space_id' => 'required|exists:office_spaces,id',
            'started_at' => 'required|date_format:Y-m-d|after_or_equal:today',
            'duration' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find the office space to get its price
            $officeSpace = OfficeSpace::find($request->office_space_id);
            if (!$officeSpace) {
                return response()->json([
                    'message' => 'Office space not found.',
                    'status' => 'failed'
                ], 404);
            }

            // Calculate total amount
            $totalAmount = $officeSpace->price_per_day * $request->duration;

            // Calculate ended_at date
            $startedAt = Carbon::parse($request->started_at);
            $endedAt = $startedAt->copy()->addDays($request->duration - 1); // Use copy() to avoid modifying $startedAt

            // Check for overlapping bookings for the specific office space
            $overlappingBooking = BookingTransaction::where('office_space_id', $request->office_space_id)
                ->where(function ($query) use ($startedAt, $endedAt) {
                    $query->whereBetween('started_at', [$startedAt, $endedAt])
                          ->orWhereBetween('ended_at', [$startedAt, $endedAt])
                          ->orWhere(function ($query) use ($startedAt, $endedAt) {
                              $query->where('started_at', '<=', $startedAt)
                                    ->where('ended_at', '>=', $endedAt);
                          });
                })
                ->where('is_paid', true) // Only consider paid bookings as occupied
                ->exists();

            if ($overlappingBooking) {
                return response()->json([
                    'message' => 'The selected office space is already booked for the specified dates. Please choose different dates or another office space.',
                    'status' => 'failed'
                ], 409);
            }

            // Generate unique booking transaction ID
            $bookingTrxId = BookingTransaction::generateUniqueTrxId();

            // Create the booking transaction
            $booking = BookingTransaction::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'booking_trx_id' => $bookingTrxId,
                'is_paid' => false, // Initial status is unpaid
                'started_at' => $request->started_at,
                'total_amount' => $totalAmount,
                'duration' => $request->duration,
                'ended_at' => $endedAt,
                'office_space_id' => $request->office_space_id,
            ]);

            return response()->json([
                'message' => 'Booking created successfully. Please proceed with payment.',
                'status' => 'success',
                'data' => $booking
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create booking. ' . $e->getMessage(),
                'status' => 'failed',
            ], 500);
        }
    }

    public function booking_details(Request $request)
    {
        // For /check-booking, typically the booking_trx_id would be in the request body
        $validator = Validator::make($request->all(), [
            'booking_trx_id' => 'required|string|exists:booking_transactions,booking_trx_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = BookingTransaction::where('booking_trx_id', $request->booking_trx_id)
                                         ->with('officeSpace')
                                         ->first();


            if (!$booking) {
                return response()->json([
                    'message' => 'Booking transaction not found.',
                    'status' => 'failed'
                ], 404);
            }

            return response()->json([
                'message' => 'Booking transaction details retrieved successfully.',
                'status' => 'success',
                'data' => $booking
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve booking details. ' . $e->getMessage(),
                'status' => 'failed',
            ], 500);
        }
    }
}