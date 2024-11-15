var base_url = "https://d2d.lucrative-esystem.com/";
     jQuery.validator.addMethod("emailtest", function(value, element) {
        return this.optional(element) || /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$/i.test(value);
    }, "Please enter a valid Email ID");
$("#login_form").validate(
    {
      errorElement: "span", 

      rules: 
      {
        email: 
        {
          required:true,
          emailtest:true,
          remote: {
                url: base_url+"login/check_email_exists_registration",
                type: "post",
                data: {
                    email: function() {
                        return $("#email").val();
                    },
                }
            }
        },
        password: 
            {
                required:true,
            },
      },
      

      messages: 
      { 
            
        email: 
        {
          required:"Required Email Address",
          remote:"Please Enter Valid Email Address This Id Not Register",
        },
         password: 
            {  
                required:"Required Password"
            },
      },
    });