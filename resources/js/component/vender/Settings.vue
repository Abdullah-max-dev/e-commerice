<template>



    <div id="layoutSidenav_content">
        <main>
            <div class="container mt-4">
                <form action="">
                    <div class='ml-2'><h4>Settings</h4></div>
                    <div class='card p-3 mt-3'>
                        <div><h5>Basic info</h5></div>
                        <div class="row mt-3">
                            <div class="col-xl-8 col md-8 ">
                                <div class = 'row'>
                                    <div class='col-xl-12 col-md-12 mb-3'>
                                        <label for="first" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="first" placeholder='identification number'>
                                    </div>
                                    <div class='col-xl-12 col-md-12 mb-3'>
                                        <label for="first" class="form-label">Address</label>
                                        <textarea type="text" class="form-control" id="first" placeholder='identification number'></textarea>
                                    </div>
                                    <div class='col-xl- col-md-6 mb-3'>
                                        <label for="first" class="form-label">Phone No</label>
                                        <input type="text" class="form-control" id="first" placeholder='Bussiness name'>
                                    </div>
                                    <div class='col-xl-6 col-md-6 mb-3 '>
                                        <label for="last" class="form-label">email</label>
                                        <input type="text" class="form-control" id="last" placeholder='Doe'>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class='card p-3 mt-3'>
                        <div><h5>Bussiness information</h5></div>
                        <div class="row mt-3">
                            <div class="col-xl-12 col md-12  ">
                                <div class = 'row'>
                                    <div class='col-xl- col-md-6 mb-3'>
                                        <select class="form-control form-control-lg mb-3 " aria-label="Default select example">
                                            <option selected>Select Bussiness type</option>
                                            <option value="1">Sole propietor</option>
                                            <option value="2">Partnership</option>
                                            <option value="3">Corporation</option>

                                        </select>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-12 mb-3'>
                                        <label for="exampleInputEmail1" class="form-label">GST No.</label>
                                    <input class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder='Abc123xyz'></input>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-6 mb-3'>
                                        <label for="phone" class="form-label">Bussiness catagory</label>
                                        <input type="Text" class="form-control form-control-lg" id="exampleFormControlInput1" placeholder="type">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class='card p-3 mt-3'>
                        <div><h5>Payment information</h5></div>
                        <div class="row mt-3">
                            <div class="col-xl-12 col md-12  ">

                                <div class='row'>
                                    <div class='col-12 mb-3'>
                                        <label for="exampleInputEmail1" class="form-label">Bank account no.</label>
                                    <input class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder='account no' ></input>
                                    </div>


                                    <div class='col-xl- col-md-6 mb-3'>
                                        <label for="">Select payment method</label>
                                        <select class="form-control form-control-lg mb-3 " aria-label="Default select example">
                                            <option value="1">easypaisa or jazzcash</option>
                                            <option value="2">Bank transfer</option>
                                            <option value="3">E-wallet</option>

                                        </select>
                                    </div>
                                </div>

                            </div>


                        </div>

                        <button  class='btn btn-primary btn-md  my-5 px-4 py-2'>Save</button>
                    </div>

                </form>

            </div>


        </main>
    </div>


</template>
