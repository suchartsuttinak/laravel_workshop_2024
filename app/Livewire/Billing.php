<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BillingModel;
use App\Models\CustomerModel;
use App\Models\RoomModel;
use App\Models\OrganizationModel;

class Billing extends Component
{
    public $showModal = false;
    public $showModalDelete = false; 
    public $showModalGetMoney = false;
    public $rooms = [];
    public $billings = [];
    public $id;
    public $roomId;
    public $remark;
    public $createdAt;
    public $status;
    public $amountRent;
    public $amountWater;
    public $amountElectric;
    public $amountInternet;
    public $amountFitness;
    public $amountWash;
    public $amountBin;
    public $amountEtc;
    public $customerName;
    public $customerPhone;
    public $listStatus = [
        ['status' => 'wait', 'name' => 'รอชำระเงิน'],
        ['status' => 'paid', 'name' => 'ชำระเงินแล้ว'],
        ['status' => 'next', 'name' => 'ขอค้างจ่าย'],
    ];
    public $sumAmount = 0;
    public $roomForDelete ;
    public $waterUnit = 0;
    public $electricUnit = 0;
    public $waterCostPerUnit = 0;
    public $electricCostPerUnit = 0;
    public $roomNameForEdit = '';
    
     // ชำระค่าใช้จ่าย
     public $roomNameForGetMoney = '';
     public $customerNameForGetMoney = '';
     public $payedDateForGetMoney = '';
     public $moneyAdded = 0;
     public $remarkForGetMoney = '';
     public $sumAmountForGetMoney = 0;

    public function mount(){
        $this->fetchData();
        $this->createdAt = date('Y-m-d');
        $this->status = 'wait';
    }

    public function fetchData(){
    
       //ดึงชื่อที่มีลูกค้ามา
       $customers = CustomerModel::where('status', 'use')->get();
       //สร้างตัวแปรที่เป็น array ว่าง
       $rooms =[];

        //หารายการออกบิล ดึงข้อมูลจากฐานข้อมูลเก็บในตัวแปร billings
        $this->billings = BillingModel::orderBy('id', 'desc')->get();
       //วนลูปชื่อที่มีลูกค้า เพื่อดูว่ามีการออกบิลหรือไม่
        foreach($customers as $customer){
            //ลูกค้าที่ยังไม่ได้ออกบิล
            //สร้างตัวแปร listBilling ให้เป็น false หมายถึงไม่มีข้อมูล
         $listBilling = false;
         
          foreach($this->billings as $billing){
            //ค้นหา id ที่ตรงกัน
            if($billing->room_id == $customer->room_id){
                //ถ้าห้องมีการออกบิลให้หยุดทำงาน (ถ้ามีข้อมูล billings ในฐานข้อมูล) ให้หยุดการทำงาน)
                $listBilling = true;
                break;
            }
          }
  //ถ้าห้องยังไม่มีการออกบิลให้เลือกห้องนี้ได้ แล้วนำค่าไปเก็บไว้ในตัวแปร rooms
  //(ยังไม่มีการบันทึก billings ในฐานข้อมูล)
  //ตอนโหลดหน้าเว็บให้ไปถึงข้อมูลมาจากตาราง customer มาใส่ในตัวแปร rooms
          if(!$listBilling){
            $rooms[] = [
                'id' => $customer->room_id, 
                'name' => $customer->room->name,   
                // 'amount_rent' => $customer->room->price_per_month
            ];
          }
        }
        //ส่งตัวแปร rooms ไปใช้
        $this->rooms = $rooms;
        //เมื่อเปิดมาให้เลือกราคาห้องมาโชว์เลย
        if (count($rooms)> 0){
            $this->roomId = $rooms[0]['id'];
            $this->selectedRoom();
        }  
    }


    public function render()
    {
        return view('livewire.billing');
    }
        public function openModal(){
            $this->showModal = true;
        }

        public function closeModal(){
            $this->showModal = false;
        }

        public function selectedRoom(){
            // เลือกห้อง ให้ไปค้นห้องที่เลือกตาม roomId
            $room = RoomModel::find($this->roomId);
            //ไปค้นหาลูกค้าตาม room_id ให้ตรงกับ roomId ในตาราง rooms
            $customer = CustomerModel::where('room_id', $this->roomId)->first(); 

            $organization = OrganizationModel::first();

            //คำนวนหาค่าน้ำ ค่าไฟ และอื่น ๆ
            //ถ้ามีการใช้น้ำมากกว่า 0 ให้นำค่าน้ำมาใส่ในตัวแปร amountWater
            if ($organization->amount_water > 0) {
                $this->amountWater = $organization->amount_water;
            } else {
                $this->waterCostPerUnit = $organization->amount_water_per_unit;
            }

            if($organization->amount_electric_per_unit > 0){
                $this->electricCostPerUnit = $organization->amount_electric_per_unit;
            }

            if($organization->amount_internet > 0){
                $this->internetCostPerUnit = $organization->amount_internet;
            }
            $this->amountInternet = $organization->amount_internet;
            $this->amountEtc = $organization->amount_etc;
          
          
            // เอาค่า name และ phone จากตาราง customer มาใส่ในตัวแปร
            // เอาค่า amount_rent จากตาราง room มาใส่ในตัวแปร
            $this->customerName = $customer->name;
            $this->customerPhone = $customer->phone;
            $this->amountRent = $room->price_per_month;
          
            // คำนวนรายการ
            $this->computeSumAmount();
        }

        public function computeSumAmount(){

            if($this->waterUnit > 0){
                $this->amountWater = $this->waterUnit * $this->waterCostPerUnit;
            }

            if($this->electricUnit > 0){
                $this->amountElectric = $this->electricUnit * $this->electricCostPerUnit;
            }
            // หายอดรวมทั้งหมด
            $this->sumAmount = $this->amountRent + $this->amountWater 
            + $this->amountElectric + $this->amountInternet 
            + $this->amountFitness + $this->amountWash + $this->amountBin 
            + $this->amountEtc;  
              
        }

        public function save(){
            $billing = new BillingModel();

            if ($this->id != null) {
                $billing = BillingModel::find($this->id);
            }
        $billing->room_id = $this->roomId;
        $billing->created_at = $this->createdAt;
        $billing->status = $this->status;
        $billing->remark = $this->remark ?? '';
        $billing->amount_rent = $this->amountRent ?? 0;
        $billing->amount_water = $this->amountWater ?? 0;
        $billing->amount_electric = $this->amountElectric ?? 0;
        $billing->amount_internet = $this->amountInternet ?? 0;
        $billing->amount_fitness = $this->amountFitness ?? 0;
        $billing->amount_wash = $this->amountWash ?? 0;
        $billing->amount_bin = $this->amountBin ?? 0;
        $billing->amount_etc = $this->amountEtc ?? 0;
        $billing->save();

        $this->showModel = false;
        $this->fetchData();

        $this->id = null;
        $this->electricUnit = 0;
        $this->waterUnit = 0;
        $this->electricCostPerUnit = 0;
        $this->waterCostPerUnit = 0;
    }      
    public function openModalEdit($id){
        $this->showModal = true;
        $this->billing = BillingModel::find($id);
        $this->id = $id;
        $this->roomId = $this->billing->room_id;

        $this->selectedRoom();
        $this->amountWater = $this->billing->amount_water;
        $this->amountElectric = $this->billing->amount_electric;

        $this->amountFitness = $this->billing->amount_fitness;
        $this->amountWash = $this->billing->amount_wash;
        $this->amountBin = $this->billing->amount_bin;
        $this->amountEtc = $this->billing->amount_etc;

        $this->roomNameForEdit = $this->billing->room->name;
        //หารเพื่อหาค่า unit
        $this->waterUnit = $this->amountWater / $this->waterCostPerUnit;  //หรือ $organization->amount_water_per_unit
        $this->electricUnit = $this->amountElectric / $this->electricCostPerUnit; //หรือ $organization->amount_electric_per_unit

        $this->computeSumAmount();
    } 
    public function closeModalEdit(){
        $this->showModel = false;
    }
    public function openModalDelete($id, $name){
        $this->showModalDelete = true;
        $this->id = $id;
        $this->roomForDelete = $name;  
          } 
    public function closeModalDelete(){
        $this->showModalDelete = false;
    }
    public function deleteBilling(){
        $billing = BillingModel::find($this->id);
        $billing->delete();
        
        $this->fetchData();
        $this->showModalDelete = false;    
    }

    public function openModalGetMoney($id){
        $billing = BillingModel::find($id);
        $this->showModalGetMoney = true;
        $this->id = $id;
        $this->roomForGetMoney = $billing->room->name;
        $this->customerNameForGetMoney = $billing->getCustomer()->name;
        $this->sumAmountForGetMoney = $billing->sumAmount();
        $this->payedDateForGetMoney = date('Y-m-d');
        $this->moneyAdded = 0;
        $this->remarkForGetMoney = '';
        
    }
    public function closeModalGetMoney(){
        $this->showModalGetMoney = false;
        $this->id = null;
        $this->moneyAdded = 0;
        $this->remarkForGetMoney = '';
        $this->sumAmountForGetMoney = 0;
        $this->payedDateForGetMoney = '';
        $this->customerNameForGetMoney = '';
        $this->roomNameForGetMoney = '';
    }
    

    public function printBilling($billingId){
        return redirect()->to('print-billing/' . $billingId);  
    }

 }
    
  



 





   