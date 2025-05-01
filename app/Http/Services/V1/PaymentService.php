<?php

namespace App\Http\Services\V1;

use App\Enums\PaymentStatusEnum;
use App\Http\Resources\v1\PaymentResource;
use App\Models\Payment;
use App\Models\Formation;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function getAllPayments()
    {
        return PaymentResource::collection(
            Payment::with(['student', 'formation'])->get()
        );
    }

    public function getStudentPayments($studentId)
    {
        return PaymentResource::collection(
            Payment::with(['formation'])
                ->where('student_id', $studentId)
                ->orderBy('payment_date', 'desc')
                ->get()
        );
    }

    public function createPayment($data)
    {
        try {
            DB::beginTransaction();

            $formation = Formation::findOrFail($data['formation_id']);
            $existingPayments = Payment::where('student_id', $data['student_id'])
                ->where('formation_id', $data['formation_id'])
                ->sum('amount');

            $remainingAmount = $formation->price - ($existingPayments + $data['amount']);

            $status = $remainingAmount <= 0 ? PaymentStatusEnum::COMPLETED->value : PaymentStatusEnum::PARTIAL->value;

            Payment::create([
                ...$data,
                'remaining_amount' => max(0, $remainingAmount),
                'status' => $status
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getPaymentDetails($id)
    {
        return new PaymentResource(
            Payment::with(['student', 'formation'])->findOrFail($id)
        );
    }

    public function updatePayment($id, $data)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::find($id);
            if (!$payment) {
                return false;
            }

            $formation = Formation::findOrFail($data['formation_id'] ?? $payment->formation_id);

            // Calculate total payments excluding current payment
            $existingPayments = Payment::where('student_id', $data['student_id'] ?? $payment->student_id)
                ->where('formation_id', $data['formation_id'] ?? $payment->formation_id)
                ->where('id', '!=', $id)
                ->sum('amount');

            // Add new amount if provided, otherwise use existing amount
            $totalAmount = $existingPayments + ($data['amount'] ?? $payment->amount);

            $remainingAmount = $formation->price - $totalAmount;
            $status = $remainingAmount <= 0 ? PaymentStatusEnum::COMPLETED->value : PaymentStatusEnum::PARTIAL->value;

            $payment->update([
                ...$data,
                'remaining_amount' => max(0, $remainingAmount),
                'status' => $status
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function deletePayment($id)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::find($id);
            if (!$payment) {
                return false;
            }

            $payment->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
