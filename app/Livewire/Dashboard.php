<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BillingModel;
use App\Models\RoomModel;
use App\Models\CustomerModel;
use App\Models\PayLogModel;

class Dashboard extends Component{
    public $income = 0;
    public $roomFee = 0;
    public $debt = 0;
    public $pay = 0;

    //รายได้แต่ละเดือน
    public $incomeInMonth = [];

    public function mount(){
        //รายได้
   $incomes = BillingModel::where('status', 'paid')->get();
        //นำยอดรายรับแต่ละรายการมารวมกัน
   foreach($incomes as $income){
    $this->income += $income->sumAmount() + $income->money_added;
   }
        //ห้องว่าง
    //นับจำนวนลูกค้าทั้งหมด
    $countCustomers = CustomerModel::where('status', 'use')->count();
    //นับจำนวนห้องทั้งหมด
    $countRoom = RoomModel::where('status', 'use')->count();
    //นำจำนวนห้องทั้งหมด - จำนวนลูกค้าทั้งหมด
    $this->roomFee = $countRoom - $countCustomers;

        //ค้างจ่าย
    $waits = BillingModel::where('status', 'wait')->get();

    foreach($waits as $wait){
        $this->debt += $wait->sumAmount() + $wait->money_added;
    }
           
        //รายจ่าย
        $this->pay = PayLogModel::where('status', 'use')->sum('amount');  

         //****รายได้แต่ละเดือน*****
         for ($i = 1; $i <= 12; $i++) { 
            $billingsInMonth = BillingModel::where('status', 'paid')
            ->whereMonth('created_at', $i)->get();

            $sum = 0;
            foreach($billingsInMonth as $billing){
                $sum += $billing->sumAmount() + $billing->money_added;
            }
            $this->incomeInMonth[$i] = $sum;           
        }
        
        // dd($this->incomeInMonth);
    }

   

    public function render()
    {
        return view('livewire.dashboard');
    }
}