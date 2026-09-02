$('.select2').select2();
$('#coupon_type').change(function(){
   couponType();
})

function couponType(){
     if($("#coupon_type").val() == 'private'){
        $('.user_type').hide();
        $('.customer_name').show();
    } else {
        $('.user_type').show();
        $('.customer_name').hide();
    }
}
$('#is_unlimited').change(function(){
   unlimited();
})

unlimited();
function unlimited(){
     if($("#is_unlimited").val() == '1'){
        $('.available_coupons').hide();
    } else {
        $('.available_coupons').show();
    }
}
$('#category').change(function(){
   getSubCategory();
})

function getSubCategory(){
    var cat_id = $("#category").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        url: subCategoryRoute,
        data: { cat_id: cat_id },
        success: function (response) {
            $('#sub_category').html('<option value="0">All</option>')
            .select2({
                data: $.map(response.data, function (item) {
                    return {
                        text: item.name,
                        id: item.id,
                    }
                }),
            });
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}


