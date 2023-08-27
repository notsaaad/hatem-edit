<?php
/*
 * Plugin Name: HatemEdit
 * Description:  Handle the custom funcation for the Sawyan Site.
 */


//===================================== Start Gloable Methods =============================

/******************************* Start Add_actions ******************************/

add_action( 'wp_footer', 'Hatem_is_affi' );        //Argun: wp_user_id               ,Return bool

add_action( 'wp_footer', 'Hatem_get_affi' );        //Argun:wp_user_id               ,Return Aff_id

add_action ( 'wp_footer','Hatem_is_aff_lvl_one');       //Argun:Aff_ID               ,Return Bool

add_action( 'Wp_footer', 'Hatem_is_aff_lvl_two');     //Argn:Aff_ID                  ,Return Bool

add_filter( 'wp_footer', 'Hatem_get_all_Aff_with_type_and_numbers'); // no Argn      ,Return Tabel

add_filter('wp_footer', 'Hatem_get_aff_phone' ); // userID ,Return Phone;
/******************************* End Add_actions ******************************/


function Hatem_is_affi($user_ID){
  global $wpdb;

  $table_name =  'wp40_uap_affiliates'; //$wpdb->prefix .
  $results 	= $wpdb->get_results("SELECT * FROM $table_name" );
  $check = false;
  $values = json_decode(json_encode($results), true);
  $check = false;
  foreach($values as $value){
    if ($value['uid'] == $user_ID){
      $check = true;

    }
  }

  return $check;
}


//--------------------------------------------------------
function Hatem_get_affi($user_ID){
  global $wpdb;

  $table_name =  'wp40_uap_affiliates'; //$wpdb->prefix .
  $results 	= $wpdb->get_results("SELECT * FROM $table_name" );

  $values = json_decode(json_encode($results), true);
  $check = false;
  foreach($values as $value){
    if ($value['uid'] == $user_ID){
      return $value['id'];
    }
  }

}
//--------------------------------------------------------
function Hatem_is_aff_lvl_one($user_ID){
  global $wpdb;

    $table_name =  'wp40_uap_mlm_relations'; //$wpdb->prefix .
    $results 	= $wpdb->get_results("SELECT * FROM $table_name" );

    $values = json_decode(json_encode($results), true);
    $check = false;
    foreach($values as $value){
      if ($value['parent_affiliate_id'] == $user_ID){
        $check = true;
        return $check;
      }
    }
    return $check;
}

//--------------------------------------------------------

function Hatem_is_aff_lvl_two($user_ID){
  global $wpdb;

    $table_name =  'wp40_uap_mlm_relations'; //$wpdb->prefix .
    $results 	= $wpdb->get_results("SELECT * FROM $table_name" );

    $values = json_decode(json_encode($results), true);
    $check = false;
    foreach($values as $value){
      if ($value['affiliate_id'] == $user_ID){
        $check = true;
        return $check;
      }
    }
    return $check;
}
//--------------------------------------------------------------
function Hatem_get_aff_phone($userID){
  if($userID==""){
    return;
  }
  global $wpdb;
  $sql_user_id = "user_id";
  $sql_user_meta = " meta_key";
  $sql_user_phone = "phone";
  $table_user_meta ="wp40_usermeta";
  $results = $wpdb->get_results("SELECT 'phone' FROM $table_user_meta WHERE $sql_user_id = $userID ");

  return $results[0]->meta_value;

}

//--------------------------------------------------------------
function Hatem_get_all_Aff_with_type_and_numbers(){
  global $wpdb;

  $tabel_aff_name ='wp40_uap_affiliates';
  $results_aff = $wpdb->get_results("SELECT * FROM $tabel_aff_name");
  foreach ($results_aff as $result) {
    $tabel_users_name = "wp40_users";
    $results_name = $wpdb->get_results("SELECT * FROM $tabel_users_name WHERE (ID=$result->uid)");
    // echo '<br>' . $result->id .' => '. $results_name[0]->user_login .' => '. Hatem_get_aff_phone( $result->uid);
  }
  // print_r(Hatem_get_aff_phone($results_aff[0]->uid));


}


//===================================== End Gloable Methods =============================


add_action( 'wp_footer', 'HatemAffiliateChange' );


function HatemAffiliateChange(){

  $user_id = wp_get_current_user() -> ID;

  $isAff = Hatem_is_affi($user_id);

  $AffID="";
  $AFFLvl ="";
  $AFFLv2 ="";
  if ($isAff){
    $AffID = Hatem_get_affi($user_id); //Get Aff ID
    $AFFLvl = Hatem_is_aff_lvl_one($AffID); //True || False
    $AFFLv2 = Hatem_is_aff_lvl_two($AffID); // True || False
  }



  ?>
  <script>
    // Append Aff Link to Lvl One and lvl 2 and Let Log Page Show Only to Thoes
    (function($){
      let href = window.location.href;
      let hrefUserID = '';
      let UserID = `<?php echo $user_id ?>`;
      let AffID = `<?php echo $AffID ?>`;
      let IS_AFFLvl_1 = `<?php echo $AFFLvl ?>`;
      let IS_AFFLvl_2 = `<?php echo $AFFLv2 ?>`;
      let IsAFF = `<?php echo $isAff ?>`;

      let paramString = href.split('?')[1];
      let queryString = new URLSearchParams(paramString);
      // console.log(queryString);

      for (let pair of queryString.entries()) {

        hrefUserID = pair[1];
}


      if (IsAFF){

        // $('#header #logo').after(`<li class="menu-item menu-item-type-post_type menu-item-object-page  menu-item-design-default" style="list-style: none;" "Hatem-header-aff-FI-Links"><a style="color:#75c37d;" href="https://sawyancom.com/my-account-2/?uap_aff_subtab=affiliate_link">Fi Links </a></li>`);
        let LinkMain = $('.uap-account-affiliatelinks-tab2');
        let NewLink = LinkMain.clone();
        let NewCustomerLink = LinkMain.clone();
            //Append New 2 Links At mlm lvl One
        NewLink.html(`<span class="Hatem-aff-g2">لتسجيل مسوق جديد New Fi Link :</span> <span class=" uap-special-label"><span class="Hatem-AFF-new-Link">https://sawyancom.com/aff-signup/Sale/${AffID}</span></span>`);
        NewCustomerLink.html(`<span > لتسجيل مشتري جديد New Customer Link:</span> <span class=" uap-special-label"><span class="Hatem-AFF-new-Link">https://sawyancom.com/my-account/?Sale=${AffID}</span></span>`);
        $('.uap-col-xs-8').append(NewLink);
        $('.uap-col-xs-8').append(NewCustomerLink);
      }//els{
      //   //Append New 1 Links At mlm lvl two
      //   let LinkMain = $('.uap-account-affiliatelinks-tab2');
      //   let NewCustomerLink = LinkMain.clone();
      //   NewCustomerLink.html(`<span> لتسجيل مشتري جديد New Customer Link:</span> <span class=" uap-special-label"><span class="Hatem-AFF-new-Link">https://sawyancom.com/my-account/?Sale=${AffID}</span></span>`);
      //   $('.uap-col-xs-8').append(NewCustomerLink);
      // }

      if (IS_AFFLvl_2){
        $('.Hatem-aff-g2').parent().remove();
      }


      let Aff_lvlOne_ID;

      if(IsAFF){
        let NewLi = $('.dashboard-links li').first();
        NewLi.addClass("Hatem-FI");
        // NewLi.html('<a href="https://sawyancom.com/my-account-2/">عمولاتي FI</a>');
        // NewLi.removeClass('active');
        $('.woocommerce-MyAccount-navigation-link.woocommerce-MyAccount-navigation-link--uap').css('display','list-item');
        $('.woocommerce-MyAccount-navigation-link.woocommerce-MyAccount-navigation-link--uap a').text("عمولاتي FI");
        $('.woocommerce-MyAccount-navigation-link.woocommerce-MyAccount-navigation-link--uap a').attr('href','https://sawyancom.com/my-account-2/');
        // $('#my-account-nav').append(NewLi);

        Aff_lvlOne_ID = AffID;

      }

      function Hatem_is_Aff_lvl_one(LastIndex){

        <?php

          global $wpdb;
          $Users_ID_Aff =array();

          $table_name =  'wp40_uap_mlm_relations'; //$wpdb->prefix .
          $results 	= $wpdb->get_results("SELECT * FROM $table_name" );

          $values = json_decode(json_encode($results), true);

          foreach($values as $value){
            array_push($Users_ID_Aff,$value['parent_affiliate_id']);
          }

          ?>
          let User_counts = `<?php echo count($Users_ID_Aff)  ?>`;
          var arr_user_aff_lvl_one = [];




            arr_user_aff_lvl_one.push(`<?php  foreach($Users_ID_Aff as $ID)echo $ID ?>`);

            return arr_user_aff_lvl_one.includes(LastIndex);
      }
      let Check_come_from_aff_lvl1 = Hatem_is_Aff_lvl_one(hrefUserID);




      if ( (IsAFF=='') && (Check_come_from_aff_lvl1 != true)){
        $('.uap-login-form-wrap').html(`<div class="Hatem-aff-no-login"><h2>عذرا ليس لديك صلاحية الوصول </h2></div>`);
      }


      // Append At Profile wordpress Aff_lvl_one DashBord



      // Hide SignIn form from c at lvl 1 or 2
      <?php


      // Initialize URL to the variable
      // $url = $_SERVER['QUERY_STRING'];
      $url = $_SERVER['REQUEST_URI'];


      // Use parse_url() function to parse the URL
      // and return an associative array which
      // contains its various components
      $url_components = parse_url($url);
      $LinkPram = '1000';
      // Use parse_str() function to parse the
      // string passed via URL
      $ChechPram = false;
      if (isset($url_components['query'])){
        $ChechPram = true;
      }
      if ($ChechPram == 1){
      parse_str($url_components['query'], $params);




      // Display result
      $LinkPram = $params['Sale'];

      }

      ?>
      let CheckPram = false ;
      if (`<?php echo $ChechPram == 1 ?>`){
        CheckPram = true;
      }

      if (CheckPram){

        let Test = <?php
            global $wpdb;

            $table_name =  'wp40_uap_affiliates'; //$wpdb->prefix .
            $results 	= $wpdb->get_results("SELECT * FROM $table_name" );
            $HE_IS_AFF = false;
            $values = json_decode(json_encode($results), true);
            $HE_IS_AFF = false;
            foreach($values as $value){
              if ($value['id'] == $LinkPram ){
                $HE_IS_AFF = true;

              }
            }
            if ($HE_IS_AFF == 1){
              echo $HE_IS_AFF;
            }else{
              echo '0';
            }

          // ?>;

          if (Test == 1){

            $('#customer_login .col-1').remove();
          }
      }

    })(jQuery);


  </script>


  <?php

}







    //Append to manual  scraping price input

add_action('wp_footer','Hatem_manual_Scraping_Price' );

function Hatem_manual_Scraping_Price(){

  $exchangrate 		= get_option('product_scrapper_rate') ? get_option('product_scrapper_rate') : '0.051939';
  $fees = sam_acf_get_field( 'sam_scraping' , 'options' );
  $bankperc 	= 0;
  if ( ! empty( $fees ) && is_array( $fees ) ) {

    foreach ( $fees as $fee ) {

      $name  = $fee['cost_name']     ?? '';
      $price = $fee['cost_price']    ?? '';
      $status = $fee['cost_status']   ?? '';
      if ( $status )
        $bankperc += $price;


    }
  }

  ?>

  <script>


    (function($){


      $('#product-9640 .price-wrapper').after(`<span style="
    width: 100%;
    font-weight: 400;
    line-height: 20px;
    margin: 0 0 8px 0;
    color:#424242;
    font-weight: normal;
    font-size: 14px;
    ">ادخل سعر المنتج بالليرة التركية</span><br>  <input type="number" id="Hatem-manual-scrap-clac" placeholder="ادخل سعر المنتج بالليرة التركية و سيظهر تلقائيا في خانه السعر بالدولار" style="border:1px solid #c6d0e9; border-radius: 6px;">`);
      $('#Hatem-manual-scrap-clac').on("focusout",function(){
        var LastPrice = $( $(this) ).val();
        let exchangrate = `<?php echo $exchangrate; ?>`;
        ratepr = LastPrice * exchangrate;

        var newrateptr = ratepr + (ratepr * ( parseInt(`<?php echo $bankperc ; ?>`) / 100));

        // ratepr = ratepr  *( parseInt(`<?php echo $bankperc ; ?>`) /100);

        // $('#field_text-5329502930').val(ratepr.toFixed(2));
        $('#field_text-5329502930').attr("value",newrateptr.toFixed(2));
      });

      // console.log("work");
      //   LastPrice = $('#field_text-5289327285').val();
      //   var ratepr = LastPrice * exchangrate;
      //   $('#field_text-5329502930').val(ratepr.toFixed(2))
      })(jQuery);

  </script>
  <?php

}


add_action( 'wp_footer','hatem_singel_product_show_ref_for_Aff' );

function hatem_singel_product_show_ref_for_Aff(){

  $refvalue="لا يوجد نسبة";

  $user_id = wp_get_current_user() -> ID;

  $isAff = Hatem_is_affi($user_id);

  if (!($isAff)){
    return ;
  }

  if( is_product()){
          global $post;
          $current_product_ID = $post->ID;
          $product = wc_get_product($current_product_ID);
          $productPrice = $product -> price;


          if ($product ==""){
            return;
          }
          $meta_data = $product->get_meta_data();
          if ( count( $meta_data ) ) {
            foreach ( $meta_data as $meta ) {
              $meta_key   = apply_filters( 'woocommerce_product_export_meta_key', $meta->key, $meta, $product );
              $meta_value = apply_filters( 'woocommerce_product_export_meta_value', $meta->value, $meta, $product );
              if ($meta_key == 'uap-woo-wsr-value'){
                $refvalue =  $meta_value;
              }
            }
          }
          if (is_numeric($refvalue)){
          $FI_Price =   $productPrice * ($refvalue / 100); //نسبة الهتتاخد للفاي
            $refvalue = $FI_Price . '$';
          }
  }



  ?>
  <script>
    try{
    (function($){

    $('.product-main .social-icons').after(`<div class='Hatem-show-ref'><h2>نسبة ربحك من هذا المنتج هي <?php echo $refvalue ;?> <h2></div>`);
    })(jQuery)
  }catch(e){

  }
  </script>



<?php
}


//Function to Change ALl Affiliate/s ect to FI


add_action( 'wp_footer','Hatem_change_Aff_to_fi');

function Hatem_change_Aff_to_fi(){

  ?>

  <script>

  (function($){

    const FI = 'FI';


    let word1 = $('.uap-account-page-top-mess .uap-user-page-mess').remove();
    $('.uap-account-help-link').text(`You can learn more about ${FI} program and to start earning referrals`)



    if(location.href == 'https://sawyancom.com/my-account-2/?uap_aff_subtab=help'){


    $('.uap-ap-wrap').html(`

    <h4>How does the ${FI} program work?</h4>

    The ${FI} program handles the ${FI} accounts by tracking which ${FI} have referred visitors to the website, and rewards them with a specific
    <strong>commission&nbsp;</strong>
    based on what the referred visitor did on the website (purchases, sign ups, etc).

    ${FI} use the ${FI} referral link/URL to promote current website or products. Specific ${FI} are tracked because their IDs or usernames are appended to their URL, therefore the system can track which ${FI} link brought a customer to your website. If the customer successfully completes a conversion (i.e. a sale, or a form submission), a referral will be generated and the ${FI} will be awarded a commission.


    `)

    }

    let worddd = $('#uap_public_ap_marketing li:first-child a').text(`${FI} Links`);



    if(location.href == 'https://sawyancom.com/my-account-2/?uap_aff_subtab=affiliate_link'){


    $('h3').first().text(`${FI} Links`);


    $('.uap-account-affiliatelinks-tab2').first().remove();

    $('.uap-account-link-generator.uap-account-affiliatelinks-tab5 p').html(`
    <p>
    If you'd prefer to append your own ${FI} links with an alternate incoming URL, use the following structure. To build your link, take the following URL and append it with the Alternate Incoming URL you want to use.

    </p>

    `);

    $('.uap-ap-wrap.uap-js-list-affiliate-links-wrapp .uap-profile-box-title span').text(`Generated ${FI} Links`);


    $('.uap-account-table th:nth-child(2)').text(`${FI} Link`);

    }


    if(location.href == 'https://sawyancom.com/my-account-2/?uap_aff_subtab=campaigns'){

      $('.uap-col-xs-8').html(`
      Campaigns will help you to better promote your marketing strategy. Those are private and individual for each ${FI} account.
      `);

    }

    if(location.href == 'https://sawyancom.com/my-account-2/?uap_aff_subtab=visits'){
      $('.uap-account-no-box-inside .uap-subnote').html(`How many times your ${FI} link have been used`);
    }


    if(location.href == 'https://sawyancom.com/my-account-2/?uap_aff_subtab=campaign_reports'){
      $('.uap-account-detault-message').html(`

      You have no campaign into your ${FI} account. Please create one here
      <a href="https://sawyancom.com/my-account-2/?uap_aff_subtab=campaigns">here</a>
      `);
    }

  })(jQuery);


  </script>

  <?php




}


add_action( "wp_footer","Hatem_pop_for_users_to_sign_in" );

function Hatem_pop_for_users_to_sign_in(){


  if (! is_user_logged_in()){

      ?>
  <style>


    .hatem-popup-container {
    visibility: hidden;
    opacity: 0;
    transition: all 0.3s ease-in-out;
    transform: scale(1.3);
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(21, 17, 17, 0.61);
    display: flex;
    align-items: center;
}
.popup-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    border-radius: 10px;
}
.popup-content p{
    font-size: 17px;
    padding: 10px;
    line-height: 20px;
    color: #082241;
    font-weight: bold;
    line-height: 1.6;
}

.popup-content a.close{
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    background: none;
    padding: 0;
    margin: 0;
    text-decoration:none;
}

.popup-content a.close:hover{
  color:#333;
}

.popup-content button:hover,
.popup-content button:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.hatem-popup-container{

  opacity: 1;
  transform: scale(1);
  z-index: 100;
}

.hatem-popup-container h3{
  margin:10px;
}


  </style>

  <script>
    (function($){

      if (location.href !== "https://sawyancom.com/my-account/"){
        $('#popup2').remove();
    //     setTimeout(function(){
    //     $('#popup2').css('visibility','visible');
    //     $( "#popup2-close" ).on( "click", function() {
    //     $('#popup2').remove();

    // });
    //   },2000);
      }









    })(jQuery);
  </script>

  <div id="popup2" class="hatem-popup-container popup-style-2">
    <div class="popup-content">
      <button id="popup2-close">x</button>
      <p>سجل في ثواني و احصل علي خصم مدي الحياة علي جميع المنتجات التركية </p>
      <a href="https://sawyancom.com/my-account/" style="color:#082241;">اضغط هنا لتسجيل الدخول </a>
    </div>
  </div>


  <?php

  }

}



add_action( "wp_footer","Hatem_FI_own_ref_value" );


function Hatem_FI_own_ref_value(){
  $userID =  get_current_user_id();

  global $wpdb;
  $isAff = Hatem_is_affi($userID);

  if ($isAff){
    $AffID =  Hatem_get_affi($userID);

    // echo 'userID=> '. $userID .'<br>';
    // echo 'AffID => '. $AffID .'<br>';


    global $wpdb;

    $results = $wpdb ->get_results("SELECT * FROM `wp40_uap_affiliate_referral_users_relations` WHERE (affiliate_id = $AffID AND referral_wp_uid = $userID)");

    // print_r($results[0]->affiliate_id);
    // echo '<br>';

    if ($results[0]->affiliate_id !== $AffID){
      $table_name = "wp40_uap_affiliate_referral_users_relations";
      $wpdb ->insert($table_name,array(
        "affiliate_id"=> $AffID,
        "referral_wp_uid"=>$userID,
      ));
      // echo"WORK";
    }


  }


}


add_action( "wp_footer","Hatem_change_users_AFF_Role" );


function Hatem_change_users_AFF_Role(){

  // print_r($user->roles[0]);

  global $wpdb;
  $tabel_name_Aff = "wp40_uap_affiliates";
  $results = $wpdb->get_results("SELECT * FROM $tabel_name_Aff");

  foreach ($results as $result) {
    $user = get_user_by( 'id', $result ->uid );
    if ($user->roles[0] != 'g1'){
    $user->remove_role($user->roles[0]);
    $user->add_role( 'g1' );
    }
  }



  //Change Aff g2 Role;

    $table_mlm_name = "wp40_uap_mlm_relations";

    $results_mlm = $wpdb->get_results("SELECT * FROM $table_mlm_name");

    foreach ($results_mlm as $result_mlm) {
      $user_wp_ID = $wpdb->get_results("SELECT * FROM $tabel_name_Aff where (id = $result_mlm->affiliate_id) ");
      $user2 = get_user_by( 'id', $user_wp_ID[0]->uid );
      if ($user2->roles[0] != 'g2'){
      $user2->remove_role($user2->roles[0]);
      $user2->add_role( 'g2' );
    }

    }

}





