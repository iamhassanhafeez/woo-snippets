<script>
    //send wishlist id in elementor form
    let wid = document.getElementById("yith-wcwl-form").action; document.getElementById("form-field-wishlist_url").value=wid;
    
    
//======== Place accordion inside
let accordion = document.getElementById("form-accordion");  let contAccordion = document.querySelector("#place-accordion");  contAccordion.append(accordion);

//===== Place form fields under accordion step2
let accStep1 = document.querySelector("#step1");
let accStep2 = document.querySelector("#elementor-tab-content-2642");
let accStep3 = document.querySelector("#step3");

let fname = document.querySelector(".elementor-field-group-fname");  
let lname = document.querySelector(".elementor-field-group-lname");
let email = document.querySelector(".elementor-field-group-email");
let phone = document.querySelector(".elementor-field-group-phone");
let company_name = document.querySelector(".elementor-field-group-company_name");
let consent1 = document.querySelector(".elementor-field-group-consent1");
let consent2 = document.querySelector(".elementor-field-group-consent2");
let move3 = document.querySelector(".elementor-field-group-field_16f765f");
let move2 = document.querySelector(".elementor-field-group-field_f4ddadc");
let artwork = document.querySelector(".elementor-field-group-upload_artwork");
let submit = document.querySelector(".elementor-field-type-submit");
let skip_artwork = document.querySelector(".elementor-field-group-skip_artwork");
let wishlist_prods = document.querySelector(".elementor-field-group-wishlist_prods");


//start appending
accStep1.append(move2);

accStep2.append(fname, lname, email, phone, company_name, consent1, consent2, move3, wishlist_prods);

accStep3.append(artwork, skip_artwork, submit);

    
    
    
    
    
//   Click next button to expand accordion  
//      jQuery(document).ready(function($){$(document).on('click', '.elementor-field-group-field_f4ddadc', function(e) {
//     $("#elementor-tab-title-2642").trigger('click');
// });});

//   Click next button to expand accordion  
//      jQuery(document).ready(function($){$(document).on('click', '.elementor-field-group-field_16f765f', function(e) {
//     $("#elementor-tab-title-2643").trigger('click');
// });});
      
//======== handle Products       
let container = document.getElementById("step1");
let contChild = container.childNodes[0];

//=======Actor=========
document.querySelectorAll(".item-wrapper").forEach((element) =>
  element.addEventListener("click", (e) => {
    let itemTitle =
      e.target.lastChild.previousSibling.childNodes[1].childNodes[1]
        .childNodes[0].data;

    let itemThumb =
      e.target.firstChild.nextSibling.childNodes[1].childNodes[1].attributes[2]
        .value;
    let uid = e.target.parentNode.getAttribute("data-row-id");
    //================ Append element ===========
    let row_html = document.createElement("div");
    row_html.classList.add("prod-row");
    row_html.setAttribute("data-uid", uid);
    row_html.innerHTML =
      '<div class="col-first">' +
      '<div class="prod-thumb"><img src="' +
      itemThumb +
      '"></div>' +
      '<div class="prod-title" id="prod-' +
      uid +
      '">' +
      itemTitle +
      '</div></div><div class="prod-qty"><span class="minus" id="minus-' +
      uid +
      '">-</span><input min="0" value="1" class="qty-number" type="number" name="quantity-' +
      uid +
      '" id="quantity-' +
      uid +
      '"><span class="plus" id="plus-' +
      uid +
      '">+</span></div>';
    container.insertBefore(row_html, contChild);
    //===Hide add all and show remove all
    showrmAll();
    initQty();
  //deactivated for now  initTrash();
    initIncDec(uid);
  })
);

function showrmAll(){
    document.querySelector('.rm-all').style.display = 'block';
    document.querySelector('.add-all').style.display = 'none';
    document.querySelector('.fallback-text').style.display = 'none';
    
}
//====== Run Loop on input change

function initQty() {
  jQuery(document).ready(function ($) {
    $(".qty-number").on("change", function () {
      let values = [];
      // console.log("cheeta");
      document.querySelectorAll(".prod-row").forEach((row) => {
        let uid = row.getAttribute("data-uid");
        let postTitle = document.getElementById("prod-" + uid).innerText;
        let postQty = document.getElementById("quantity-" + uid).value;
        values.push(runPosting(uid, postTitle, postQty, values));
        //console.log(uid + postTitle + postQty + "\n" + values);
      });
      let targField = document.getElementById("form-field-wishlist_prods");
      let finalValues = values.toString().replaceAll(",", " - ");
      targField.value = finalValues;
    });
    
  $(".plus, .minus").on("mouseout", function () {
      let values = [];
      // console.log("cheeta");
      document.querySelectorAll(".prod-row").forEach((row) => {
        let uid = row.getAttribute("data-uid");
        let postTitle = document.getElementById("prod-" + uid).innerText;
        let postQty = document.getElementById("quantity-" + uid).value;
        values.push(runPosting(uid, postTitle, postQty, values));
        //console.log(uid + postTitle + postQty + "\n" + values);
      });
      let targField = document.getElementById("form-field-wishlist_prods");
      let finalValues = values.toString().replaceAll(",", " - ");
      targField.value = finalValues;
    });    
    
  });
}
function runPosting(uid, postTitle, postQty, values) {
  return "Prod ID:" + uid + " Title:" + postTitle + " Quantity:" + postQty;
}

function initTrash() {
  jQuery(document).ready(function ($) {
    $(".trash-prod").on("click", function (e) {
      e.preventDefault();
      var parentRow = $(this).closest(".prod-row");
      parentRow.remove();
    });
  });
}

function initIncDec(uid) {
  jQuery(document).ready(function ($) {
    //====== Launch Increment =====
    $("#plus-" + uid).on("click", function() {
      var qtyField = $("#quantity-" + uid);
     var qtyVal = qtyField.val();
      qtyVal++;
     qtyField.val(qtyVal);
      console.log(qtyField.val());
    });
    
    //======== Launch decrement =====
     $("#minus-" + uid).on("click", function() {
      var qtyField = $("#quantity-" + uid);
     var qtyVal = qtyField.val();
     if (qtyVal != 0){
      qtyVal--;
     qtyField.val(qtyVal);}
      console.log(qtyField.val());
    });
  });
}

//   Click next button to expand accordion  
     jQuery(document).ready(function($){
         $(document).on('click', '.add-all', function(e) {
    $(".item-wrapper").trigger('click');
});
});
//========Remove all on click =========
jQuery(document).ready(function ($) {
  $(".rm-all").on("click", function () {
    var parentRow = $(".prod-row");
    parentRow.remove();
    $('.rm-all').css('display', 'none');
    $('.add-all').css('display', 'block');
    $('.fallback-text').css('display', 'block');
  });
});


</script>