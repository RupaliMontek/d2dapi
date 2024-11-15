
<div class="container px-5 my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 rounded-3 shadow-lg">
        <div class="card-body p-4">
          <div class="text-center">
            <div class="h1 fw-light">IEC Resgiter Form</div>
          </div>
          <!-- * * * * * * * * * * * * * *
          // * * SB Forms Contact Form * *
          // * * * * * * * * * * * * * * *

          // This form is pre-integrated with SB Forms.
          // To make this form functional, sign up at
          // https://startbootstrap.com/solution/contact-forms
          // to get an API token!
          -->
          <form name="iec_register_form" id="iec_register_form" method="POST" action="<?php echo base_url();?>Admin/register_iec_user"> 
            <!-- Name Input -->
            <div class="form-floating mb-3">
              <input class="form-control" id="Enter First Name" id="first_name" name="first_name"  type="text" placeholder="Name" />
              <label for="name">Name</label>
            </div>

            <!-- Email Input -->
            <div class="form-floating mb-3">
              <input class="form-control" name="last_name" id="last_name" type="text" placeholder="Email Last Name"  />
              <label for="emailAddress">Last Name</label>
            </div>
            
            <div class="form-floating mb-3">
              <input class="form-control" name="iec_email" id="iec_email" type="text" placeholder="Email Last Name"  />
              <label for="emailAddress">Email</label>
            </div>

            <!-- Message Input -->
            <div class="form-floating mb-3">
              <input class="form-control" id="iec_no" name="iec_no" type="text" placeholder="Enter IEC No"  >
              <label >IEC No</label>
            </div>
            
            <div class="form-floating mb-3">
              <input class="form-control" id="mobile no" name="mobile no" type="text" placeholder="Enter IEC No"  >
              <label >Mobile No</label>
            </div>

          
            <!-- Submit error message -->
          

            <!-- Submit button -->
            <div class="d-grid">
              <button class="btn btn-primary btn-lg" id="submitButton" type="submit">Submit</button>
            </div>
          </form>
          <!-- End of contact form -->

        </div>
      </div>
    </div>
  </div>
</div>
