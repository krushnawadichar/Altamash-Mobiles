<?php

namespace App\Services;

use App\Models\Repair;
use App\Repositories\RepairRepository;
use App\Services\CustomerService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RepairService
{
    protected $repairRepository;
    protected $customerService;

    public function __construct(RepairRepository $repairRepository, CustomerService $customerService)
    {
        $this->repairRepository = $repairRepository;
        $this->customerService = $customerService;
    }

    public function getAllRepairs()
    {
        return $this->repairRepository->getWithRelations();
    }

    public function getRepairById($id)
    {
        return $this->repairRepository->find($id);
    }

    public function createRepair(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle customer
            $customerId = $this->handleCustomer($data);

            // Calculate remaining amount
            $estimatedCost = $data['estimated_cost'] ?? 0;
            $advancePaid = $data['advance_paid'] ?? 0;
            $remainingAmount = $estimatedCost - $advancePaid;

            $repairData = [
                'repair_number' => $this->generateRepairNumber(),
                'customer_id' => $customerId,
                'customer_name' => $data['customer_name'] ?? '',
                'customer_mobile' => $data['customer_mobile'] ?? '',
                'device_name' => $data['device_name'],
                'imei' => $data['imei'] ?? null,
                'issue' => $data['issue'],
                'accessories_received' => $data['accessories_received'] ?? null,
                'estimated_cost' => $estimatedCost,
                'advance_paid' => $advancePaid,
                'remaining_amount' => $remainingAmount,
                'engineer_notes' => $data['engineer_notes'] ?? null,
                'repair_status_id' => $data['repair_status_id'],
                'receive_date' => $data['receive_date'],
                'delivery_date' => $data['delivery_date'] ?? null,
                'payment_status' => $this->getPaymentStatus($advancePaid, $estimatedCost),
                'created_by' => auth()->id(),
            ];

            // Handle images
            if (isset($data['images']) && $data['images']) {
                $images = [];
                foreach ($data['images'] as $image) {
                    $images[] = $this->uploadImage($image);
                }
                $repairData['images'] = $images;
            }

            // Handle documents
            if (isset($data['documents']) && $data['documents']) {
                $documents = [];
                foreach ($data['documents'] as $document) {
                    $documents[] = $this->uploadDocument($document);
                }
                $repairData['documents'] = $documents;
            }

            return $this->repairRepository->create($repairData);
        });
    }

    public function updateRepair($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $repair = $this->repairRepository->find($id);

            // Handle customer
            if (isset($data['customer_id']) || isset($data['customer_name'])) {
                $customerId = $this->handleCustomer($data);
                $data['customer_id'] = $customerId;
            }

            // Calculate remaining amount
            $estimatedCost = $data['estimated_cost'] ?? $repair->estimated_cost;
            $advancePaid = $data['advance_paid'] ?? $repair->advance_paid;
            $data['remaining_amount'] = $estimatedCost - $advancePaid;
            $data['payment_status'] = $this->getPaymentStatus($advancePaid, $estimatedCost);

            // Handle images
            if (isset($data['images']) && $data['images']) {
                // Delete old images
                if ($repair->images) {
                    foreach ($repair->images as $oldImage) {
                        Storage::delete('public/' . $oldImage);
                    }
                }
                $images = [];
                foreach ($data['images'] as $image) {
                    $images[] = $this->uploadImage($image);
                }
                $data['images'] = $images;
            }

            // Handle documents
            if (isset($data['documents']) && $data['documents']) {
                // Delete old documents
                if ($repair->documents) {
                    foreach ($repair->documents as $oldDocument) {
                        Storage::delete('public/' . $oldDocument);
                    }
                }
                $documents = [];
                foreach ($data['documents'] as $document) {
                    $documents[] = $this->uploadDocument($document);
                }
                $data['documents'] = $documents;
            }

            return $this->repairRepository->update($repair, $data);
        });
    }

    public function deleteRepair($id)
    {
        return DB::transaction(function () use ($id) {
            $repair = $this->repairRepository->find($id);

            // Delete images
            if ($repair->images) {
                foreach ($repair->images as $image) {
                    Storage::delete('public/' . $image);
                }
            }

            // Delete documents
            if ($repair->documents) {
                foreach ($repair->documents as $document) {
                    Storage::delete('public/' . $document);
                }
            }

            return $this->repairRepository->delete($repair);
        });
    }

    public function updateStatus($id, $statusId)
    {
        $repair = $this->repairRepository->find($id);
        $repair->repair_status_id = $statusId;

        // If status is completed or delivered, set delivery date
        $status = \App\Models\RepairStatus::find($statusId);
        if ($status && in_array($status->name, ['Completed', 'Delivered'])) {
            $repair->delivery_date = now()->format('Y-m-d');
        }

        return $repair->save();
    }

    protected function handleCustomer($data)
    {
        if (isset($data['customer_id']) && $data['customer_id']) {
            return $data['customer_id'];
        }

        if (isset($data['customer_name']) && $data['customer_name']) {
            // Check if customer exists by phone
            $customer = null;
            if (isset($data['customer_mobile'])) {
                $customer = $this->customerService->getCustomerByPhone($data['customer_mobile']);
            }

            if (!$customer) {
                $customer = $this->customerService->createCustomer([
                    'name' => $data['customer_name'],
                    'email' => 'repair_' . time() . '@example.com',
                    'phone' => $data['customer_mobile'] ?? 'N/A',
                    'address' => $data['customer_address'] ?? null,
                    'is_active' => true,
                ]);
            }

            return $customer->id;
        }

        return null;
    }

    protected function getPaymentStatus($paid, $total)
    {
        if ($paid >= $total && $total > 0) {
            return 'paid';
        } elseif ($paid > 0) {
            return 'partial';
        }
        return 'pending';
    }

    protected function generateRepairNumber()
    {
        $prefix = 'REP';
        $year = date('Y');
        $month = date('m');
        $lastRepair = Repair::orderBy('id', 'desc')->first();
        $number = $lastRepair ? intval(substr($lastRepair->repair_number, -4)) + 1 : 1;

        return $prefix . '-' . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    protected function uploadImage($image)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('repairs/images', $filename, 'public');
    }

    protected function uploadDocument($document)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
        return $document->storeAs('repairs/documents', $filename, 'public');
    }

    public function getPendingRepairs()
    {
        return $this->repairRepository->getPending();
    }

    public function getCompletedRepairs()
    {
        return $this->repairRepository->getCompleted();
    }

    public function getRepairsByCustomer($customerId)
    {
        return $this->repairRepository->getByCustomer($customerId);
    }

    public function getRepairsByStatus($statusId)
    {
        return $this->repairRepository->getByStatus($statusId);
    }

    public function getRepairsByDateRange($startDate, $endDate)
    {
        return $this->repairRepository->getByDateRange($startDate, $endDate);
    }

    public function getPendingPayments()
    {
        return $this->repairRepository->getPendingPayments();
    }

    public function searchRepairs($query)
    {
        return $this->repairRepository->search($query);
    }

    public function getTotalRepairs($startDate = null, $endDate = null)
    {
        return $this->repairRepository->getTotalRepairs($startDate, $endDate);
    }

    public function getTotalRevenue($startDate = null, $endDate = null)
    {
        return $this->repairRepository->getTotalRevenue($startDate, $endDate);
    }

    public function getRecentRepairs($limit = 10)
    {
        return $this->repairRepository->getRecentRepairs($limit);
    }

    public function getRepairStatistics()
    {
        $total = $this->repairRepository->count();
        $pending = $this->repairRepository->getPending()->count();
        $completed = $this->repairRepository->getCompleted()->count();
        $cancelled = $this->repairRepository->getCancelled()->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}