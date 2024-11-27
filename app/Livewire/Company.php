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
        $organization = OrganizationModel::first();
        $this->name = $organization->name ?? '';
        $this->address = $organization->address ?? '';
        $this->phone = $organization->phone ?? '';
        $this->tax_code = $organization->tax_code ?? '';

        //link picture
        if ($organization->logo) {
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

        if ($this->logo) {
            $logo = $this->logo->store('organizations', 'public');
        }
        # Add Picture
        if (OrganizationModel::count() == 0) {
            $organization = new OrganizationModel();
        } else {
            # remove old logo
            $organization = OrganizationModel::first();

            if ($organization->logo) {
                if ($logo != '') {
                    $Storage = Storage::disk('public');

                    if ($Storage->exists($organization->logo)) {
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