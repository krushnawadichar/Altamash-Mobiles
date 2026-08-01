<?php

namespace App\Repositories;

use App\Models\Repair;

class RepairRepository extends BaseRepository
{
    public function __construct(Repair $repair)
    {
        parent::__construct($repair);
    }

    public function getByRepairNumber($repairNumber)
    {
        return $this->model->where('repair_number', $repairNumber)->first();
    }

    public function getWithRelations()
    {
        return $this->model->with(['customer', 'repairStatus'])->get();
    }

    public function getByCustomer($customerId)
    {
        return $this->model->where('customer_id', $customerId)
                          ->with(['customer', 'repairStatus'])
                          ->get();
    }

    public function getByStatus($statusId)
    {
        return $this->model->where('repair_status_id', $statusId)
                          ->with(['customer', 'repairStatus'])
                          ->get();
    }

    public function getPending()
    {
        return $this->model->whereHas('repairStatus', function($q) {
            $q->whereIn('name', ['Pending', 'Checking', 'Waiting Parts', 'Repairing']);
        })->with(['customer', 'repairStatus'])
          ->get();
    }

    public function getCompleted()
    {
        return $this->model->whereHas('repairStatus', function($q) {
            $q->where('name', 'Completed');
        })->with(['customer', 'repairStatus'])
          ->get();
    }

    public function getDelivered()
    {
        return $this->model->whereHas('repairStatus', function($q) {
            $q->where('name', 'Delivered');
        })->with(['customer', 'repairStatus'])
          ->get();
    }

    public function getCancelled()
    {
        return $this->model->whereHas('repairStatus', function($q) {
            $q->where('name', 'Cancelled');
        })->with(['customer', 'repairStatus'])
          ->get();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('receive_date', [$startDate, $endDate])
                          ->with(['customer', 'repairStatus'])
                          ->get();
    }

    public function getByPaymentStatus($status)
    {
        return $this->model->where('payment_status', $status)
                          ->with(['customer', 'repairStatus'])
                          ->get();
    }

    public function getPendingPayments()
    {
        return $this->model->where('payment_status', '!=', 'paid')
                          ->where('remaining_amount', '>', 0)
                          ->with('customer')
                          ->get();
    }

    public function search($query)
    {
        return $this->model->where('repair_number', 'like', "%{$query}%")
                          ->orWhere('customer_name', 'like', "%{$query}%")
                          ->orWhere('customer_mobile', 'like', "%{$query}%")
                          ->orWhere('device_name', 'like', "%{$query}%")
                          ->orWhere('imei', 'like', "%{$query}%")
                          ->with(['customer', 'repairStatus'])
                          ->paginate(15);
    }

    public function getRecentRepairs($limit = 10)
    {
        return $this->model->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->with(['customer', 'repairStatus'])
                          ->get();
    }

    public function getLastRepair()
    {
        return $this->model->orderBy('id', 'desc')->first();
    }

    public function getTotalRepairs($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('receive_date', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    public function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('receive_date', [$startDate, $endDate]);
        }
        
        return $query->sum('estimated_cost');
    }
}