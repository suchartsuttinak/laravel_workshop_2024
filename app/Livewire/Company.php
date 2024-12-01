<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OrganizationModel;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;





class Company extends Component
{
    use WithFileUploads;

    public $name, $address, $phone, $tax_code, $logo;
    public $logoUrl;
    public $flashMessage;

    public function mount()
    {
        $this->fetchData();
    }

    //fetchData
    public function fetchData()
    {
        //ไปดึงข้อมูลมา แล้วมาใส่ในตัวแปร แต่ละตัว
        //ถ้าไม่มีข้อมูลในฐานข้อมูล ให้ใส่ค่าเริ่มต้น เป็น null
        $organization = OrganizationModel::first();
        $this->name = $organization->name ?? '';
        $this->address = $organization->address ?? '';
        $this->phone = $organization->phone ?? '';
        $this->tax_code = $organization->tax_code ?? '';

        //ถ้ามี logo ไปอ่านค่าจาก storage แล้วมาใส่ในตัวแปร logoUrl
        if (isset($organization->logo)) {
        $this->logoUrl = Storage::disk('public')->url($organization->logo);
        }
    }
    //fetchData
    public function render()
    {
        return view('livewire.company');
    }


    public function save()
    {
        # การจัดการรูปภาพ 
        $logo = '';

        //ถ้ามีการอัพโหลดรูปภาพ ให้ไปเก็บภาพไว้ใน public
        if ($this->logo) {
            $logo = $this->logo->store('organizations', 'public');
        }
        // ถ้าไม่เคยมีข้อมูลในฐานข้อมูล
        if (OrganizationModel::count() == 0) {
            //สร้าง object ใหม่ขึ้นมา
            $organization = new OrganizationModel();
        } else {
            //ถ้ามีข้อมูลในฐานข้อมูล ให้ไปดึงข้อมูลมา
            $organization = OrganizationModel::first();

            // ถ้ามีการแนบ logo มา แล้วมี logo อยู่ในฐานข้อมูล 
            if ($organization->logo) {
                if ($logo != '') {
                    //ไปอ่านไฟล์ออกมา
                    $Storage = Storage::disk('public');
                    //แล้วเช็กว่าถ้ามีภาพอยู่ในฐานข้อมูล 
                    if ($Storage->exists($organization->logo)) {
                        //แล้วลบภาพเดิมทิ้ง
                        $Storage->delete($organization->logo);
                    }
                } else {
                    $logo = $organization->logo; // old logo
                }
            }
        }
            # การจัดการรูปภาพ 
           
            # การเพิ่มข้อมูลลงฐานข้อมูล
        $organization->name = $this->name;
        $organization->address = $this->address;
        $organization->phone = $this->phone;
        $organization->tax_code = $this->tax_code;
        $organization->logo = $logo;
        $organization->save();

        $this->flashMessage = 'บันทึกสำเร็จ';
        $this->fetchData();
    }
}