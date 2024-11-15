<script src="<?php echo base_url(); ?>frontend/js/forms_validation.js"></script>
<div class="container-fluid g-0 forfooter">
<div class="container">
<footer class="footer">
  <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Licensed by <a href="javascript:void(0)">LEO TECH EXIM SOLUTIONS LLP</a>.</span>
  <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2023-2024. All rights reserved.</span>
  <span class="text-muted d-block text-center text-sm-left d-sm-inline-block txt-rgt">Developed &amp; maintain by <a href="http://www.montekservices.com/" target="_blank">MONTEK TECH SERVICES PVT LTD</a>.</span>
</footer>
</div>
</div>

  </body>
</html>
<script>
$( document ).ready(function() {
    alert("Hello Jaywant");
    $("#import").css({'display': 'none',});
    $("#export").css({'display': 'none',});
});

function select_sheet_type(type){
    if(type==1){
     $("#export").css({'display': 'none',});
     $("#import").css({'display': '',});
    }
    else if(type==2){
        $("#import").css({'display': 'none',});
        $("#export").css({'display': '',});
    }
    else{
        $("#import").css({'display': 'none',});
        $("#export").css({'display': 'none',});
    }
    

}

 function CheckedAll_import(){
     if ($('#ChkAll_import').is(':checked')) {
          $('#all_import input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#all_import input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
   
    function CheckedAllChkboe_summary(){
     if ($('#AllChkboe_summary').is(':checked')) {
         $('#Chkboe_summary input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#Chkboe_summary input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
function CheckedAllChkboe_entry(){
    
     if ($('#AllChkboe_entry').is(':checked')) {
         $('#Chkboe_entry input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#Chkboe_entry input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
   function CheckedAllBond_Details(){
 
     if ($('#AllBond_Details').is(':checked')) {
         $('#ChkBond_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#ChkBond_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
      function CheckedAllContainer_Details(){
 
     if ($('#AllContainer_Details').is(':checked')) {
         $('#ChkContainer_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#ChkContainer_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
        function CheckedAllManifest_Details(){
 
     if ($('#AllManifest_Details').is(':checked')) {
         $('#ChkManifest_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#ChkManifest_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
    function CheckedAllLicense_Details(){
 
     if ($('#AllLicense_Details').is(':checked')) {
         $('#ChkLicense_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#ChkLicense_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
   
function CheckedAllPayment_Details(){
 
     if ($('#AllPayment_Details').is(':checked')) {
         $('#ChkPayment_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#ChkPayment_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
   function CheckedAllEquipment_Details(){
 
     if ($('#AllEquipment_Details').is(':checked')) {
         $('#chkEquipment_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkEquipment_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
   function CheckedAllChkSHB_Summary(){
 
     if ($('#AllChkSHB_Summary').is(':checked')) {
         $('#chkSHB_Summary input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkSHB_Summary input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
    function CheckedAllChallan_Details(){
 
     if ($('#AllChallan_Details').is(':checked')) {
         $('#chkChallan_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkChallan_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }


    function CheckedAllJobbing_Details(){
 
     if ($('#AllJobbing_Details').is(':checked')) {
         $('#chkJobbing_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkJobbing_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
       function CheckedAllDFIA_Licence_Details(){
 
     if ($('#AllDFIA_Licence_Details').is(':checked')) {
         $('#chkDFIA_Licence_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkDFIA_Licence_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
          function CheckedAllDrawback_Details(){
 
     if ($('#AllDrawback_Details').is(':checked')) {
         $('#chkDrawback_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkDrawback_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
      
          function CheckedAllThird_Party_Details(){
 
     if ($('#AllThird_Party_Details').is(':checked')) {
         $('#chkThird_Party_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkThird_Party_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
         
          function CheckedAllItem_Manufacturer(){
 
     if ($('#AllItem_Manufacturer').is(':checked')) {
         $('#chkItem_Manufacturer input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkItem_Manufacturer input[type=checkbox]:checked').removeAttr('checked');
     }
   }
   
   
     
          function CheckedAllRodtep_Details(){
 
     if ($('#AllRodtep_Details').is(':checked')) {
         $('#chkRodtep_Details input[type=checkbox]').attr('checked', 'checked');           
     }
     else {
          $('#chkRodtep_Details input[type=checkbox]:checked').removeAttr('checked');
     }
   }


   
     
   
function myFunction1(type){
    var base_url = "<?php echo base_url(); ?>";
    $.ajax({
    type:"POST",
    data:{type},
    dataType: 'json',
    url:base_url+"Admin/get_worksheet_name_by_type",
    success:function(response) {
    $('#worksheet option').remove();
    for(var i = 0; i<response.length; i++){
    var id = response[i]; 
    $(".multiselect-dropdown").append("</br><option value="+response[i]['tbl_name']+">"+response[i]['tbl_sheet_name'] +"</option></br>");
   
    }
    }
  }); };
  
  function myFunction2(tbl_name){
    var base_url = "<?php echo base_url(); ?>";
    $.ajax({
    type:"POST",
    data:{tbl_name},
    dataType: 'json',
    url:base_url+"Admin/get_worksheet_fieds",
    success:function(response) {
    $('#sheets_fields option').remove();
    for(var i = 0; i<response.length; i++){
    var id = response[i]; 
    $("#sheets_fields").append("</br><option value="+response[i]['Field']+">"+response[i]['Field'] +"</option></br>");
   
    }
    }
  }); };
  
  </script>