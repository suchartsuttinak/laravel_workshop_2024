<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BillingModel;
use App\Models\RoomModel;
use App\Models\CustomerModel;
use App\Models\PayLogModel;
use Illuminate\Support\Facades\Log;


class Dashboard extends Component{
    public $income = 0;
    public $roomFee = 0;
    public $debt = 0;
    public $pay = 0;
    //รายได้แต่ละเดือน
    public $incomeInMonths = [];
    public $incomePie = [];
    public $yearList = [];
    public $monthList = [
        'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
        'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม',
    ];
    public $selectedYear;
    public $selectedMonth;
   

    public function mount(){
        $this->selectedYear = date('Y');
        $this->selectedMonth = date('m');

        // read from url
        if (request()->has('year') && request()->has('month')) {
            $this->selectedYear = request()->query('year');
            $this->selectedMonth = request()->query('month');
        }
        
        // year list 5 year ago
        for ($i = 0; $i < 5; $i++) {
            $this->yearList[] = date('Y') - $i;
        }
        $this->fetchData();
    }

    public function fetchData(){
        $this->income = 0;
        $this->debt = 0;
        $this->pay = 0;
      
         // รายได้
         $incomes = BillingModel::where('status', 'paid')
         ->whereYear('created_at', $this->selectedYear)
         ->whereMonth('created_at', $this->selectedMonth)
         ->get();

     foreach ($incomes as $income) {
         $this->income += $income->sumAmount() + $income->money_added;
     }
        //หาจํานวนห้องว่าง = จํานวนห้องทั้งหมด - จํานวนลูกค้า
        
        //นับจำนวนลูกค้าทั้งหมด
        $countCustomers = CustomerModel::where('status', 'use')->count();
        //นับจำนวนห้องทั้งหมด
        $countRoom = RoomModel::where('status', 'use')->count();
        //นำจำนวนห้องทั้งหมด - จำนวนลูกค้าทั้งหมด
        $this->roomFee = $countRoom - $countCustomers;

        //ค้างจ่าย
        $waits = BillingModel::where('status', 'wait')
            ->whereYear('created_at', $this->selectedYear)
            ->whereMonth('created_at', $this->selectedMonth)
            ->get();

        foreach ($waits as $wait) {
            $this->debt += $wait->sumAmount() + $wait->money_added;
        }

        //รายจ่าย
        $this->pay = PayLogModel::where('status', 'use')
            ->whereYear('pay_date', $this->selectedYear)
            ->whereMonth('pay_date', $this->selectedMonth)
            ->sum('amount');
 

        //***รายได้แต่ละเดือน****
        //(ข้อมูลที่ถูกนำไปแสดงที่กราฟ)
        for ($i=0; $i <=12 ; $i++) { 
            $billingsInMonth = BillingModel::where('status', 'paid')
            ->whereYear('payed_date', $this->selectedYear)
            ->whereMonth('payed_date', $i)
            ->get();

            $sum = 0;
            foreach($billingsInMonth as $billing){
                $sum += $billing->sumAmount() + $billing->money_added;
            }
            $this->incomeInMonths[] = $sum;
        }
            //*** End รายได้แต่ละเดือน****
        
          // ***random income per 12 months****(Chart.js)
        //   for ($i = 1; $i <= 12; $i++) {
        //     $this->incomeInMonths[$i] = rand(1000, 10000);
        // }

        $incomeTypeDay = rand(1000, 10000);
        $incomeTypeMonth = rand(1000, 10000);

        $this->incomePie = [
            $incomeTypeDay,
            $incomeTypeMonth
        ];
    }
    // ***random income per 12 months****(Chart.js)
    public function loadNewData() {
        return redirect()->to('/dashboard?year=' . $this->selectedYear . '&month=' . $this->selectedMonth);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
     

//  โค๊ดเดิมที่ไม่มีการเลือกปี และเดือน       
//รายได้
//    $incomes = BillingModel::where('status', 'paid')->get();
//         //นำยอดรายรับแต่ละรายการมารวมกัน
//    foreach($incomes as $income){
//     $this->income += $income->sumAmount() + $income->money_added;
//    }
//         //ห้องว่าง
//     //นับจำนวนลูกค้าทั้งหมด
//     $countCustomers = CustomerModel::where('status', 'use')->count();
//     //นับจำนวนห้องทั้งหมด
//     $countRoom = RoomModel::where('status', 'use')->count();
//     //นำจำนวนห้องทั้งหมด - จำนวนลูกค้าทั้งหมด
//     $this->roomFee = $countRoom - $countCustomers;

//         //ค้างจ่าย
//     $waits = BillingModel::where('status', 'wait')->get();

//     foreach($waits as $wait){
//         $this->debt += $wait->sumAmount() + $wait->money_added;
//     }
           
//         //รายจ่าย
//         $this->pay = PayLogModel::where('status', 'use')->sum('amount');  

//          //****รายได้แต่ละเดือน*****
//          for ($i = 1; $i <= 12; $i++) { 
//             $billingsInMonth = BillingModel::where('status', 'paid')
//             ->whereMonth('created_at', $i)->get();

//             $sum = 0;
//             foreach($billingsInMonth as $billing){
//                 $sum += $billing->sumAmount() + $billing->money_added;
//             }
//             $this->incomeInMonths[$i] = $sum;           
//         }

   