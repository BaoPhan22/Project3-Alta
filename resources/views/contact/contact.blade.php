 <!-- Modal -->
 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 Gửi liên hệ thành công.
                 <br>
                 Vui lòng kiên nhẫn đợi phản hồi từ chúng tôi, bạn nhé!
             </div>
         </div>
     </div>
 </div>
 @extends('layouts.master')
 @section('title', 'Liên hệ')
 @section('content')
     <style>
         .contact-info-item {
             background-color: #fff6d4;
             border-radius: 10px;
             border: 3px dashed #ffb489;
             min-height: 100px;
             padding: 10px;
         }

         .contact-form-item {
             background-color: #fff6d4;
             border-radius: 10px;
             border: 3px dashed #ffb489;
             min-height: 400px;
             padding: 10px;
         }
     </style>

     <img src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688372713/Alex_AR_Lay_Do_shadow_1_fv9ypm.svg"
         style="position: absolute; bottom: 20%; left:0; z-index: 100; width: 12%">
     <p class="page-title tilte-custom">Liên hệ</p>
     <div class="d-flex justify-content-center align-items-center content-container">
         <div class="row w-75 mt-5">
             <div class="row">
                 <div class="col-7 form-item">
                     <form action="#" class="contact-form-item">
                         <label class="form-label">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Suscipit nam
                             non
                             veritatis illo laborum</label>
                         <div class="mb-3 row">
                             <div class="col-4"><input type="text" class="form-control" placeholder="Tên"></div>
                             <div class="col-8"><input type="text" class="form-control" placeholder="Email"></div>
                         </div>
                         <div class="mb-3 row">
                             <div class="col-4"><input type="text" class="form-control" placeholder="Số điện thoại">
                             </div>
                             <div class="col-8"><input type="text" class="form-control" placeholder="Địa chỉ"></div>
                         </div>
                         <div class="mb-3 row">
                             <div class="col-12">
                                 <textarea name="" class="form-control" placeholder="Nhập lời nhắn" rows="4"></textarea>
                             </div>
                         </div>
                         <div class="d-flex align-items-center justify-content-center w-50 m-auto">
                             {{-- <button class="btn btn-primary mt-3" type="submit">Gửi liên hệ</button> --}}
                             <!-- Button trigger modal -->
                             <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                 data-bs-target="#exampleModal">
                                 Gửi liên hệ
                             </button>
                         </div>
                     </form>
                 </div>
                 <div class="col-1"></div>
                 <div class="col-4 flex-column d-flex justify-content-between">
                     <div class="row mb-4 infomation-item">
                         <div class="contact-info-item row m-auto">
                             <div class="col-3">
                                 <img
                                     src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688373490/adress_1_f196la.svg">
                             </div>
                             <div class="col-9">
                                 <p class="mb-0 fw-bold fs-5">Địa chỉ:</p>
                                 <p class="fs-6">86/33 Âu Cơ, Phường 9, Quận Tân Bình, TP. Hồ Chí Minh</p>
                             </div>
                         </div>
                     </div>
                     <div class="row mb-4 infomation-item">
                         <div class="contact-info-item row m-auto">
                             <div class="col-3">
                                 <img
                                     src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688373490/mail-inbox-app_1_d2mxi0.svg">
                             </div>

                             <div class="col-9">
                                 <p class="mb-0 fw-bold fs-5">Email:</p>
                                 <p class="fs-6">investigate@your-site.com</p>
                             </div>
                         </div>
                     </div>
                     <div class="row mb-3 infomation-item">
                         <div class="contact-info-item row m-auto">
                             <div class="col-3">
                                 <img
                                     src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688373490/telephone_2_quuhc9.svg">
                             </div>
                             <div class="col-9">
                                 <p class="mb-0 fw-bold fs-5">Điện thoại:</p>
                                 <p class="fs-6">+84 145 689 798</p>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

         </div>
     </div>


 @endsection
