<div>
    <div class="content-header">
        <div class="flex justify-between">
            <div>ข้อมูลห้องพัก</div>
            <div>ทั้งหมด<strong>{{ $rooms->count() }}</strong>ห้อง</div>
        </div>
    </div>
    <div class="content-body">
        <button class="btn-info" wire:click="openModal">
            <i class="fa-solid fa-plus mr-2"></i>
            เพิ่มห้องพัก
        </button>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th class="text-left">ห้องพัก</th>
                    <th class="text-left" width="150px">ค่าเช่าต่อวัน</th>
                    <th class="text-left" width="150px">ค่าเช่าต่อเดือน</th>
                    <th width="130px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rooms as $room)
                    <tr>
                        <td class="text-left">{{ $room->name }}</td>
                        <td class="text-left">{{ number_format($room->price_per_day, 2) }}</td>
                        <td class="text-left">{{ number_format($room->price_per_month, 2) }}</td>
                        <td class="text-center">
                            <button class="btn-edit mr-2" wire:click="openModalEdit({{ $room->id }})">
                                <i class="fa-pencil mr-2"></i>
                            </button>
                            <button class="btn-delete" wire:click="openModalDelete({{ $room->id }})">
                                <i class="fa-times"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
        </table>
    </div>

    <x-modal wire:model="showModal" title="ห้องพัก" maxWidth="xl">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div>
            <h1 class="text-xl text-red-500">สร้างห้องพักแบบจำนวนมากในครั้งเดียว</h1>
        </div>
        <div class="flex gap-5 mt-3 p-3">
            <div class="w-1/2">
                <label>จากหมายเลข</label>
                <input type="text" class="form-control" wire:model="from_number">
            </div>
            <div class="w-1/2">
                <label>ถึงหมายเลข</label>
                <input type="text" class="form-control" wire:model="to_number">
            </div>
            <div class="w-1/2">
                <label">ค่าเช่าต่อวัน</label>
                    <input type="text" class="form-control" wire:model="price_per_day">
            </div>
            <div class="w-1/2">
                <label">ค่าเช่าต่อเดือน</label>
                    <input type="text" class="form-control" wire:model="price_per_month">
            </div>
        </div>

        <div class="mt-5 text-center pb-5">
            <button class="btn-success mr-2" wire:click = "createRoom">
                <i class="fa-solid fa-check mr-2"></i>
                สร้างห้องพัก
            </button>
            <button class="btn-danger" wire:click="showModal = false">
                <i class="fa-solid fa-times mr-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>
    <!-- แก้ไขห้องพัก-->
    <x-modal wire:model="showModalEdit" title="แก้ไขห้องพัก" maxWidth="xl">
        <div>ห้องพัก</div>
        <input type="text" class="form-control" wire:model="name">

        <div class="mt-3">ราคาต่อวัน</div>
        <input type="text" class="form-control" type="number" wire:model="price_day">

        <div class="mt-3">ราคาต่อเดือน</div>
        <input type="text" class="form-control" type="number" wire:model="price_month">

        <div class="mt-5 text-center pb-5">
            <button class="btn-success mr-2" wire:click="updateRoom">
                <i class="fa-solid fa-check mr-2"></i>
                บันทึก
            </button>
            <button class="btn-danger" wire:click="showModalEdit = false">
                <i class="fa-solid fa-times mr-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>

    <!-- ลบห้องพัก-->
    <x-modal-confirm wire:model="showModalDelete" title="ลบห้องพัก"
        text="คุณต้องการลบห้องพัก {{ $name }} ใช่หรือไม่" clickConfirm="deleteRoom"
        clickCancel="showModalDelete = false" />

</div>
